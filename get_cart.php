<?php
include('db_connect.php'); // 确保连接数据库
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = dbConnect();

$sql = "SELECT id, subject, price, child, image, teacher, time, class_id, capacity FROM cart_items";
$result = $conn->query($sql);

$cart = [];
while ($row = $result->fetch_assoc()) {
    $cart[] = $row;
}

$conn->close();

echo json_encode(['status' => 'success', 'cart' => $cart]);
?>
