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

// Check if user has previous enrollments
$hasPreviousEnrollment = false;
$enrollmentCheckStmt = $conn->prepare("SELECT COUNT(*) as count FROM enrollment WHERE parent_id = ?");
$enrollmentCheckStmt->bind_param("i", $parent_id);
$enrollmentCheckStmt->execute();
$result = $enrollmentCheckStmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hasPreviousEnrollment = $row['count'] > 0;
} else {
    $hasPreviousEnrollment = false;
}
$enrollmentCheckStmt->close();
error_log("hasPreviousEnrollment in process_payment.php: " . ($hasPreviousEnrollment ? 'true' : 'false'));

// Get data from the request
$cart_items = $data["cart_items"] ?? [];
$subjectTotal = 0;

// Calculate subject total
foreach ($cart_items as $item) {
    $priceStmt = $conn->prepare("SELECT subject_price FROM subject WHERE subject_id = ?");
    $priceStmt->bind_param("i", $item["subject_id"]);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    if ($priceResult->num_rows > 0) {
        $subjectTotal += $priceResult->fetch_assoc()['subject_price'];
    }
    $priceStmt->close();
}

// Dynamic total amount
$enrollmentFee = $hasPreviousEnrollment ? 0 : 100;
$total_amount = $subjectTotal + $enrollmentFee;

$payment_method = $conn->real_escape_string($data["payment_method"] ?? "");
$phone = isset($data["phone"]) ? $conn->real_escape_string($data["phone"]) : null;
$card_details = isset($data["card_details"]) ? $data["card_details"] : null;

// Validate data
if (empty($cart_items) || $total_amount <= 0 || empty($payment_method)) {
    error_log("Invalid data: cart_items, total_amount, or payment_method missing");
    echo json_encode(["success" => false, "message" => "Invalid payment data."]);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert payment record
    $master_card_number = null;
    if ($payment_method === "Credit Card" && $card_details) {
        $master_card_number = substr($card_details['card_number'], -4);
    }
    $payment_status = "Completed";
    $stmt = $conn->prepare("
        INSERT INTO payment (parent_id, payment_total_amount, payment_method, master_card_number, payment_status, enrollment_fee)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("idsssi", $parent_id, $total_amount, $payment_method, $master_card_number, $payment_status, $enrollmentFee);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert payment: " . $stmt->error);
    }
    $payment_id = $conn->insert_id;

    // Record enrollment if first time
    if (!$hasPreviousEnrollment) {
        foreach ($cart_items as $item) {
            $class_id = $conn->real_escape_string($item['class_id']);
            $child_id = (int)$item['child_id'];
            $stmt = $conn->prepare("INSERT INTO enrollment (parent_id, class_id, child_id) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $parent_id, $class_id, $child_id);
            if (!$stmt->execute()) {
                error_log("Enrollment insert failed: " . $stmt->error . " for parent_id=$parent_id, class_id=$class_id, child_id=$child_id");
                throw new Exception("Failed to record enrollment: " . $stmt->error);
            }
        }
    }

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
        $subject_id = (int)$item["subject_id"];
        $teacher_id = (int)$item["teacher_id"];

        $stmt = $conn->prepare("
            INSERT INTO registration_class (parent_id, class_id, child_id, subject_id, teacher_id, payment_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isiiii", $parent_id, $class_id, $child_id, $subject_id, $teacher_id, $payment_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert registration_class: " . $stmt->error);
        }
    }

    // Save credit card if provided and save_card is true
    if ($card_details && $card_details['save_card'] && $payment_method === "Credit Card") {
        $card_number = password_hash($card_details['card_number'], PASSWORD_DEFAULT);
        $expiry_date = $conn->real_escape_string($card_details['expiry_date']);
        $last_four = substr($card_details['card_number'], -4);

        $stmt = $conn->prepare("DELETE FROM credit_cards WHERE parent_id = ?");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO credit_cards (parent_id, card_number, expiry_date, last_four) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $parent_id, $card_number, $expiry_date, $last_four);
        if (!$stmt->execute()) {
            throw new Exception("Failed to save credit card: " . $stmt->error);
        }
    }

    // Mark cart items as deleted
    if (!empty($data['cart_ids'])) {
        $cart_ids = explode(',', $data['cart_ids']);
        $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
        $stmt = $conn->prepare("UPDATE cart SET deleted = 1 WHERE cart_id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($cart_ids)), ...$cart_ids);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update cart: " . $stmt->error);
        }
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(["success" => true, "payment_id" => "PAY" . str_pad($payment_id, 4, "0", STR_PAD_LEFT)]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Payment processing error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Payment processing failed: " . $e->getMessage()]);
}

$conn->close();
?>