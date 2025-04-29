<?php
session_start();
header('Content-Type: application/json');


if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$parent_id = (int)$_SESSION['parent_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['notification_id'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$notification_id = (int)$_POST['notification_id'];


$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// update read_status
$update_sql = "UPDATE notification_receiver SET read_status = 'read' WHERE parent_id = ? AND notification_id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("ii", $parent_id, $notification_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No record updated']);
}

$stmt->close();
$conn->close();
?>
