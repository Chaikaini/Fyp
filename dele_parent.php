<?php
// 数据库连接设置
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_information"; // 请替换为你的数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 确保是 POST 请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取 POST 请求的 JSON 数据
    $requestBody = file_get_contents("php://input");
    error_log("Request Body: " . $requestBody); // 输出请求体，查看实际内容
    $data = json_decode($requestBody, true);

    // 检查是否成功解析 JSON
    if ($data === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON received']);
        exit;
    }

    if (isset($data['username'])) {
        $username = $data['username']; // 获取要删除的用户名
        $sql = "DELETE FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Parent not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'SQL Error', 'sql_error' => $conn->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username not provided']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// 关闭连接
$conn->close();
?>
