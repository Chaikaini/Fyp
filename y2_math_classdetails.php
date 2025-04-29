<?php
// 连接到新的数据库
$servername = "localhost";
$username = "root"; // 替换为你的数据库用户名
$password = "";     // 替换为你的数据库密码
$dbname = "admin";  // 新的数据库名

$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 设置要查询的课程条件
$year = 'Year 2';
$subject_id = 22134; // 例如 Malay 的 subject_id

// 查询符合条件的课程 Part A 和 Part B
$sql = "SELECT * FROM admin_class 
        WHERE year = '$year' 
        AND subject_id = $subject_id 
        AND part IN ('Part A', 'Part B')";

$result = $conn->query($sql);

// 返回数据
if ($result->num_rows > 0) {
    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
    echo json_encode($classes); // 返回 JSON 数据
} else {
    echo "No data found";
}

$conn->close();
?>
