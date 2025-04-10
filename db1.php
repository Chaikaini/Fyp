<?php
$db = new mysqli("localhost", "root", "", "admin");
if($db->connect_error) die("连接失败");
?>