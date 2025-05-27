<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Check if subject_id is provided and is numeric
if (!isset($_GET['subject_id']) || !is_numeric($_GET['subject_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid or missing subject_id"]);
    exit;
}

$subject_id = (int)$_GET['subject_id'];

$stmt = $conn->prepare("SELECT subject_price FROM subject WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["success" => true, "price" => $row['subject_price']]);
} else {
    echo json_encode(["success" => false, "message" => "Subject not found"]);
}

$stmt->close();
$conn->close();
?>