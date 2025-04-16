<?php
header('Content-Type: application/json');
include 'db_connect.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

if (empty($query)) {
    echo json_encode(['error' => 'Search query is empty']);
    exit;
}

// Search both subject_name and year
$sql = "SELECT 
        s.subject_id,
        s.subject_name as name,
        s.subject_image as image,
        s.subject_price as price,
        ROUND(COALESCE(AVG(c.comment_rating), 0.0), 1) as rating,
        s.page,
        s.year
    FROM subject s
    LEFT JOIN comments c ON s.subject_id = c.subject_id
    WHERE s.subject_name LIKE ? OR s.year LIKE ?
    GROUP BY s.subject_id";

$stmt = $conn->prepare($sql);
$searchTerm = "%$query%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    // Ensure all required fields exist
    $row['rating'] = $row['rating'] ?? 0;
    $subjects[] = $row;
}

$conn->close();
echo json_encode($subjects);
?>