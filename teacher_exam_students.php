<?php

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$class_id = $_GET['class_id'];

$sql = "SELECT student_id, student_name, exam_result_midterm AS midterm, exam_result_final AS final 
        FROM exam_result 
        JOIN child ON exam_result.child_id = child.child_id 
        WHERE class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $class_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

$stmt->close();
$conn->close();
?>
