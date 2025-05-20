<?php
session_start();
header('Content-Type: application/json');

// Check for unread notifications count
if (isset($_GET['check_unread'])) {
    $parent_id = $_SESSION['parent_id'];
    $conn = new mysqli("127.0.0.1", "root", "", "the seeds");
    
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }
    
    $sql = "SELECT COUNT(*) as unread_count FROM notification_receiver 
            WHERE parent_id = ? AND read_status = 'unread'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    echo json_encode(['unread_count' => $result['unread_count']]);
    $conn->close();
    exit();
}

// Mark single notification as read
if (isset($_POST['mark_single_read']) && isset($_POST['notification_id'])) {
    $parent_id = $_SESSION['parent_id'];
    $notification_id = $_POST['notification_id'];
    $conn = new mysqli("127.0.0.1", "root", "", "the seeds");
    
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }
    
    $sql = "UPDATE notification_receiver SET read_status = 'read' 
            WHERE parent_id = ? AND notification_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $parent_id, $notification_id);
    $success = $stmt->execute();
    
    echo json_encode(['success' => $success]);
    $conn->close();
    exit();
}

// check user is first time login or not
if (!isset($_SESSION['first_login'])) {
    $_SESSION['first_login'] = true;
}

$parent_id = $_SESSION['parent_id'];
$conn = new mysqli("127.0.0.1", "root", "", "the seeds");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed', 'code' => 500]);
    exit();
}

// Get user information
$user_sql = "SELECT parent_name FROM parent WHERE parent_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['error' => 'User not found', 'code' => 404]);
    exit();
}

// Get notifications
$notification_sql = "
    SELECT 
        n.notification_id,
        n.notification_title,
        n.notification_content,
        n.notification_document,
        n.notification_created_at,
        nr.read_status,
        CASE 
            WHEN n.sender_id IN (SELECT admin_id FROM admin) THEN a.admin_name
            ELSE t.teacher_name
        END AS sender_name,
        COALESCE(s.subject_name, 'General Announcement') as subject_name, 
        s.year AS year                
    FROM notification_receiver nr
    JOIN notification n ON nr.notification_id = n.notification_id
    LEFT JOIN teacher t ON n.sender_id = t.teacher_id  
    LEFT JOIN admin a ON n.sender_id = a.admin_id
    LEFT JOIN class c ON n.class_id = c.class_id
    LEFT JOIN subject s ON c.subject_id = s.subject_id
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

$conn->close();
?>