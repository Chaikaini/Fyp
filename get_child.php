<?php
header('Content-Type: application/json');
session_start();

// 获取当前用户的 parent_id
$parent_id = $_SESSION['parent_id'] ?? '';

if (empty($parent_id)) {
    echo json_encode(["error" => "User is not logged in"]);
    exit();
}

// 引入数据库连接
include 'db.php';

// 根据 parent_id 查找 child 表
$sql = "SELECT child_name, child_year FROM child WHERE parent_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $parent_id);  // parent_id 是 int 类型
$stmt->execute();
$result = $stmt->get_result();

$children = [];

while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

if (empty($children)) {
    echo json_encode(["message" => "No children found for parent_id: $parent_id"]);
} else {
    echo json_encode($children);
}

$stmt->close();
$conn->close();
?>
