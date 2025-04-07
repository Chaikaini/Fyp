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

$cart = $data['cart'] ?? [];

// 如果购物车数据有效
if (!empty($cart)) {
    foreach ($cart as $item) {
        $subject = $item['subject'] ?? '';
        $price = $item['price'] ?? 0;
        $child = $item['child'] ?? '';
        $image = $item['image'] ?? ''; // 获取图片路径
        $teacher = $item['teacher'] ?? ''; // 获取教师名称
        $time = $item['time'] ?? ''; // 获取课程时间
        
        // 检查该商品是否已经存在于购物车中
        $stmt = $conn->prepare("SELECT id FROM cart_items WHERE subject = ? AND child = ? AND teacher = ? AND time = ?");
        $stmt->bind_param("ssss", $subject, $child, $teacher, $time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // 商品已经存在
            echo json_encode(['status' => 'error', 'message' => 'This item is already in the cart.']);
            exit;
        }
        
        // 插入数据到数据库
        $stmt = $conn->prepare("INSERT INTO cart_items (subject, price, child, image, teacher, time) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssss", $subject, $price, $child, $image, $teacher, $time);
        
        // 执行插入操作
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save cart item']);
            exit;
        }
    }
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid cart data']);
}

$conn->close();
?>
