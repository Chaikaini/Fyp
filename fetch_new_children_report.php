<?php
include 'db.php';

// 确保 `$conn` 可用
global $conn;

// 获取参数
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// 如果 `start_date` 或 `end_date` 为空，则查询所有数据
if (empty($start_date) || empty($end_date)) {
    $sql = "SELECT id, name, gender, kidNumber, birthday FROM childreninfo";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT id, name, gender, kidNumber, birthday FROM childreninfo 
            WHERE birthday BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
}

if (!$stmt) {
    die(json_encode(["error" => "SQL Error: " . $conn->error]));
}

$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

// 返回 JSON 数据
echo json_encode($children);
?>
