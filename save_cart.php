<?php
header('Content-Type: application/json');
session_start();

// 检查用户是否登录
if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

include 'db.php';

// 获取POST数据
$data = json_decode(file_get_contents('php://input'), true);

// 验证必要字段
$requiredFields = ['subject_id', 'subject_name', 'price', 'child_name', 'teacher'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
        exit();
    }
}

// 准备SQL语句
$sql = "INSERT INTO cart_items 
        (parent_id, subject_id, subject_name, price, child_name, child_year, image, teacher, class_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("iissssssi", 
    $_SESSION['parent_id'],
    $data['subject_id'],
    $data['subject_name'],
    $data['price'],
    $data['child_name'],
    $data['child_year'] ?? null,
    $data['image'],
    $data['teacher'],
    $data['class_id'] ?? null
);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'id' => $stmt->insert_id,
        'message' => 'Item added to cart'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>