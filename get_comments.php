<?php
include('db1.php');

$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

$query = "SELECT * FROM comments WHERE subject_id = ? ORDER BY comment_created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

header('Content-Type: application/json');
echo json_encode($comments);
?>