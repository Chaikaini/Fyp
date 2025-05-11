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

// Get notifications from admin
$sql = "
    SELECT 
        n.notification_id,
        n.notification_title,
        n.notification_content,
        n.notification_document,
        n.notification_created_at,
        n.sender_id,
        a.admin_name as sender_name
    FROM notification n
    JOIN notification_receiver nr ON n.notification_id = nr.notification_id
    JOIN admin a ON n.sender_id = a.admin_id
    WHERE nr.teacher_id = ? 
    AND nr.recipient_type = 'Teacher'
    ORDER BY n.notification_created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'notification_id' => $row['notification_id'],
        'sender_name' => $row['sender_name'],
        'sender_id' => $row['sender_id'],
        'notification_title' => $row['notification_title'],
        'notification_content' => $row['notification_content'],
        'notification_document' => $row['notification_document'],
        'notification_created_at' => $row['notification_created_at'] ?? null
    ];
}


$conn->close();

echo json_encode($notifications);
?>