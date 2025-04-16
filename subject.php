<?php
header('Content-Type: application/json');
include 'db_connect.php';

$year = isset($_GET['year']) ? $_GET['year'] : 'Year 1';

$sql = "SELECT 
        s.subject_id,
        s.subject_name as name,
        s.subject_image as image,
        s.subject_price as price,
        COALESCE(AVG(c.comment_rating), 5.0) as rating,
        s.page,
        t.teacher_name as teacher,
        cl.class_id
    FROM subject s
    JOIN teacher t ON s.teacher_id = t.teacher_id
    JOIN class cl ON s.subject_id = cl.subject_id
    LEFT JOIN comments c ON s.subject_id = c.subject_id
    WHERE cl.year = ?
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