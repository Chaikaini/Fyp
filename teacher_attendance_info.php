<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'the seeds';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

$subject_id = $_POST['subject_id'] ?? '';

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
        WHERE s.subject_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['error' => 'SQL error: ' . $conn->error]));
}
$stmt->bind_param("s", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'subject_id' => $row['subject_id'],
        'subject_name' => $row['subject_name'],
        'class_id' => $row['class_id'],
        'year' => $row['year'],
        'time' => $row['class_time'],
        'capacity' => $row['class_enrolled'] . '/' . $row['class_capacity'],
        'part' => $row['part_name'] . '    ' . $row['part_duration']
    ];
}

echo json_encode($data);
$conn->close();
?>
