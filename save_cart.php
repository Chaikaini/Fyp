<?php
header('Content-Type: application/json');
session_start();

// 1. 检查登录
if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['status' => 'error', 'message' => '请先登录']);
    exit();
}

// 2. 数据库连接
$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => '数据库连接失败']);
    exit();
}

// 3. 获取输入数据
$data = json_decode(file_get_contents('php://input'), true);

// 4. 验证数据
if (empty($data['subject_id']) || empty($data['child_name'])) {
    echo json_encode(['status' => 'error', 'message' => '缺少必要数据']);
    exit();
}

// 5. 插入数据
$sql = "INSERT INTO cart_items 
       (parent_id, subject_id, subject_name, price, child_name, teacher) 
       VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iissss",
    $_SESSION['parent_id'],
    $data['subject_id'],
    $data['subject_name'] ?? '',
    $data['price'] ?? 0,
    $data['child_name'],
    $data['teacher'] ?? ''
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => '添加失败: '.$stmt->error]);
}

$stmt->close();
$conn->close();
?>