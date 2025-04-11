<?php
// 设置响应头
header('Content-Type: application/json');

// 数据库配置
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin";

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

// 完整的科目ID映射表（根据您的数据库实际情况调整）
$subject_map = [
    // Year 1 科目
    'year1english' => ['subject_id' => 11245, 'year' => 'Year 1'],
    'year1malay' => ['subject_id' => 11351, 'year' => 'Year 1'],
    'year1math' => ['subject_id' => 11132, 'year' => 'Year 1'],
    
    // Year 2 科目
    'year2english' => ['subject_id' => 22534, 'year' => 'Year 2'],
    'year2malay' => ['subject_id' => 22345, 'year' => 'Year 2'], 
    'year2math' => ['subject_id' => 22134, 'year' => 'Year 2'],
    
    // 默认值（找不到匹配时使用）
    'default' => ['subject_id' => 11245, 'year' => 'Year 1']
];

// 方法1：优先从URL参数获取页面标识
$page_identifier = $_GET['page'] ?? '';

// 方法2：如果无URL参数，尝试从Referer自动识别
if (empty($page_identifier)) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    // 检查所有可能的页面标识
    foreach ($subject_map as $key => $value) {
        if ($key !== 'default' && strpos($referer, $key) !== false) {
            $page_identifier = $key;
            break;
        }
    }
}

// 获取对应的科目配置
$subject_config = $subject_map[$page_identifier] ?? $subject_map['default'];

// 查询数据库
$stmt = $conn->prepare("SELECT * FROM admin_class 
                       WHERE subject_id = ? 
                       AND year = ?
                       AND part IN ('Part A', 'Part B')");
$stmt->bind_param("is", $subject_config['subject_id'], $subject_config['year']);
$stmt->execute();

$result = $stmt->get_result();
$classes = $result->fetch_all(MYSQLI_ASSOC);

// 返回JSON结果
echo json_encode([
    "success" => true,
    "page_identifier" => $page_identifier,  // 调试用，显示当前匹配的页面标识
    "subject_id" => $subject_config['subject_id'],
    "year" => $subject_config['year'],
    "data" => $classes
]);

// 关闭连接
$stmt->close();
$conn->close();
?>