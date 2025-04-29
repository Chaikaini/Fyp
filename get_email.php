<?php
$servername = "127.0.0.1";  // 数据库服务器地址
$username = "root";         // 数据库用户名
$password = "";             // 数据库密码（假设为空）
$dbname = "the seeds";      // 使用正确的数据库名称

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // 连接成功
    echo "Connection successful!";
}

// 示例：查询parent表中的parent_id
function getParentIdByEmail($conn, $email) {
    $sql = "SELECT parent_id FROM parent WHERE parent_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['parent_id'];
    } else {
        return null;
    }
}

// 使用示例
// $parentEmail = "parent@example.com";
// $parentId = getParentIdByEmail($conn, $parentEmail);
// if ($parentId) {
//     echo "Parent ID: " . $parentId;
// } else {
//     echo "Parent not found";
// }
?>
