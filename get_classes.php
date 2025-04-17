<?php
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

// 完整的科目映射表（根据你的 subject_id 与年级设定）
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

// 准备 SQL 查询 class 表
$sql = "SELECT * FROM class 
        WHERE subject_id = ? 
        AND year = ? 
        AND part_id IN (1, 2)"; // 假设 1 = Part A, 2 = Part B

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $subject_config['subject_id'], $subject_config['year']);
$stmt->execute();

$result = $stmt->get_result();
$classes = $result->fetch_all(MYSQLI_ASSOC);

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
