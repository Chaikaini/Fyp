<?php
header('Content-Type: application/json'); // 确保返回 JSON 格式

$year = isset($_GET['year']) ? $_GET['year'] : 'Year 1';
$query = isset($_GET['query']) ? $_GET['query'] : '';

// 数据库连接
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// 安全查询：使用预处理语句防止 SQL 注入
$sql = "SELECT 
        subject_id,
        subject_name as name,
        subject_image as image,
        subject_price as price,
        year,
        page
    FROM subject 
    WHERE year = ? 
    AND subject_name LIKE ?";

$stmt = $conn->prepare($sql);
$searchTerm = "%$query%";
$stmt->bind_param("ss", $year, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$conn->close();
echo json_encode($subjects);
?>