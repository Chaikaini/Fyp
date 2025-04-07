<?php
session_start();
header('Content-Type: application/json'); 


$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "order";  


$conn = new mysqli($servername, $username, $password, $dbname);

// check database connecting
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// make sure user has login
if (!isset($_SESSION['email'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$email = $_SESSION['email']; // get email
$studentName = isset($_GET['student_name']) ? $_GET['student_name'] : ''; // 改成 student_name

// make sure student_name emty
if (empty($studentName)) {
    echo json_encode(["error" => "No student selected"]);
    exit;
}

// check orders 表，based on student_name & email get the information
$sql = "SELECT course_name, time FROM orders WHERE email = ? AND student_name = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}
$stmt->bind_param("ss", $email, $studentName);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = [
        "course_name" => $row["course_name"],
        "time" => $row["time"]
    ];
}

$stmt->close();
$conn->close();


echo json_encode($courses);
?>

