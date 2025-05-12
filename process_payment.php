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

$parent_id = isset($_SESSION["parent_id"]) ? (int)$_SESSION["parent_id"] : 0;
if (!$parent_id) {
    error_log("No parent_id in session");
    echo json_encode(["success" => false, "message" => "User not authenticated."]);
    exit;
}

$hasPreviousEnrollment = false;
$enrollmentCheckStmt = $conn->prepare("SELECT COUNT(*) as count FROM enrollment WHERE parent_id = ?");
$enrollmentCheckStmt->bind_param("i", $parent_id);
$enrollmentCheckStmt->execute();
$result = $enrollmentCheckStmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hasPreviousEnrollment = $row['count'] > 0;
}
$enrollmentCheckStmt->close();
error_log("hasPreviousEnrollment in process_payment.php: " . ($hasPreviousEnrollment ? 'true' : 'false'));

$cart_items = $data["cart_items"] ?? [];
$subjectTotal = 0;

foreach ($cart_items as $item) {
    $priceStmt = $conn->prepare("SELECT subject_price FROM subject WHERE subject_id = ?");
    $priceStmt->bind_param("s", $item["subject_id"]); // 假设 subject_id 是 VARCHAR
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    if ($priceResult->num_rows > 0) {
        $subjectTotal += $priceResult->fetch_assoc()['subject_price'];
    }
    $priceStmt->close();
}

$enrollmentFee = $hasPreviousEnrollment ? 0 : 100;
$total_amount = $subjectTotal + $enrollmentFee;
$payment_method = "Credit Card"; // 固定为信用卡支付
$payment_status = "Completed";

$credit_card_id = null;
$card_details = $data["card_details"] ?? null;
if ($card_details) {
    $card_number = password_hash($card_details['card_number'], PASSWORD_DEFAULT);
    $expiry_date = $conn->real_escape_string($card_details['expiry_date']);
    $last_four = substr($card_details['card_number'], -4);

    // 保存信用卡到 credit_cards 表
    $stmt = $conn->prepare("INSERT INTO credit_cards (parent_id, card_number, expiry_date, last_four) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $parent_id, $card_number, $expiry_date, $last_four);
    $stmt->execute();
    $credit_card_id = $conn->insert_id;

    // 删除旧信用卡（可选，根据需求）
    $stmt = $conn->prepare("DELETE FROM credit_cards WHERE parent_id = ? AND id != ?");
    $stmt->bind_param("ii", $parent_id, $credit_card_id);
    $stmt->execute();
}

$conn->begin_transaction();

try {
    // 插入 payment 记录
    $stmt = $conn->prepare("
        INSERT INTO payment (parent_id, payment_total_amount, payment_method, credit_card_id, payment_status, enrollment_fee)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("idsssi", $parent_id, $total_amount, $payment_method, $credit_card_id, $payment_status, $enrollmentFee);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert payment: " . $stmt->error);
    }
    $payment_id = $conn->insert_id;

    // 其他逻辑（enrollment, registration_class, cart 更新）保持不变
    if (!$hasPreviousEnrollment) {
        foreach ($cart_items as $item) {
            $class_id = $conn->real_escape_string($item['class_id']);
            $child_id = (int)$item['child_id'];
            $stmt = $conn->prepare("INSERT INTO enrollment (parent_id, class_id, child_id) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $parent_id, $class_id, $child_id);
            if (!$stmt->execute()) {
                error_log("Enrollment insert failed: " . $stmt->error);
                throw new Exception("Failed to record enrollment: " . $stmt->error);
            }
        }
    }

    foreach ($cart_items as $item) {
        $class_id = $conn->real_escape_string($item['class_id']);
        $stmt = $conn->prepare("UPDATE class SET class_enrolled = class_enrolled + 1 WHERE class_id = ?");
        $stmt->bind_param("s", $class_id);
        if (!$stmt->execute()) {
            error_log("Failed to update class_enrolled: " . $stmt->error);
            throw new Exception("Failed to update class_enrolled: " . $stmt->error);
        }
    }

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

    if (!empty($data['cart_ids'])) {
        $cart_ids = explode(',', $data['cart_ids']);
        $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
        $stmt = $conn->prepare("UPDATE cart SET deleted = 1 WHERE cart_id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($cart_ids)), ...$cart_ids);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update cart: " . $stmt->error);
        }
    }

    $conn->commit();
    echo json_encode(["success" => true, "payment_id" => "PAY" . str_pad($payment_id, 4, "0", STR_PAD_LEFT)]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Payment processing error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Payment processing failed: " . $e->getMessage()]);
}

$conn->close();
?>