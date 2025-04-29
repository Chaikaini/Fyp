<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}


$teacher_id = $_SESSION['teacher_id'];
$data = json_decode(file_get_contents("php://input"), true);

$current_password = $data['current_password'] ?? '';
$new_password = $data['new_password'] ?? '';

if (empty($current_password) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "Missing password fields"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT teacher_password FROM teacher WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($current_password, $user['teacher_password'])) {
        echo json_encode(["status" => "error", "message" => "Current password is incorrect"]);
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE teacher SET teacher_password = ? WHERE teacher_id = ?");
    $stmt->bind_param("si", $hashed_password, $teacher_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["status" => "success", "message" => "Password updated successfully"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>
