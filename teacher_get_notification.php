<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "the seeds");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// get notification
$sql = "SELECT * FROM notification ORDER BY notification_id DESC";
$result = $conn->query($sql);

$notifications = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // based on the sender_id, get the teacher name
        $sender_id = $row['sender_id'];
        $sqlSender = "SELECT teacher_name FROM teacher WHERE teacher_id = ?";
        $stmtSender = $conn->prepare($sqlSender);
        $stmtSender->bind_param("s", $sender_id);
        $stmtSender->execute();
        $resultSender = $stmtSender->get_result();

        $teacher_name = 'Unknown Teacher';
        if ($resultSender->num_rows > 0) {
            $teacher_name = $resultSender->fetch_assoc()['teacher_name'];
        }

        // Add notification to array
        $notifications[] = [
            'notification_id' => $row['notification_id'],
            'sender_name' => $teacher_name, 
            'sender_id' => $sender_id, 
            'subject_id' => $row['subject_id'],
            'class_id' => $row['class_id'],
            'notification_title' => $row['notification_title'],
            'notification_content' => $row['notification_content'],
            'notification_document' => $row['notification_document'],
            'notification_created_at' => $row['notification_created_at'] ?? null
        ];
    }
}

// Reverse the order of notifications to display the newest at the top
$notifications = array_reverse($notifications);

$conn->close();

echo json_encode($notifications);
?>
