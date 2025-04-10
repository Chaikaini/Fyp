<?php
$db = new mysqli("localhost", "root", "", "admin");
if ($db->connect_error) {
    die("连接失败: " . $db->connect_error);  // 输出详细的错误信息
}
?>
