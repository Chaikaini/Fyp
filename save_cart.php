<?php
session_start();
header('Content-Type: application/json');
require_once('db_connect.php');

try {
    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON", 400);
    }

    $required = ['subject_id', 'child_name', 'price', 'class_id', 'teacher_id'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing field: " . $field, 400);
        }
    }

    $child_name = $data['child_name'];
    $child_id = null;

    if (!empty($child_name)) {
        $stmt = $conn->prepare("SELECT child_id FROM child WHERE child_name = ?");
        $stmt->bind_param("s", $child_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $child_id = $row['child_id'];
        } else {
            throw new Exception("Child not found", 404);
        }
    }

    // 验证 class_id 是否存在
    $stmt = $conn->prepare("SELECT class_id FROM class WHERE class_id = ?");
    $stmt->bind_param("s", $data['class_id']);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        throw new Exception("Class not found", 404);
    }

    // 验证 teacher_id 是否存在
    $teacher_id = $data['teacher_id'];
    $stmt = $conn->prepare("SELECT teacher_id FROM teacher WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id);  
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        throw new Exception("Teacher not found", 404);
    }

    // 检查是否已经存在相同记录（parent_id, child_name, subject_id）
    $stmt = $conn->prepare("SELECT cart_id, deleted FROM cart WHERE parent_id = ? AND child_name = ? AND subject_id = ?");
    $stmt->bind_param("isi", $_SESSION['parent_id'], $child_name, $data['subject_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['deleted'] == 0) {
            throw new Exception("This subject is already in the cart", 400);
        } else {
            // 已软删除，恢复它
            $stmt = $conn->prepare("UPDATE cart SET deleted = 0 WHERE cart_id = ?");
            $stmt->bind_param("i", $row['cart_id']);
            $stmt->execute();

            echo json_encode([
                'status' => 'success',
                'message' => 'Cart item restored successfully'
            ]);
            exit;
        }
    }

    // 插入新记录
    $stmt = $conn->prepare("INSERT INTO cart (parent_id, subject_id, child_id, child_name, price, class_id, teacher_id, deleted) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("iiisssi", $_SESSION['parent_id'], $data['subject_id'], $child_id, $child_name, $data['price'], $data['class_id'], $teacher_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to add item to cart", 500);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Added to cart successfully'
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
