<?php
session_start();

// 数据库配置
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin";

// 连接数据库
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// 获取参数
$year = $_SESSION['selected_year'] ?? 'Year 1';
$subject_id = $_SESSION['selected_subject_id'] ?? 11245;

// 查询有 class_id 的课程（非空）
$sql = "SELECT * FROM admin_class 
        WHERE year = ? 
        AND subject_id = ? 
        AND class_id != '' 
        AND part IN ('Part A', 'Part B')";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "Prepare failed: " . $conn->error]));
}

$stmt->bind_param("si", $year, $subject_id);
$stmt->execute();

$result = $stmt->get_result();
$classes = $result->fetch_all(MYSQLI_ASSOC);

// 返回JSON数据
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "data" => $classes,
    "count" => count($classes)
]);

$stmt->close();
$conn->close();
?>