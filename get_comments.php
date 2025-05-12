<?php
header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "the seeds";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . $conn->connect_error]);
    exit;
}

$subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;


$sql = "SELECT 
            c.comment_id, 
            c.parent_id, 
            c.class_id, 
            c.subject_id, 
            c.comment_rating, 
            c.comment_text, 
            c.comment_created_at,
            s.subject_name, 
            cl.year 
        FROM comments c
        LEFT JOIN subject s ON c.subject_id = s.subject_id
        LEFT JOIN class cl ON c.class_id = cl.class_id";
$params = [];
$types = "";

if ($subject_id && $class_id) {
    $sql .= " WHERE c.subject_id = ? AND c.class_id = ?";
    $params = [$subject_id, $class_id];
    $types = "ss";
} elseif ($subject_id) {
    $sql .= " WHERE c.subject_id = ?";
    $params = [$subject_id];
    $types = "s";
} elseif ($class_id) {
    $sql .= " WHERE c.class_id = ?";
    $params = [$class_id];
    $types = "s";
}

$sql .= " ORDER BY c.comment_created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Preparation Error: ' . $conn->error]);
    exit;
}

if ($params) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Execution Error: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$comments = [];

while ($row = $result->fetch_assoc()) {
    $comments[] = [
        'comment_id' => $row['comment_id'],
        'parent_id' => $row['parent_id'],
        'class_id' => $row['class_id'],
        'subject_id' => $row['subject_id'],
        'comment_rating' => (int)$row['comment_rating'],
        'comment_text' => $row['comment_text'],
        'comment_created_at' => $row['comment_created_at'],
        'subject_name' => $row['subject_name'], // 新增 subject_name
        'year' => $row['year'] // 新增 year
    ];
}

echo json_encode([
    'status' => 'success',
    'data' => $comments,
    'debug' => [
        'subject_id_used' => $subject_id,
        'class_id_used' => $class_id,
        'num_comments' => count($comments)
    ]
]);

$conn->close();
?>