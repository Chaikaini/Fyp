<?php
// 启动会话
session_start();

// 数据库连接
$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['isLoggedIn' => false, 'error' => 'Database connection failed']);
    exit;
}

// 防止 SQL 注入和其他输入问题
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// 检查登录状态
function getParentIdByEmail($conn, $email) {
    if (empty($email)) return null;
    $email = sanitizeInput($email);
    $sql = "SELECT parent_id FROM parent WHERE parent_email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return null;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['parent_id'] : null;
}

// 设置响应头
header('Content-Type: application/json');

// 初始化登录状态
$isLoggedIn = false;

// 仅当会话中存在 parent_email 且非空时检查
if (isset($_SESSION['parent_email']) && !empty($_SESSION['parent_email'])) {
    $parentId = getParentIdByEmail($conn, $_SESSION['parent_email']);
    if ($parentId !== null) {
        $isLoggedIn = true;
    } else {
        // 如果邮箱在数据库中不存在，清空会话（防止伪造）
        unset($_SESSION['parent_email']);
    }
}

// 输出响应
echo json_encode(['isLoggedIn' => $isLoggedIn]);

// 关闭数据库连接
$conn->close();
?>