<?php
session_start();  
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "order");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

// get email
$email = isset($_SESSION["email"]) ? $conn->real_escape_string($_SESSION["email"]) : "";


// get data
$class_id = $conn->real_escape_string($data["class_id"]); 
$student_name = $conn->real_escape_string($data["student_name"]);
$course_name = $conn->real_escape_string($data["course_name"]);
$teacher_name = $conn->real_escape_string($data["teacher_name"]);
$total_amount = (float) $data["total_amount"];
$payment_method = $conn->real_escape_string($data["payment_method"]);
$time = $conn->real_escape_string($data["time"]);

// insert data
$sql = "INSERT INTO orders (class_id, email, student_name, course_name, teacher_name, total_amount, payment_method, time) 
        VALUES ('$class_id','$email', '$student_name', '$course_name', '$teacher_name', '$total_amount', '$payment_method', '$time')";

if ($conn->query($sql) === TRUE) {
    $order_id = $conn->insert_id;
    echo json_encode(["success" => true, "order_id" => "ORD" . str_pad($order_id, 4, "0", STR_PAD_LEFT)]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();

?>
