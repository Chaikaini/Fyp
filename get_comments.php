<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "the seeds";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT class_id, subject_id, comment_rating, comment_text, comment_created_at FROM comments ORDER BY comment_created_at DESC";
$result = $conn->query($sql);

$comments = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
}

header('Content-Type: application/json');

echo json_encode($comments, JSON_PRETTY_PRINT);

$conn->close();
?>
