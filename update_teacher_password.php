<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$teacher_id = $_SESSION['teacher_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    if ($action === 'resetPassword') {
        $current_password = $input['current_password'] ?? '';
        $new_password = $input['new_password'] ?? '';

        if (empty($current_password) || empty($new_password)) {
            echo json_encode(['status' => 'error', 'message' => 'Current and new passwords are required']);
            exit;
        }

        if (strlen($new_password) < 8) {
            echo json_encode(['status' => 'error', 'message' => 'New password must be at least 8 characters long']);
            exit;
        }

        //check password strength
        $hasLower = preg_match('/[a-z]/', $new_password);
        $hasUpper = preg_match('/[A-Z]/', $new_password);
        $hasNumber = preg_match('/\d/', $new_password);
        $hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password);
        $strength = $hasLower + $hasUpper + $hasNumber + $hasSpecial;

        if ($strength < 2) {
            echo json_encode(['status' => 'error', 'message' => 'Password is too weak. Include at least two types: lowercase, uppercase, numbers, or special characters']);
            exit;
        }

        // check current password
        $stmt = $conn->prepare("SELECT teacher_password FROM teacher WHERE teacher_id = ?");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($current_password, $user['teacher_password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
            exit;
        }

        // update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE teacher SET teacher_password = ? WHERE teacher_id = ?");
        $stmt->bind_param("si", $hashed_password, $teacher_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
        }

        $stmt->close();
        $conn->close();
        exit;
    }

   
}

// if request method is not POST
echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
$conn->close();
exit;
?>
