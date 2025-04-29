<?php
session_start();
include 'get_email.php';  // 引入数据库连接文件

// 检查管理员是否登录
if (!isset($_SESSION['admin_email'])) {
    echo json_encode(['error' => 'Admin not logged in']);
    exit();
}

// 获取管理员提交的通知内容
$title = $_POST['title'];
$message = $_POST['message'];
$userSelect = $_POST['userSelect'];
$userEmail = isset($_POST['userEmail']) ? $_POST['userEmail'] : null;
$notificationFile = isset($_FILES['notificationFile']) ? $_FILES['notificationFile'] : null;

// 验证通知内容
if (empty($title) || empty($message)) {
    echo json_encode(['error' => 'Title and message cannot be empty']);
    exit();
}

// 插入通知到数据库
$sql = "INSERT INTO notifications (title, message, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $title, $message);
$stmt->execute();
$notificationId = $stmt->insert_id;

// 如果有文件上传，处理文件并保存路径
if ($notificationFile) {
    $fileName = time() . '-' . $notificationFile['name'];
    $uploadDir = 'uploads/';
    $uploadPath = $uploadDir . $fileName;
    move_uploaded_file($notificationFile['tmp_name'], $uploadPath);

    // 更新数据库，保存文件路径
    $sql = "UPDATE notifications SET file_path = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $uploadPath, $notificationId);
    $stmt->execute();
}

// 如果通知发送给特定用户，保存该用户的通知记录
if ($userSelect == 'specific' && $userEmail) {
    // 这里假设 `users` 表有 `email` 和 `id`
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId);
        $stmt->fetch();

        $sql = "INSERT INTO user_notifications (user_id, notification_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $notificationId);
        $stmt->execute();
    } else {
        echo json_encode(['error' => 'User not found']);
        exit();
    }
} elseif ($userSelect == 'all') {
    // 如果发送给所有用户，插入所有用户的记录
    $sql = "SELECT id FROM users";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $sql = "INSERT INTO user_notifications (user_id, notification_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $notificationId);
        $stmt->execute();
    }
}

echo json_encode(['success' => 'Notification sent successfully']);
$stmt->close();
$conn->close();
?>
