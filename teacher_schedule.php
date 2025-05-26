<?php

session_start();
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

// get
$category = $_POST['category'] ?? '';
$keyword = $_POST['keyword'] ?? '';


if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(["error" => "No teacher ID in session"]);
    exit;
}

// auto display
$sql = "SELECT 
            s.subject_id, 
            s.subject_name, 
            s.year,
            c.teacher_id,
            c.class_term,
            c.class_id,
            c.class_venue,
            c.class_time,
            p.part_duration
        FROM subject s
        JOIN class c ON s.subject_id = c.subject_id
        JOIN part p ON c.part_id = p.part_id
        WHERE c.teacher_id = ?";

$params = [intval($_SESSION['teacher_id'])];
$types = "i";

// if have search keyword, just can search the data based on teacher_id
if (!empty($category) && !empty($keyword)) {
    $allowed = ['class_term', 'subject_name'];
    if (!in_array($category, $allowed)) {
        echo json_encode(["error" => "Invalid search category"]);
        exit;
    }

    if ($category === 'class_term') {
        if (!is_numeric($keyword)) {
            echo json_encode(["error" => "Term must be a number"]);
            exit;
        }
        $sql .= " AND c.class_term = ?";
        $params[] = intval($keyword);
        $types .= "i";
    } else if ($category === 'subject_name') {
        $sql .= " AND s.subject_name LIKE ?";
        $params[] = "%$keyword%";
        $types .= "s";
    }
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'subject_id' => $row['subject_id'],
        'subject_name' => $row['subject_name'],
        'class_term' => $row['class_term'],
        'class_id' => $row['class_id'],
        'year' => $row['year'],
        'class_venue' => $row['class_venue'],
        'time' => $row['class_time'],
        'part_duration' => $row['part_duration']
    ];
}

echo json_encode($data);
$stmt->close();
$conn->close();
