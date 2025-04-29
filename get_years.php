<?php
header('Content-Type: application/json');
include 'db_connect.php';

// 查询所有唯一的 year 值
$sql = "SELECT DISTINCT year FROM subject ORDER BY year ASC";
$result = $conn->query($sql);

$years = [];
while ($row = $result->fetch_assoc()) {
    $years[] = $row['year'];
}

echo json_encode($years);

// 清理
$result->free();
$conn->close();
?>