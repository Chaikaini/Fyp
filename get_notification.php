<?php
session_start();
header('Content-Type: application/json');



// check user is first time login or not
if (!isset($_SESSION['first_login'])) {
  
    $_SESSION['first_login'] = true;
    // if first time show welcome message
}


$parent_id = $_SESSION['parent_id'];
$conn = new mysqli("127.0.0.1", "root", "", "the seeds");


if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed', 'code' => 500]);
    exit();
}


$user_sql = "SELECT parent_name FROM parent WHERE parent_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['error' => 'User not found', 'code' => 404]);
    exit();
}


$notification_sql = "
    SELECT 
        n.notification_id,
        n.notification_title,
        n.notification_content,
        n.notification_document,
        n.notification_created_at,
        nr.read_status,
        t.teacher_name AS sender_name,  
        s.subject_name                 
    FROM notification_receiver nr
    JOIN notification n ON nr.notification_id = n.notification_id
    JOIN teacher t ON n.sender_id = t.teacher_id  
    JOIN subject s ON n.subject_id = s.subject_id  
    WHERE nr.parent_id = ?  
    ORDER BY n.notification_created_at DESC
";
$notif_stmt = $conn->prepare($notification_sql);
$notif_stmt->bind_param("i", $parent_id);  
$notif_stmt->execute();
$notifications = $notif_stmt->get_result()->fetch_all(MYSQLI_ASSOC);



echo json_encode([
    'success' => true,
    'user_name' => $user['parent_name'],
    'first_login' => $_SESSION['first_login'],  
    'notifications' => $notifications
]);

$notifications = array_reverse($notifications);
$conn->close();

?>
