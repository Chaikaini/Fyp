<?php
$servername = "localhost";
$username = "root";  
$password = "";  
$database = "the seeds";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_year = $_POST['comment_year'] ?? null;
    $comment_subject = $_POST['comment_subject'] ?? null;
    $comment_rating = $_POST['comment_rating'] ?? null;
    $comment_text = $_POST['comment_text'] ?? null;

    if (!$comment_year || !$comment_subject || !$comment_rating || !$comment_text) {
        echo "All fields are required!";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO comments (comment_year, comment_subject, comment_rating, comment_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $comment_year, $comment_subject, $comment_rating, $comment_text);

    if ($stmt->execute()) {
        echo "Comment submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
