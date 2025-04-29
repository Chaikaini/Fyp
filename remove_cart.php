<?php
session_start();
include('db_connect.php'); // 确保连接数据库

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1); // 显示所有错误

$conn = dbConnect();

// 获取请求体中的原始 JSON 数据
$json = file_get_contents('php://input');

// 调试：检查是否有数据接收到
if (empty($json)) {
    echo json_encode(['status' => 'error', 'message' => 'No data received', 'raw' => $json]);
    exit;
}

// 解析 JSON 数据
$data = json_decode($json, true);

// 调试：检查解析后的数据
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input', 'raw' => $json]);
    exit;
}

$id = $data['id'] ?? null;

// 如果没有提供 id 或 id 无效
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'No valid item ID received']);
    exit;
}

// 删除购物车项
$stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
$stmt->bind_param("i", $id); // 假设 id 是整数类型

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete cart item']);
}

$conn->close();
?>
