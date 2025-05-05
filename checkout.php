<?php
session_start();
header('Content-Type: application/json');

$servername = "127.0.0.1";
$username = "root"; 
$password = ""; 
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

if (!isset($_SESSION['parent_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$parent_id = $_SESSION['parent_id'];

// 联表查询
$stmt = $conn->prepare("
    SELECT c.cart_id, c.class_id, c.subject_id, c.price, c.teacher_id,
           s.subject_name, s.subject_image,
           t.teacher_name,
           ch.child_name,
           cl.class_time
    FROM cart c
    LEFT JOIN subject s ON c.subject_id = s.subject_id
    LEFT JOIN teacher t ON c.teacher_id = t.teacher_id
    LEFT JOIN child ch ON c.child_id = ch.child_id
    LEFT JOIN class cl ON c.class_id = cl.class_id
    WHERE c.parent_id = ? AND c.deleted = 0
");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

echo json_encode($cartItems);
$conn->close();
?>
