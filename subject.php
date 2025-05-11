<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate'); // 防止缓存
header('Pragma: no-cache');
header('Expires: 0');

include 'db_connect.php';

$year = isset($_GET['year']) ? $_GET['year'] : '';
error_log("Received year: $year");

$subjects = [];

if (empty($year)) {
    error_log("Year is empty");
    echo json_encode($subjects);
    exit;
}

$sql = "SELECT 
        s.subject_id,
        s.subject_name as name,
        s.subject_image as image,
        s.subject_price as price,
        ROUND(COALESCE(AVG(c.comment_rating), 0.0), 1) as rating,
        s.year
    FROM subject s
    LEFT JOIN comments c ON s.subject_id = c.subject_id
    WHERE s.year = ?
    GROUP BY s.subject_id";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['error' => 'SQL prepare failed']);
    exit;
}

$stmt->bind_param("s", $year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['page'] = 'generate_subject_page.php?id=' . $row['subject_id'];
    $subjects[] = $row;
}

error_log("Subjects found: " . count($subjects));
echo json_encode($subjects);
?>