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

// 获取用户信息（仅需姓名和首次登录日期）
$user_sql = "SELECT parent_name, first_login_date FROM parent WHERE parent_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['error' => 'User not found', 'code' => 404]);
    exit();
}

// 如果是首次登录，更新日期（不创建通知记录）
if (empty($user['first_login_date'])) {
    $update_sql = "UPDATE parent SET first_login_date = CURDATE() WHERE parent_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $parent_id);
    $update_stmt->execute();
    $user['first_login_date'] = date('Y-m-d');
}

// 获取真实通知（从原有notification表查询）
$notifications = $conn->query("
    SELECT * FROM notification 
    ORDER BY notification_created_at DESC
")->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'user_name' => $user['parent_name'],
    'first_login_date' => $user['first_login_date'],
    'notifications' => $notifications
]);

$conn->close();
?>