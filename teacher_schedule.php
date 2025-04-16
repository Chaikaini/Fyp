<?php

$host = 'localhost';
$user = 'root';
$password = ''; 
$dbname = 'the seeds'; 

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$teacher_id = $_POST['teacher_id'] ?? '';

$sql = "SELECT 
            s.subject_id, 
            s.subject_name, 
            s.teacher_id,
            c.class_id,
            c.year,
            DATE_FORMAT(c.class_time, '%l%p') AS start_time,
            DATE_FORMAT(c.class_end_time, '%l%p') AS end_time
        FROM subject s
        JOIN class c ON s.subject_id = c.subject_id
        WHERE s.teacher_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['time'] = $row['start_time'] . "-" . $row['end_time'];
    unset($row['start_time'], $row['end_time']); 
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
