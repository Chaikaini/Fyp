<?php
// 检查是否存在 'year' 参数，若不存在则使用默认值 'Year 1'
$year = isset($_GET['year']) ? $_GET['year'] : 'Year 1';
$query = isset($_GET['query']) ? $_GET['query'] : '';

// 数据库连接
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 根据年级和查询条件搜索科目
$sql = "SELECT * FROM subjects WHERE year = '$year' AND name LIKE '%$query%'";
$result = $conn->query($sql);

$subjects = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

$conn->close();

// 返回 JSON 格式的结果
echo json_encode($subjects);
?>
