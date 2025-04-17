<?php
header('Content-Type: application/json');
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'the seeds';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$class_id = $_POST['class_id'] ?? '';
if (empty($class_id)) {
    echo json_encode(["error" => "Class ID is required"]);
    exit;
}

$sql = "SELECT 
            ch.child_name, 
            ch.child_gender, 
            ch.child_kidnumber, 
            p.parent_name, 
            p.phone_number 
        FROM registration_class rc
        JOIN child ch ON rc.child_id = ch.child_id
        JOIN parent p ON ch.parent_id = p.parent_id
        WHERE rc.class_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $class_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
