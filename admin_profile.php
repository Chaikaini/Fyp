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

if (!isset($_SESSION['role']) || !isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Handle POST request
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

        // Server-side password strength validation
        $hasLower = preg_match('/[a-z]/', $new_password);
        $hasUpper = preg_match('/[A-Z]/', $new_password);
        $hasNumber = preg_match('/\d/', $new_password);
        $hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password);
        $strength = $hasLower + $hasUpper + $hasNumber + $hasSpecial;

        if ($strength < 2) {
            echo json_encode(['status' => 'error', 'message' => 'Password is too weak. Include at least two types: lowercase, uppercase, numbers, or special characters']);
            exit;
        }

        // Fetch current password hash
        $sql = "SELECT admin_password FROM admin WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        // Verify current password
        if (!password_verify($current_password, $user['admin_password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
            exit;
        }

        // Hash new password
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password
        $sql = "UPDATE admin SET admin_password = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_password_hashed, $admin_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
        }

        $stmt->close();
        $conn->close();
        exit;
    } else {
        // Existing profile update logic
        $name = $input['name'] ?? '';
        $gender = $input['gender'] ?? '';
        $email = $input['email'] ?? '';
        $address = $input['address'] ?? '';
        $phone_number = $input['phone_number'] ?? '';

        if (empty($name) || empty($gender) || empty($email) || empty($address) || empty($phone_number)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }

        $sql = "UPDATE admin SET admin_name = ?, admin_gender = ?, admin_email = ?, admin_address = ?, admin_phone_number = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $gender, $email, $address, $phone_number, $admin_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
        }

        $stmt->close();
        $conn->close();
        exit;
    }
}

// Handle GET request for fetching profile
$sql = "SELECT admin_name AS name, admin_gender AS gender, admin_email AS email, admin_address AS address, admin_phone_number AS phone_number FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode(['status' => 'success', 'data' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$stmt->close();
$conn->close();
?>