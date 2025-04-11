<?php
// 连接数据库
$conn = new mysqli("localhost", "root", "", "admin");

// 智能识别文件名（兼容带空格和特殊字符）
$referer = $_SERVER['HTTP_REFERER'] ?? '';
preg_match('/(Year 1 English class|Year 1 Malay class)/', $referer, $matches);
$page = $matches[1] ?? '';

// 科目映射表（根据你的实际文件名配置）
$subject_ids = [
    'Year 1 English class' => 11245,  // 对应英语
    'Year 1 Malay class' => 11351,
    'Year 1 Math class' => 11132    
];

// 设置查询参数
$subject_id = $subject_ids[$page] ?? 11245; // 默认英语
$year = "Year 1";

// 查询数据库
$stmt = $conn->prepare("SELECT * FROM admin_class 
                       WHERE subject_id=? AND year=?
                       AND part IN ('Part A','Part B')");
$stmt->bind_param("is", $subject_id, $year);
$stmt->execute();

// 返回结果
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "subject" => str_replace('Year 1 ', '', $page), // 返回"English class"/"Malay class"
    "data" => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)
]);

$stmt->close();
$conn->close();
?>