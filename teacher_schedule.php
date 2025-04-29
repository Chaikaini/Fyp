<?php

header('Content-Type: application/json');
ini_set('display_errors', 0); 
ini_set('log_errors', 1); 

$host = 'localhost';
$user = 'root';
$password = ''; 
$dbname = 'the seeds'; 

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$category = $_POST['category'] ?? '';
$keyword = $_POST['keyword'] ?? '';

$allowed = ['teacher_id', 'subject_name'];
if (!in_array($category, $allowed)) {
    echo json_encode(["error" => "Invalid search category"]);
    exit;
}

if (empty($keyword)) {
    echo json_encode(["error" => "No class found"]);
    exit;
}

if ($category === 'teacher_id' && !is_numeric($keyword)) {
    echo json_encode(["error" => "Teacher ID must be number"]);
    exit;
}


$sql = "SELECT 
            s.subject_id, 
            s.subject_name, 
            s.teacher_id,
            c.class_id,
            s.year,
            c.class_time,
            p.part_duration
        FROM subject s
        JOIN class c ON s.subject_id = c.subject_id
        JOIN part p ON c.part_id = p.part_id
        WHERE s.$category LIKE ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit;
}

$param = ($category === 'teacher_id') ? intval($keyword) : "%$keyword%";
if ($category === 'teacher_id') {
    $stmt->bind_param("i", $param);
} else {
    $stmt->bind_param("s", $param);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'subject_id' => $row['subject_id'],
        'subject_name' => $row['subject_name'],
        'teacher_id' => $row['teacher_id'],
        'class_id' => $row['class_id'],
        'year' => $row['year'],
        'time' => $row['class_time'],
        'part_duration' => $row['part_duration'] 
    ];
}

echo json_encode($data);
$stmt->close();
$conn->close();
