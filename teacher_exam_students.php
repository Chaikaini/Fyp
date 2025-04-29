<?php
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

$class_id = $_POST['class_id'] ?? '';
if (empty($class_id)) {
    echo json_encode(["error" => "Class ID is required"]);
    exit;
}


$sql = "SELECT ch.child_id , ch.child_name, er.exam_result_midterm, er.exam_result_final
        FROM registration_class rc
        JOIN child ch ON rc.child_id = ch.child_id
        LEFT JOIN exam_result er ON er.child_id = ch.child_id AND er.class_id = rc.class_id
        WHERE rc.class_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $class_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
$conn->close();
