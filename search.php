<?php
header('Content-Type: application/json');
include 'db_connect.php';

$query = isset($_GET['query']) ? '%' . $_GET['query'] . '%' : '';
$results = [];

if (empty($query)) {
    echo json_encode($results);
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
    WHERE s.subject_name LIKE ?
    GROUP BY s.subject_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $query);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['page'] = 'generate_subject_page.php?id=' . $row['subject_id'];
    $results[] = $row;
}

echo json_encode($results);
?>