<?php
header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp2\htdocs\Fyp\Fyp\php_errors.log');

// Handle CORS preflight
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "the seeds";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class_id = $_POST['class_id'] ?? null;
    $subject_id = $_POST['subject_id'] ?? null;
    $comment_rating = $_POST['comment_rating'] ?? null;
    $comment_text = $_POST['comment_text'] ?? null;

    if (!$class_id || !$subject_id || !$comment_rating || !$comment_text) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
        exit;
    }

    $comment_rating = (int)$comment_rating;
    if ($comment_rating < 1 || $comment_rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid rating value!']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO comments (class_id, subject_id, comment_rating, comment_text, comment_created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        error_log("SQL Preparation Error: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'SQL Preparation Failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssis", $class_id, $subject_id, $comment_rating, $comment_text);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Comment saved successfully!']);
    } else {
        error_log("SQL Execution Error: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Failed to save comment: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    error_log("Invalid request method: " . $_SERVER["REQUEST_METHOD"]);
    echo json_encode(['status' => 'error', 'message' => 'Method not supported!']);
}

$conn->close();
?>