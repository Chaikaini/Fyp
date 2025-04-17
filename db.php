<?php
$servername = "127.0.0.1";
$username = "root";         // 数据库用户名
$password = "";             // 数据库密码（空代表本地默认）
$dbname = "the seeds";      // 数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否出错
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
