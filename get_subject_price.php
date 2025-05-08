<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
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

$conn->close();
?>