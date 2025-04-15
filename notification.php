<?php
session_start();
header('Content-Type: application/json');

// 检查是否已登录且有parent_id
if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['error' => '用户未登录或会话失效', 'code' => 401]);
    exit();
}

// 数据库连接（使用您提供的配置）
$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
if ($conn->connect_error) {
    echo json_encode(['error' => '数据库连接失败', 'code' => 500]);
    exit();
}

$parent_id = $_SESSION['parent_id'];

// 查询用户信息
$sql = "SELECT 
          parent_name, 
          welcome_notification_shown,
          first_login_date
        FROM parent 
        WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => '用户数据不存在', 'code' => 404]);
    exit();
}

$row = $result->fetch_assoc();

// 处理首次登录
if (!$row['welcome_notification_shown']) {
    $current_date = date('Y-m-d');
    $update_sql = "UPDATE parent 
                  SET welcome_notification_shown = 1,
                      first_login_date = ?
                  WHERE parent_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $current_date, $parent_id);
    $update_stmt->execute();
    $display_date = $current_date;
} else {
    $display_date = $row['first_login_date'];
}

echo json_encode([
    'success' => true,
    'name' => $row['parent_name'],
    'date' => $display_date,
    'message' => "Welcome, {$row['parent_name']}!"  // 添加message字段保持前端兼容
]);

$stmt->close();
$conn->close();
?>