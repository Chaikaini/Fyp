<?php
session_start();

// 数据库配置
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin";

// 连接数据库
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. 从 Session 获取年级和科目
$year = $_SESSION['selected_year'] ?? 'Year 1';  // 默认 'Year 1'
$subject_id = $_SESSION['selected_subject_id'] ?? 11245;  // 默认科目 ID

// 2. 从 GET 参数获取 class_id（必须是 5 位数）
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;

// 检查 class_id 是否有效（5位数）
if (!$class_id || strlen((string)$class_id) != 5) {
    die(json_encode(["error" => "Invalid class_id. Must be a 5-digit number."]));
}

// 3. SQL 查询：获取 Part A 和 Part B，并匹配 class_id
$sql = "SELECT * FROM admin_class 
        WHERE year = ? 
        AND subject_id = ? 
        AND class_id = ? 
        AND part IN ('Part A', 'Part B')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $year, $subject_id, $class_id);  // s=string, i=integer
$stmt->execute();

$result = $stmt->get_result();
$classes = $result->fetch_all(MYSQLI_ASSOC);  // 直接获取全部数据

// 4. 返回 JSON 格式数据
header('Content-Type: application/json');
echo json_encode($classes);

// 关闭连接
$stmt->close();
$conn->close();
?>