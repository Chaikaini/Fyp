<?php
header('Content-Type: application/json');
include 'db_connect.php';

// 获取 year 参数，如果未提供或为空，则不设置默认值
$year = isset($_GET['year']) ? $_GET['year'] : '';

$subjects = [];

// 如果 year 为空，返回空结果
if (empty($year)) {
    echo json_encode($subjects);
    exit;
}

// 直接从 subject 表获取数据
$sql = "SELECT 
        s.subject_id,
        s.subject_name as name,
        s.subject_image as image,
        s.subject_price as price,
        ROUND(COALESCE(AVG(c.comment_rating), 0.0), 1) as rating,
        COALESCE(s.page_path, s.page) as page,
        s.year
    FROM subject s
    LEFT JOIN comments c ON s.subject_id = c.subject_id
    WHERE s.year = ?
    GROUP BY s.subject_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode($subjects);
?>