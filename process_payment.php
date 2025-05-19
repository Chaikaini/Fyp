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
$subjectTotal = 0;

// Validate cart_items against cart_ids
$cart_item_ids = array_map(function($item) { return $item['cart_id']; }, $cart_items);
if (array_diff($cart_ids, $cart_item_ids) || array_diff($cart_item_ids, $cart_ids)) {
    error_log("Cart items do not match cart_ids: cart_ids=" . implode(',', $cart_ids) . ", cart_item_ids=" . implode(',', $cart_item_ids));
    echo json_encode(["success" => false, "message" => "Cart items do not match."]);
    exit;
}

// Calculate subject total and validate items
foreach ($cart_items as $item) {
    if (!isset($item['subject_id'], $item['class_id'], $item['child_id'])) {
        error_log("Invalid cart item: missing required fields");
        echo json_encode(["success" => false, "message" => "Invalid cart item data."]);
        exit;
    }
    $priceStmt = $conn->prepare("SELECT subject_price FROM subject WHERE subject_id = ?");
    $priceStmt->bind_param("i", $item["subject_id"]);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    if ($priceResult->num_rows > 0) {
        $subjectTotal += $priceResult->fetch_assoc()['subject_price'];
    } else {
        error_log("Subject price not found for subject_id=" . $item["subject_id"]);
        echo json_encode(["success" => false, "message" => "Invalid subject ID: " . $item["subject_id"]]);
        exit;
    }
    $priceStmt->close();
}

// Include the enrollment fee in the total amount
$enrollmentFee = 100.00; // Always RM100 enrollment fee
$total_amount = $subjectTotal + $enrollmentFee;

$payment_method = $conn->real_escape_string($data["payment_method"] ?? "");
$phone = isset($data["phone"]) ? $conn->real_escape_string($data["phone"]) : null;
$card_details = isset($data["card_details"]) ? $data["card_details"] : null;

// Validate data
if (empty($cart_items) || $total_amount <= 0 || empty($payment_method)) {
    error_log("Invalid data: cart_items=" . count($cart_items) . ", total_amount=$total_amount, payment_method=$payment_method");
    echo json_encode(["success" => false, "message" => "Invalid payment data."]);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Initialize credit_card_id
    $credit_card_id = null;

    // Save credit card if provided and save_card is true
    if ($card_details && isset($card_details['save_card']) && $card_details['save_card'] && $payment_method === "Credit Card") {
        $card_number = password_hash($card_details['card_number'], PASSWORD_DEFAULT);
        $expiry_date = $conn->real_escape_string($card_details['expiry_date']);
        $last_four = substr($card_details['card_number'], -4);

        // Delete existing card for this parent
        $stmt = $conn->prepare("DELETE FROM credit_cards WHERE parent_id = ?");
        $stmt->bind_param("i", $parent_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to delete existing credit card: " . $stmt->error);
        }

        // Insert new card
        $stmt = $conn->prepare("INSERT INTO credit_cards (parent_id, card_number, expiry_date, last_four) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $parent_id, $card_number, $expiry_date, $last_four);
        if (!$stmt->execute()) {
            throw new Exception("Failed to save credit card: " . $stmt->error);
        }
        $credit_card_id = $conn->insert_id;
    } elseif ($payment_method === "Credit Card") {
        // Check if a saved card exists for this parent
        $stmt = $conn->prepare("SELECT credit_card_id FROM credit_cards WHERE parent_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $credit_card_id = $row['credit_card_id'];
        } else {
            error_log("No saved credit card found for parent_id=$parent_id");
            throw new Exception("No saved credit card found.");
        }
    }

    // Insert payment record (only includes parent_id, payment_total_amount, payment_method, credit_card_id, payment_time)
    $stmt = $conn->prepare("
        INSERT INTO payment (payment_id, payment_total_amount, payment_method, credit_card_id, payment_time)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("idsi", $payment_id, $total_amount, $payment_method, $credit_card_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert payment: " . $stmt->error);
    }
    $payment_id = $conn->insert_id;

    // Update class_enrolled in class table
    foreach ($cart_items as $item) {
        $class_id = $conn->real_escape_string($item['class_id']);
        $stmt = $conn->prepare("UPDATE class SET class_enrolled = class_enrolled + 1 WHERE class_id = ?");
        $stmt->bind_param("s", $class_id);
        if (!$stmt->execute()) {
            error_log("Failed to update class_enrolled: " . $stmt->error . " for class_id=$class_id");
            throw new Exception("Failed to update class_enrolled: " . $stmt->error);
        }
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
            throw new Exception("Failed to insert registration_class: " . $stmt->error);
        }
    }

    // Mark cart items as deleted
    if (!empty($cart_ids)) {
        $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
        $stmt = $conn->prepare("UPDATE cart SET deleted = 1 WHERE cart_id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($cart_ids)), ...$cart_ids);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update cart: " . $stmt->error);
        }
    }

    // Commit transaction
    $conn->commit();
    error_log("Payment successful: payment_id=$payment_id, parent_id=$parent_id, total_amount=$total_amount");
    echo json_encode(["success" => true, "payment_id" => "PAY" . str_pad($payment_id, 4, "0", STR_PAD_LEFT)]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Payment processing error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Payment processing failed: " . $e->getMessage()]);
} finally {
    $conn->close();
}
?>