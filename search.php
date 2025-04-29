<?php
header('Content-Type: application/json');
include 'db_connect.php';

// 确保没有意外输出
ob_start(); // 开启输出缓冲，避免意外输出干扰 JSON

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($query)) {
    echo json_encode([]);
    ob_end_flush();
    exit;
}

// Search only in subject_name to avoid year mismatches
$sql = "SELECT 
        s.subject_id,
        s.subject_name AS name,
        s.subject_image AS image,
        s.subject_price AS price,
        ROUND(COALESCE(AVG(c.comment_rating), 0.0), 1) AS rating,
        COALESCE(s.page_path, s.page) AS page,
        s.year
    FROM subject s
    LEFT JOIN comments c ON s.subject_id = c.subject_id
    WHERE s.subject_name LIKE ?
    GROUP BY s.subject_id
    ORDER BY s.subject_name ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    ob_end_flush();
    exit;
}

$searchTerm = "%$query%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

// Clean up
$stmt->close();
$result->free();
$conn->close();

echo json_encode($subjects);
ob_end_flush();
?>