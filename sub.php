<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "subjects"; // 确保数据库名称正确

$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
?>

