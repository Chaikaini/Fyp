<?php
session_start();
include 'get_email.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'user no login']);
    exit();
}

$user_email = $_SESSION['email'];

// 查询用户信息 + 是否首次登录
$sql = "SELECT parent_id, parent_name, welcome_notification_shown 
        FROM parent 
        WHERE parent_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $parent_id = $row['parent_id'];
    $parent_name = $row['parent_name'];
    $is_first_login = !$row['welcome_notification_shown'];
    
    // 如果是首次登录
    if ($is_first_login) {
        // 1. 标记为已登录
        $update_sql = "UPDATE parent SET welcome_notification_shown = TRUE 
                      WHERE parent_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $parent_id);
        $update_stmt->execute();
        
        // 2. 返回首次登录的日期（当前时间）
        echo json_encode([
            'show_welcome' => true,
            'message' => "欢迎, $parent_name！",
            'first_login_date' => date('Y-m-d') // 固定日期
        ]);
    } else {
        // 非首次登录：不返回日期（前端用本地存储的日期）
        echo json_encode(['show_welcome' => false]);
    }
} else {
    echo json_encode(['error' => '用户不存在']);
}

$stmt->close();
if (isset($update_stmt)) $update_stmt->close();
$conn->close();
?>