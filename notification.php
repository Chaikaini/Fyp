<?php
// 启动 session
session_start();

// 连接数据库 - 确保这个文件使用正确的数据库配置
include 'get_email.php';  // 应该连接到 'the seeds' 数据库

// 检查用户是否登录（检查 session 中是否有用户的 email）
if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// 获取当前用户的 email
$user_email = $_SESSION['email'];

// 从数据库查询该用户的详细信息（根据 email 获取家长信息）
$sql = "SELECT parent_id, parent_name FROM parent WHERE parent_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);  // 绑定 email 参数
$stmt->execute();
$result = $stmt->get_result();

// 检查是否找到该用户
if ($result->num_rows > 0) {
    // 获取家长信息
    $row = $result->fetch_assoc();
    $parent_id = $row['parent_id'];
    $parent_name = $row['parent_name'];
    
    // 将 parent_id 存入 session 以便后续使用
    $_SESSION['parent_id'] = $parent_id;
    
    // 返回欢迎信息的 JSON 响应
    echo json_encode([
        'message' => "Welcome, $parent_name!",
        'parent_id' => $parent_id,
        'parent_name' => $parent_name
    ]);
} else {
    echo json_encode(['error' => 'User not found in parent records']);
}

// 关闭数据库连接
$stmt->close();
$conn->close();
?>