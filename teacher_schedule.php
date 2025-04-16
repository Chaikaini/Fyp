<?php

header('Content-Type: application/json');
ini_set('display_errors', 0); // 禁止直接输出错误
ini_set('log_errors', 1); // 启用错误日志记录

$host = 'localhost';
$user = 'root';
$password = ''; 
$dbname = 'the seeds'; 

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$teacher_id = $_POST['teacher_id'] ?? '';

if (empty($teacher_id)) {
    echo json_encode(["error" => "Teacher ID is required"]);
    exit;
}

if (!is_numeric($teacher_id)) {
    echo json_encode(["error" => "Teacher ID must be a number"]);
    exit;
}

$teacher_id = (int)$teacher_id;

$sql = "SELECT 
            s.subject_id, 
            s.subject_name, 
            s.teacher_id,
            c.class_id,
            c.year,
            c.class_time
        FROM subject s
        JOIN class c ON s.subject_id = c.subject_id
        WHERE s.teacher_id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed"]);
    exit;
}

$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();