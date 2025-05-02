<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

if (!isset($_SESSION['parent_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$parent_id = $_SESSION['parent_id'];
$child_id = isset($_GET['child_id']) ? $_GET['child_id'] : '';

if (empty($child_id)) {
    echo json_encode(["error" => "No child selected"]);
    exit;
}

// 查询注册课程信息
$sql = "
SELECT 
    s.subject_name,
    s.year,
    t.teacher_name,
    c.class_id,
    c.class_time,
    p.part_name,
    p.part_duration
FROM registration_class rc
JOIN class c ON rc.class_id = c.class_id
JOIN subject s ON c.subject_id = s.subject_id
JOIN teacher t ON s.teacher_id = t.teacher_id
JOIN part p ON c.part_id = p.part_id
WHERE rc.child_id = ? AND rc.parent_id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

$stmt->bind_param("ii", $child_id, $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = [
        "subject_name" => $row["subject_name"],
        "year" => $row["year"],
        "teacher_name" => $row["teacher_name"],
        "class_id" => $row["class_id"],
        "class_time" => $row["class_time"],
        "part_name" => $row["part_name"],
        "part_duration" => $row["part_duration"]
    ];
}

$stmt->close();
$conn->close();

echo json_encode($classes);
