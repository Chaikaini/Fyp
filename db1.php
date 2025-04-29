<?php
// db1.php - 数据库连接文件

$db_host = 'localhost';  // 数据库主机
$db_user = 'root';       // 数据库用户名
$db_pass = '';           // 数据库密码
$db_name = 'the seeds';      // 数据库名

// 创建数据库连接
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 检查连接是否成功
if ($conn->connect_error) {
    die("Connection Fail: " . $conn->connect_error);  // 如果连接失败，显示错误信息
} else {
    echo "Connection Successful！";  // 显示连接成功的消息
}
?>
