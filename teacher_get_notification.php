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

// process to mark a single notification as read
if (isset($_GET['mark_single_read']) && isset($_GET['notification_id'])) {
    $notification_id = $_GET['notification_id'];
    $updateSql = "
        UPDATE notification_receiver 
        SET read_status = 'read'
        WHERE notification_id = ? AND teacher_id = ? AND recipient_type = 'Teacher'
    ";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("is", $notification_id, $teacher_id);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
    exit;
}

// count the unread notifications
if (isset($_GET['check_unread']) && $_GET['check_unread'] === 'true') {
    $sql = "
        SELECT COUNT(*) as unread_count
        FROM notification_receiver
        WHERE teacher_id = ? AND recipient_type = 'Teacher' AND read_status = 'unread'
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(["unread_count" => (int)$row['unread_count']]);
    $conn->close();
    exit;
}

// chick the bell icon mark as read
if (isset($_GET['mark_as_read']) && $_GET['mark_as_read'] === 'true') {
    $updateSql = "
        UPDATE notification_receiver
        SET read_status = 'read'
        WHERE teacher_id = ? AND recipient_type = 'Teacher' AND read_status = 'unread'
    ";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
}


$sql = "
    SELECT 
        n.notification_id,
        n.notification_title,
        n.notification_content,
        n.notification_document,
        n.notification_created_at,
        n.sender_id,
        nr.read_status,
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
        'is_unread' => $row['read_status'] === 'unread',
        'notification_created_at' => $row['notification_created_at'] ?? null
    ];
}

$conn->close();

echo json_encode($notifications);
?>
