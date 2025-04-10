<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin"; // 或你使用的数据库名称

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 获取 session 中保存的选定年级和科目
$year = $_SESSION['selected_year'] ?? 'Year 1';  // 默认为 'Year 1'
$subject_id = $_SESSION['selected_subject_id'] ?? 11245;  // 默认科目 ID

// SQL 查询，获取 Part A 和 Part B
$sql = "SELECT * FROM admin_class 
        WHERE year = ? 
        AND subject_id = ? 
        AND part IN ('Part A', 'Part B')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $year, $subject_id);
$stmt->execute();
$result = $stmt->get_result();

// 存储查询结果
$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

// 返回查询到的课程信息
echo json_encode($classes);

$stmt->close();
$conn->close();
?>
