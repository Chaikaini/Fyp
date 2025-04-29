<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if (!isset($_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$role = $_SESSION['role'];

if ($role === 'Admin' && isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT admin_name AS name, admin_gender AS gender, admin_email AS email FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
} elseif ($role === 'Teacher' && isset($_SESSION['teacher_id'])) {
    $teacher_id = $_SESSION['teacher_id'];
    $sql = "SELECT teacher_name AS name, teacher_gender AS gender, teacher_email AS email FROM teacher WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $user['role'] = $role;
    echo json_encode(['status' => 'success', 'data' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$stmt->close();
$conn->close();
