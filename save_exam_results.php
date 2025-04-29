<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

foreach ($data as $result) {
    $student_id = $result['student_id'];
    $exam = $result['exam'];
    $value = $result['value'];

    $sql = "UPDATE exam_result SET exam_result_$exam = ? WHERE child_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $value, $student_id);
    $stmt->execute();
}

$response = ["success" => true];
echo json_encode($response);

$stmt->close();
$conn->close();
?>
