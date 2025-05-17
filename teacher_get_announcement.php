<?php
session_start();
header('Content-Type: application/json');


if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

$conn = new mysqli("localhost", "root", "", "the seeds");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}


$teacher_name = 'Unknown Teacher';
$sqlTeacher = "SELECT teacher_name FROM teacher WHERE teacher_id = ?";
$stmtTeacher = $conn->prepare($sqlTeacher);
$stmtTeacher->bind_param("s", $teacher_id);
$stmtTeacher->execute();
$resultTeacher = $stmtTeacher->get_result();

if ($resultTeacher->num_rows > 0) {
    $teacher_name = $resultTeacher->fetch_assoc()['teacher_name'];
}

// check and display all anouncement of the teacher
$sql = "SELECT * FROM notification WHERE sender_id = ? ORDER BY notification_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'notification_id' => $row['notification_id'],
        'sender_name' => $teacher_name, 
        'sender_id' => $row['sender_id'],
        'class_id' => $row['class_id'],
        'notification_title' => $row['notification_title'],
        'notification_content' => $row['notification_content'],
        'notification_document' => $row['notification_document'],
        'notification_created_at' => $row['notification_created_at'] ?? null
    ];
}

$conn->close();

echo json_encode($notifications);
?>
