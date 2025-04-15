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

// 获取用户信息
$sql = "SELECT parent_name, first_login_date FROM parent WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'User not found', 'code' => 404]);
    exit();
}

$user = $result->fetch_assoc();

// 如果是首次登录，更新日期
if (empty($user['first_login_date'])) {
    $current_date = date('Y-m-d');
    $update_sql = "UPDATE parent SET first_login_date = ? WHERE parent_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $current_date, $parent_id);
    $update_stmt->execute();
    $is_first_login = true;
} else {
    $is_first_login = false;
}

// 获取真实通知（不包括欢迎通知）
$notifications = $conn->query("SELECT * FROM notification WHERE parent_id = $parent_id AND notification_category != 'welcome' ORDER BY notification_created_at DESC")
                     ->fetch_all(MYSQLI_ASSOC);

// 准备响应数据
$response = [
    'success' => true,
    'notifications' => $notifications,
    'user_info' => [
        'name' => $user['parent_name'],
        'first_login_date' => $user['first_login_date'] ?? date('Y-m-d'),
        'is_first_login' => $is_first_login
    ]
];

echo json_encode($response);
$conn->close();
?>