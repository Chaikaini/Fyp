<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "the seeds");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$parent_id = $_POST['parent_id'] ?? '';
if (empty($parent_id)) {
    echo json_encode(["error" => "Parent ID is required"]);
    exit;
}

$sql = "SELECT * FROM parent WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Parent not found"]);
}

$conn->close();
?>
