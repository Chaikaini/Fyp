<?php
header('Content-Type: application/json');
session_start();

// 获取当前用户的 parent_id
$parent_id = $_SESSION['parent_id'] ?? '';

if (empty($parent_id)) {
    echo json_encode(["error" => "User is not logged in"]);
    exit();
}

// 获取请求中的年级参数
$subject_year = isset($_GET['subject_year']) ? (int)$_GET['subject_year'] : null;

// 引入数据库连接
include 'db.php';

// 基础SQL查询
$sql = "SELECT child_name, child_year FROM child WHERE parent_id = ?";

// 如果有年级参数，添加年级筛选条件
if ($subject_year !== null) {
    $sql .= " AND child_year = ?";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
    exit();
}

// 根据是否有年级参数绑定参数
if ($subject_year !== null) {
    $stmt->bind_param("ii", $parent_id, $subject_year);
} else {
    $stmt->bind_param("i", $parent_id);
}

$stmt->execute();
$result = $stmt->get_result();

$children = [];

while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

if (empty($children)) {
    echo json_encode(["message" => "No children found"]);
} else {
    echo json_encode($children);
}

$stmt->close();
$conn->close();
?>