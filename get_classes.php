<?php
// 启用错误报告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 设置响应头
header('Content-Type: application/json');

// 数据库配置
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "Connection failed: " . $conn->connect_error
    ]);
    exit;
}

// 完整的科目映射表
$subject_map = [
    // Year 1
    'year1english' => ['subject_id' => '11245', 'year' => 'Year 1'],
    'year1malay' => ['subject_id' => '11351', 'year' => 'Year 1'],
    'year1math' => ['subject_id' => '11132', 'year' => 'Year 1'],
    // Year 2
    'year2english' => ['subject_id' => '22534', 'year' => 'Year 2'],
    'year2malay' => ['subject_id' => '22345', 'year' => 'Year 2'],
    'year2math' => ['subject_id' => '22134', 'year' => 'Year 2'],
    // 默认 fallback
    'default' => ['subject_id' => '11245', 'year' => 'Year 1']
];

// 从 URL 参数获取 page
$page_identifier = $_GET['page'] ?? '';

// 如果没有 page 参数，从 Referer 判断
if (empty($page_identifier)) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    foreach ($subject_map as $key => $value) {
        if ($key !== 'default' && strpos($referer, $key) !== false) {
            $page_identifier = $key;
            break;
        }
    }
}

// 获取映射设置（subject_id 与 year）
$subject_config = $subject_map[$page_identifier] ?? $subject_map['default'];

// 调试输出
error_log("page_identifier: " . $page_identifier);
error_log("subject_config: " . print_r($subject_config, true));

// 准备 SQL 查询
$sql = "SELECT c.* 
        FROM class c
        JOIN subject s ON c.subject_id = s.subject_id
        WHERE c.subject_id = ?
        AND s.year = ?
        AND c.part_id IN (1, 2)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $subject_config['subject_id'], $subject_config['year']);
$stmt->execute();

$result = $stmt->get_result();
$classes = $result->fetch_all(MYSQLI_ASSOC);

// 调试查询结果
error_log("classes: " . print_r($classes, true));

// 输出 JSON
echo json_encode([
    "success" => true,
    "page_identifier" => $page_identifier,
    "subject_id" => $subject_config['subject_id'],
    "year" => $subject_config['year'],
    "data" => $classes
]);

// 清理
$stmt->close();
$conn->close();
?>