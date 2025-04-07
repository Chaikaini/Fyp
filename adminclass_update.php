<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$order_dbname = "order";
$admin_dbname = "admin";

$conn = new mysqli($servername, $username, $password, $admin_dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];

   

    // 获取总容量 enrollment（admin 数据库）
    $getTotalQuery = "SELECT enrollment FROM admin_class WHERE class_id = ?";
    $stmt = $conn->prepare($getTotalQuery);
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $enrollment = $result->fetch_assoc()['enrollment'];

    // 切换到订单数据库，获取已注册学生数
    $conn->select_db($order_dbname);

    $getStudentsQuery = "SELECT student_name, gender FROM orders WHERE class_id = ?";
    $stmt = $conn->prepare($getStudentsQuery);
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    $current_count = count($students);

    echo json_encode([
        'students' => $students,
        'capacity' => "$current_count/$enrollment"
    ]);
}
?>
