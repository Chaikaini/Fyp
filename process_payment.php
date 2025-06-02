<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    error_log("Invalid request: No data received");
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

// Get parent_id
$parent_id = isset($_SESSION["parent_id"]) ? (int)$_SESSION["parent_id"] : 0;
if (!$parent_id) {
    error_log("No parent_id in session");
    echo json_encode(["success" => false, "message" => "User not authenticated."]);
    exit;
}

// Get data from the request
$cart_items = $data["cart_items"] ?? [];
$cart_ids = isset($data["cart_ids"]) ? explode(',', $data["cart_ids"]) : [];
$subject_total = isset($data["subject_total"]) ? (float)$data["subject_total"] : 0;
$enrollment_fee = isset($data["enrollment_fee"]) ? (float)$data["enrollment_fee"] : 100.00;
$total_amount = isset($data["total_amount"]) ? (float)$data["total_amount"] : 0;
$payment_method = isset($data["payment_method"]) ? $conn->real_escape_string($data["payment_method"]) : "";
$phone = isset($data["phone"]) ? $conn->real_escape_string($data["phone"]) : null;
$card_details = isset($data["card_details"]) ? $data["card_details"] : null;

// Validate data
if (empty($cart_items) || $total_amount <= 0 || $payment_method !== "Credit Card" || !$card_details) {
    error_log("Invalid data: cart_items=" . count($cart_items) . ", total_amount=$total_amount, payment_method=$payment_method");
    echo json_encode(["success" => false, "message" => "Invalid payment data."]);
    exit;
}

// Validate cart_items against cart_ids
$cart_item_ids = array_map(function($item) { return $item['cart_id']; }, $cart_items);
if (array_diff($cart_ids, $cart_item_ids) || array_diff($cart_item_ids, $cart_ids)) {
    error_log("Cart items do not match cart_ids: cart_ids=" . implode(',', $cart_ids) . ", cart_item_ids=" . implode(',', $cart_item_ids));
    echo json_encode(["success" => false, "message" => "Cart items do not match."]);
    exit;
}

// Re-validate subject total
$calculated_total = 0;
foreach ($cart_items as $item) {
    if (!isset($item['subject_id'], $item['class_id'], $item['child_id'])) {
        error_log("Invalid cart item: missing required fields");
        echo json_encode(["success" => false, "message" => "Invalid cart item data."]);
        exit;
    }
    $price_stmt = $conn->prepare("SELECT subject_price FROM subject WHERE subject_id = ?");
    $price_stmt->bind_param("i", $item["subject_id"]);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();
    if ($price_result->num_rows > 0) {
        $calculated_total += $price_result->fetch_assoc()['subject_price'];
    } else {
        error_log("Subject price not found for subject_id=" . $item["subject_id"]);
        echo json_encode(["success" => false, "message" => "Invalid subject ID: " . $item["subject_id"]]);
        exit;
    }
    $price_stmt->close();
}
$calculated_total += $enrollment_fee;
if (abs($calculated_total - $total_amount) > 0.01) {
    error_log("Total amount mismatch: calculated=$calculated_total, provided=$total_amount");
    echo json_encode(["success" => false, "message" => "Total amount verification failed."]);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Initialize credit_card_id
    $credit_card_id = null;

    // Handle credit card selection or new card
    if (isset($card_details['credit_card_id'])) {
        // Use existing card
        $credit_card_id = (int)$card_details['credit_card_id'];
        $stmt = $conn->prepare("SELECT credit_card_id FROM credit_cards WHERE credit_card_id = ? AND parent_id = ?");
        $stmt->bind_param("ii", $credit_card_id, $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Selected credit card is invalid or does not belong to this user.");
        }
        $stmt->close();
    } elseif (isset($card_details['card_number'], $card_details['expiry_date'], $card_details['cvv'])) {
        // Process new card
        $card_number_raw = $card_details['card_number'];
        $expiry_date = $conn->real_escape_string($card_details['expiry_date']);
        $cvv = $card_details['cvv'];

        // Validate new card details
        if (strlen($card_number_raw) !== 16 || !preg_match('/^\d+$/', $card_number_raw)) {
            throw new Exception("Invalid card number.");
        }
        if (!preg_match('/^\d{2}\/\d{2}$/', $expiry_date)) {
            throw new Exception("Invalid expiry date format.");
        }
        if (!preg_match('/^\d{3}$/', $cvv)) {
            throw new Exception("Invalid CVV.");
        }

        if (isset($card_details['save_card']) && $card_details['save_card']) {
            // Save the new card
            $card_number_hashed = password_hash($card_number_raw, PASSWORD_DEFAULT);
            $last_four = substr($card_number_raw, -4);

            $stmt = $conn->prepare("INSERT INTO credit_cards (parent_id, card_number, expiry_date, last_four, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isss", $parent_id, $card_number_hashed, $expiry_date, $last_four);
            if (!$stmt->execute()) {
                throw new Exception("Failed to save credit card: " . $stmt->error);
            }
            $credit_card_id = $conn->insert_id;
            $stmt->close();
        } else {
            // Use card without saving (credit_card_id remains null)
            // Note: If payment.credit_card_id is NOT NULL, we must save the card
            $card_number_hashed = password_hash($card_number_raw, PASSWORD_DEFAULT);
            $last_four = substr($card_number_raw, -4);
            $stmt = $conn->prepare("INSERT INTO credit_cards (parent_id, card_number, expiry_date, last_four, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isss", $parent_id, $card_number_hashed, $expiry_date, $last_four);
            if (!$stmt->execute()) {
                throw new Exception("Failed to save temporary credit card: " . $stmt->error);
            }
            $credit_card_id = $conn->insert_id;
            $stmt->close();
        }
    } else {
        throw new Exception("Incomplete credit card information.");
    }

    // Insert payment record
    $stmt = $conn->prepare("
        INSERT INTO payment (payment_total_amount, payment_method, credit_card_id, payment_time)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("dsi", $total_amount, $payment_method, $credit_card_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert payment record: " . $stmt->error);
    }
    $payment_id = $conn->insert_id;
    $stmt->close();

    // Update class_enrolled in class table
    foreach ($cart_items as $item) {
        $class_id = $conn->real_escape_string($item['class_id']);
        $stmt = $conn->prepare("UPDATE class SET class_enrolled = class_enrolled + 1 WHERE class_id = ?");
        $stmt->bind_param("s", $class_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update class enrollment: " . $stmt->error);
        }
        $stmt->close();
    }

    // Insert registration_class records
    foreach ($cart_items as $item) {
        $class_id = $conn->real_escape_string($item["class_id"]);
        $child_id = (int)$item["child_id"];
        $stmt = $conn->prepare("
            INSERT INTO registration_class (parent_id, class_id, child_id, payment_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isii", $parent_id, $class_id, $child_id, $payment_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert registration record: " . $stmt->error);
        }
        $stmt->close();
    }

    // Mark cart items as deleted
    if (!empty($cart_ids)) {
        $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
        $stmt = $conn->prepare("UPDATE cart SET deleted = 1 WHERE cart_id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($cart_ids)), ...$cart_ids);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update cart: " . $stmt->error);
        }
        $stmt->close();
    }

    // Commit transaction
    $conn->commit();
    error_log("Payment successful: payment_id=$payment_id, parent_id=$parent_id, total_amount=$total_amount, credit_card_id=$credit_card_id");
    echo json_encode(["success" => true, "payment_id" => "PAY" . str_pad($payment_id, 4, "0", STR_PAD_LEFT)]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Payment processing error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Payment processing failed: " . $e->getMessage()]);
} finally {
    $conn->close();
}
?>