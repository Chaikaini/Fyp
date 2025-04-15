<?php
session_start();
include 'get_email.php';

header('Content-Type: application/json'); // 确保返回JSON格式

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => '用户未登录', 'code' => 401]);
    exit();
}

$conn = /* 你的数据库连接 */;
$user_email = $_SESSION['email'];

// 查询用户信息（确保字段存在）
$sql = "SELECT 
            parent_id, 
            parent_name, 
            IFNULL(welcome_notification_shown, 0) as welcome_shown,
            IFNULL(first_login_date, CURDATE()) as login_date
        FROM parent 
        WHERE parent_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => '用户数据不存在', 'code' => 404]);
    exit();
}

$row = $result->fetch_assoc();

// 如果是首次登录，更新数据库
if (!$row['welcome_shown']) {
    $update_sql = "UPDATE parent 
                  SET welcome_notification_shown = 1,
                      first_login_date = CURDATE()
                  WHERE parent_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $row['parent_id']);
    $update_stmt->execute();
}

echo json_encode([
    'success' => true,
    'name' => $row['parent_name'], // 确保字段名统一
    'date' => $row['login_date']   // 使用数据库返回的日期
]);

$stmt->close();
$conn->close();
?>