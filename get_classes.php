<?php
// 连接数据库
$conn = new mysqli("localhost", "root", "", "admin");

// 方法1：通过URL参数强制指定科目（最可靠）
$subject = $_GET['subject'] ?? '';  // 从URL获取参数

// 方法2：如果未传参，尝试从Referer判断（备用方案）
if(empty($subject)) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referer, 'year1english') !== false) {
        $subject = 'english';
    } elseif (strpos($referer, 'year1malay') !== false) {
        $subject = 'malay';
    }
}

// 科目ID映射
$subject_ids = [
    'english' => 11245,
    'malay' => 11351
];

// 设置查询参数
$subject_id = $subject_ids[strtolower($subject)] ?? 11245; // 默认英语
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
    "subject" => $subject,  // 返回当前科目
    "data" => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)
]);

$stmt->close();
$conn->close();
?>