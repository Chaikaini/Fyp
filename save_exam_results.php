<?php
session_start(); 
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'the seeds';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}


if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(["error" => "Unauthorized: Teacher not logged in"]);
    exit;
}

$teacher_id = $_SESSION['teacher_id']; //get teacher_id from session
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['class_id']) || !is_array($data['results'])) {
    echo json_encode(["error" => "Invalid input data"]);
    exit;
}

$class_id = $data['class_id'];
$results = $data['results'];

foreach ($results as $entry) {
    $child_id = $entry['child_id'];
    $midterm = $entry['exam_result_midterm'];
    $final = $entry['exam_result_final'];

    // Validate is the data are exist
    $checkSql = "SELECT * FROM exam_result WHERE class_id = ? AND child_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $class_id, $child_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // if exist then update 
        $updateSql = "UPDATE exam_result 
                      SET exam_result_midterm = ?, exam_result_final = ?, teacher_id = ?
                      WHERE class_id = ? AND child_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ddsss", $midterm, $final, $teacher_id, $class_id, $child_id);
        $updateStmt->execute();
    } else {
        // if not exist insert new data
        $insertSql = "INSERT INTO exam_result (class_id, child_id, exam_result_midterm, exam_result_final, teacher_id)
                      VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ssdds", $class_id, $child_id, $midterm, $final, $teacher_id);
        $insertStmt->execute();
    }
}

echo json_encode(["success" => true]);
$conn->close();
?>
