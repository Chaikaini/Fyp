<?php
// Enable error reporting
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 数据库连接
$servername = "localhost";
$username = "root";
$password = "";
$database = "the seeds";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 获取 subject_id 参数
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// 查询评论数据
$sql = "SELECT comment_id, parent_id, subject_id, 
               comment_rating AS rating, 
               comment_text AS comment, 
               comment_created_at 
        FROM comments 
        WHERE subject_id = ?
        ORDER BY comment_created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param("i", $subject_id);
if (!$stmt->execute()) {
    die(json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]));
}

$result = $stmt->get_result();
$comments = [];

while ($row = $result->fetch_assoc()) {
    $comments[] = [
        'comment_id' => $row['comment_id'],
       // 'parent_id' => $row['parent_id'],
        'rating' => (float)$row['rating'],
        'comment' => $row['comment'],
        'comment_created_at' => $row['comment_created_at'],
        'parent_name' => $row['parent_id'] // 临时显示为 Parent 1, Parent 2 等
    ];
}

// 设置响应头
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// 返回 JSON 数据
echo json_encode([
    'status' => 'success',
    'data' => $comments,
    'debug' => [
        'subject_id_used' => $subject_id,
        'num_comments' => count($comments)
    ]
]);

$conn->close();
?>