<?php
$servername = "127.0.0.1";
$username = "root";
$password = ""; 
$dbname = "the seeds";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否出错
if ($conn->connect_error) {
    // 在生产环境中应该记录到日志而不是直接输出
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// 设置字符集
$conn->set_charset("utf8mb4");
?>