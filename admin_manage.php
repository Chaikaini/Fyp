<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

// Enable error reporting for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'php_errors.log');
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    logError("Connection failed: " . $conn->connect_error);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check user role
if (isset($_GET['action']) && $_GET['action'] === 'checkRole') {
    if (!isset($_SESSION['admin_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $admin_id = $conn->real_escape_string($_SESSION['admin_id']);
    $result = $conn->query("SELECT role FROM admin WHERE admin_id = '$admin_id'");
    if ($result && $row = $result->fetch_assoc()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'role' => $row['role']]);
    } else {
        logError("Role check failed for admin_id: $admin_id");
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Role check failed']);
    }
    exit;
}

// Restrict access to authorized users
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Fetch all admins
if (isset($_GET['action']) && $_GET['action'] === 'getAdmins') {
    $result = $conn->query("
        SELECT 
            admin_id AS id, 
            admin_name AS name, 
            admin_gender AS gender, 
            admin_email AS email, 
            admin_phone_number AS phone, 
            admin_address AS address,
            role
        FROM admin
    ");

    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'admins' => $admins]);
    exit;
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputJSON = file_get_contents("php://input");
    $postData = json_decode($inputJSON, true);

    // Add admin (Super Admin only)
    if ($postData["action"] === "addAdmin") {
        $admin_id = $conn->real_escape_string($_SESSION['admin_id']);
        $result = $conn->query("SELECT role FROM admin WHERE admin_id = '$admin_id'");
        $row = $result->fetch_assoc();
        if (!$row || $row['role'] !== 'Super Admin') {
            logError("Add admin attempt by non-Super Admin: admin_id $admin_id");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Only Super Admins can add new admins']);
            exit;
        }

        $admin_name = $conn->real_escape_string($postData["name"]);
        $admin_gender = $conn->real_escape_string($postData["gender"]);
        $admin_email = $conn->real_escape_string($postData["email"]);
        $admin_phone = $conn->real_escape_string($postData["phone"]);
        $admin_address = $conn->real_escape_string($postData["address"]);
        $admin_password = $postData["password"];
        $role = 'Admin'; // New admins are always regular Admins

        if (empty($admin_password)) {
            $admin_password = "admin123";
        }

        // Server-side password strength validation
        if (strlen($admin_password) < 8) {
            logError("Add admin failed: Password too short for email $admin_email");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
            exit;
        }

        $hasLower = preg_match('/[a-z]/', $admin_password);
        $hasUpper = preg_match('/[A-Z]/', $admin_password);
        $hasNumber = preg_match('/\d/', $admin_password);
        $hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $admin_password);
        $strength = $hasLower + $hasUpper + $hasNumber + $hasSpecial;

        if ($strength < 2) {
            logError("Add admin failed: Password too weak for email $admin_email");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Password is too weak. Include at least two types: lowercase, uppercase, numbers, or special characters']);
            exit;
        }

        // Check for duplicate email
        $emailCheck = $conn->query("SELECT admin_id FROM admin WHERE admin_email = '$admin_email'");
        if ($emailCheck->num_rows > 0) {
            logError("Add admin failed: Email already exists: $admin_email");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            INSERT INTO admin 
            (admin_name, admin_gender, admin_email, admin_phone_number, admin_address, admin_password, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $admin_name, $admin_gender, $admin_email, $admin_phone, $admin_address, $hashed_password, $role);

        if ($stmt->execute()) {
            logError("Successfully added admin: $admin_email");
            header('Content-Type: application/json');
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            logError("Add admin error: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    // Edit admin
    if ($postData["action"] === "editAdmin") {
        $admin_id = $conn->real_escape_string($postData["id"]);
        $admin_name = $conn->real_escape_string($postData["name"]);
        $admin_gender = $conn->real_escape_string($postData["gender"]);
        $admin_phone = $conn->real_escape_string($postData["phone"]);
        $admin_address = $conn->real_escape_string($postData["address"]);

        $stmt = $conn->prepare("
            UPDATE admin 
            SET 
                admin_name = ?, 
                admin_gender = ?, 
                admin_phone_number = ?, 
                admin_address = ?
            WHERE admin_id = ?
        ");
        $stmt->bind_param("ssssi", $admin_name, $admin_gender, $admin_phone, $admin_address, $admin_id);

        if ($stmt->execute()) {
            logError("Successfully updated admin_id: $admin_id");
            header('Content-Type: application/json');
            echo json_encode(["success" => true]);
        } else {
            logError("Update admin error for admin_id $admin_id: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    // Delete admin
    if ($postData["action"] === "deleteAdmin") {
        $admin_id = $conn->real_escape_string($postData["id"]);
        logError("Delete request received for admin_id: $admin_id");

        // Prevent deletion of Super Admins
        $result = $conn->query("SELECT role FROM admin WHERE admin_id = '$admin_id'");
        if ($result && $row = $result->fetch_assoc()) {
            if ($row['role'] === 'Super Admin') {
                logError("Delete admin failed: Cannot delete Super Admin with admin_id $admin_id");
                header('Content-Type: application/json');
                echo json_encode(["success" => false, "message" => "Cannot delete a Super Admin"]);
                exit;
            }
        }

        $stmt = $conn->prepare("DELETE FROM admin WHERE admin_id = ?");
        $stmt->bind_param("i", $admin_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                logError("Successfully deleted admin_id: $admin_id");
                header('Content-Type: application/json');
                echo json_encode(["success" => true]);
            } else {
                logError("No rows deleted for admin_id $admin_id: Record not found");
                header('Content-Type: application/json');
                echo json_encode(["success" => false, "message" => "Admin record not found"]);
            }
        } else {
            logError("Delete error for admin_id $admin_id: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }
}

// Search admins
if (isset($_GET['action']) && $_GET['action'] === 'searchAdmins' && isset($_GET['query'])) {
    $query = $conn->real_escape_string($_GET['query']);

    $sql = "
        SELECT 
            admin_id AS id, 
            admin_name AS name, 
            admin_gender AS gender, 
            admin_email AS email, 
            admin_phone_number AS phone, 
            admin_address AS address,
            role
        FROM admin 
        WHERE admin_name LIKE '%$query%'
    ";

    $result = $conn->query($sql);
    $admins = [];

    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'admins' => $admins]);
    exit;
}

$conn->close();
?>