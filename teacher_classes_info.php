<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'the seeds';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

// get teacher_id from session
$teacher_id = $_SESSION['teacher_id'] ?? '';
if (empty($teacher_id)) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}


$subject_id = $_POST['subject_id'] ?? '';
$subject_name = $_POST['subject_name'] ?? '';

if (!empty($subject_id)) {
    $sql = "SELECT 
                s.subject_id, 
                s.subject_name, 
                c.class_id, 
                c.class_time, 
                c.class_capacity,
                c.class_enrolled,
                p.part_name,
                p.part_duration,
                s.year
            FROM subject s
            JOIN class c ON s.subject_id = c.subject_id
            JOIN part p ON c.part_id = p.part_id
            WHERE s.subject_id = ? AND c.teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $subject_id, $teacher_id);
} elseif (!empty($subject_name)) {
    $sql = "SELECT 
                s.subject_id, 
                s.subject_name, 
                c.class_id, 
                c.class_time, 
                c.class_capacity,
                c.class_enrolled,
                p.part_name,
                p.part_duration,
                s.year
            FROM subject s
            JOIN class c ON s.subject_id = c.subject_id
            JOIN part p ON c.part_id = p.part_id
            WHERE s.subject_name LIKE ? AND c.teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $subject_name = '%' . $subject_name . '%';
    $stmt->bind_param("ss", $subject_name, $teacher_id);
} else {
    // if no searching, get all classes for the teacher
    $sql = "SELECT 
                s.subject_id, 
                s.subject_name, 
                c.class_id, 
                c.class_time, 
                c.class_capacity,
                c.class_enrolled,
                p.part_name,
                p.part_duration,
                s.year
            FROM subject s
            JOIN class c ON s.subject_id = c.subject_id
            JOIN part p ON c.part_id = p.part_id
            WHERE c.teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $teacher_id);
}

if (!$stmt) {
    die(json_encode(['error' => 'SQL error: ' . $conn->error]));
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'subject_id' => $row['subject_id'],
        'class_id' => $row['class_id'],
        'subject_name' => $row['subject_name'],
        'year' => $row['year'],
        'time' => $row['class_time'],
        'capacity' => $row['class_enrolled'] . '/' . $row['class_capacity'],
        'part' => $row['part_name'] . ' ' . $row['part_duration']
    ];
}

echo json_encode($data);
$conn->close();
?>
