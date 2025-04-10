<?php
$db = new mysqli("localhost", "root", "", "admin");

// 检查数据库连接是否成功
if ($db->connect_error) {
    die("Connection fail: " . $db->connect_error);  // 输出详细的错误信息
} else {
    echo "Connection  Successful！";  // 显示连接成功的消息
}
?>
