<?php
include('db1.php');

$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// 修改查询以获取家长姓名
$query = "SELECT c.*, p.parent_name 
          FROM comments c
          LEFT JOIN parent p ON c.parent_id = p.parent_id
          WHERE c.subject_id = ? 
          ORDER BY c.comment_created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    // 将字段名映射为前端期望的名称
    $comments[] = [
        'comment_id' => $row['comment_id'],
        'parent_id' => $row['parent_id'],
        'parent_name' => $row['parent_name'] ?? 'Anonymous',
        'subject_id' => $row['subject_id'],
        'rating' => $row['comment_rating'], // 映射字段
        'comment' => $row['comment_text'],  // 映射字段
        'comment_created_at' => $row['comment_created_at']
    ];
}

// 返回统一格式的JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'data' => $comments]);
?>