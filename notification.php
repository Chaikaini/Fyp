<?php
session_start();
header('Content-Type: application/json');

// 检查登录状态
if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['error' => 'User not logged in', 'code' => 401]);
    exit();
}

// 数据库连接
$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed', 'code' => 500]);
    exit();
}

$parent_id = (int)$_SESSION['parent_id'];

// 检查是否是首次登录
$check_sql = "SELECT first_login_date FROM parent WHERE parent_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'User not found', 'code' => 404]);
    exit();
}

$user = $result->fetch_assoc();

// 如果是首次登录
if (empty($user['first_login_date'])) {
    $current_date = date('Y-m-d');
    
    // 1. 更新首次登录日期
    $update_sql = "UPDATE parent SET first_login_date = ? WHERE parent_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $current_date, $parent_id);
    $update_stmt->execute();
    
    // 2. 创建永久通知记录
    $notification_sql = "INSERT INTO notification 
                        (admin_id, class_id, notification_category, notification_content, notification_created_at)
                        VALUES
                        (1, NULL, 'welcome', CONCAT('Welcome, ', (SELECT parent_name FROM parent WHERE parent_id = ?), '!'), NOW())";
    $notification_stmt = $conn->prepare($notification_sql);
    $notification_stmt->bind_param("i", $parent_id);
    $notification_stmt->execute();
}

// 获取所有通知（包括欢迎通知）
$notifications = $conn->query("SELECT * FROM notification WHERE notification_category = 'welcome' ORDER BY notification_created_at DESC")
                     ->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'notifications' => $notifications
]);

$conn->close();
?>