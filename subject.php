<?php
header('Content-Type: application/json');
include 'db_connect.php';

$year = isset($_GET['year']) ? $_GET['year'] : 'Year 1';

// 直接从subject表获取数据，因为year字段已经在subject表中
$sql = "SELECT 
        s.subject_id,
        s.subject_name as name,
        s.subject_image as image,
        s.subject_price as price,
        ROUND(COALESCE(AVG(c.comment_rating), 0.0), 1) as rating, -- 默认 0.0，保留 1 位小数
        s.page,
        s.year
    FROM subject s
    LEFT JOIN comments c ON s.subject_id = c.subject_id
    WHERE s.year = ?
    GROUP BY s.subject_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $year);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode($subjects);
?>