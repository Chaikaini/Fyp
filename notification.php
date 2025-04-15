<?php
session_start();
header('Content-Type: application/json');

// 检查登录状态和parent_id
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

// 安全查询（只查询必要字段）
$sql = "SELECT 
          parent_name, 
          welcome_notification_shown
        FROM parent 
        WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL prepare failed', 'code' => 500]);
    exit();
}

$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'User not found', 'code' => 404]);
    exit();
}

$user = $result->fetch_assoc();

// 处理欢迎信息
$response = [
    'success' => true,
    'name' => $user['parent_name'],
    'message' => "Welcome back, {$user['parent_name']}!"
];

// 如果是首次登录
if (!$user['welcome_notification_shown']) {
    $current_date = date('Y-m-d');
    
    // 更新数据库
    $update_sql = "UPDATE parent 
                  SET welcome_notification_shown = 1,
                      first_login_date = ?
                  WHERE parent_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $current_date, $parent_id);
    
    if ($update_stmt->execute()) {
        $response['message'] = "Welcome, {$user['parent_name']}!";
        $response['date'] = $current_date;
    }
}

echo json_encode($response);

$stmt->close();
if (isset($update_stmt)) $update_stmt->close();
$conn->close();
?>