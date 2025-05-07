<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "Connection failed: " . $conn->connect_error
    ]);
    exit;
}

// 查询课程，只取 Part A 和 B（part_id = 1 或 2）
$sql = "SELECT * FROM class WHERE part_id IN (1, 2)";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "error" => "Query failed: " . $conn->error
    ]);
    exit;
}

$allClasses = $result->fetch_all(MYSQLI_ASSOC);

// 初始化 Part A 和 Part B 数组
$partA = [];
$partB = [];

// 将所有 Part A 和 Part B 的数据分别存入数组
foreach ($allClasses as $class) {
    if ($class['part_id'] == 1) {
        $partA[] = $class;
    } elseif ($class['part_id'] == 2) {
        $partB[] = $class;
    }
}

// 返回所有 Part A 和 Part B 数据
$response = [
    "success" => true,
    "data" => [
        "partA" => $partA,
        "partB" => $partB
    ]
];

echo json_encode($response);
$conn->close();
?>
