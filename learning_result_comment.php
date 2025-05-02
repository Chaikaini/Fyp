<?php
header('Content-Type: application/json');


$servername = "localhost";
$username = "root";
$password = "";
$database = "the seeds";

try {
  
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}


$child_id = isset($_GET['child_id']) ? $_GET['child_id'] : null;
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

if (!$child_id || !$class_id) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}


$sqlResult = "SELECT exam_result_midterm, exam_result_final FROM exam_result WHERE child_id = :child_id AND class_id = :class_id LIMIT 1";
$stmtResult = $pdo->prepare($sqlResult);
$stmtResult->execute(['child_id' => $child_id, 'class_id' => $class_id]);
$exam = $stmtResult->fetch(PDO::FETCH_ASSOC);

$sqlComment = "SELECT teacher_comment_text FROM teacher_comment WHERE child_id = :child_id AND class_id = :class_id LIMIT 1";
$stmtComment = $pdo->prepare($sqlComment);
$stmtComment->execute(['child_id' => $child_id, 'class_id' => $class_id]);
$comment = $stmtComment->fetch(PDO::FETCH_ASSOC);


$response = [
    'exam_result_midterm' => $exam['exam_result_midterm'] ?? null,
    'exam_result_final' => $exam['exam_result_final'] ?? null,
    'teacher_comment_text' => $comment['teacher_comment_text'] ?? null
];

echo json_encode($response);
