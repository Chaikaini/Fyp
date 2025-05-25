<?php
session_start();

if (!isset($_SESSION['role']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

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

if (isset($_GET['action']) && $_GET['action'] === 'getAdmins') {
    $result = $conn->query("
        SELECT 
            teacher_id AS id, 
            teacher_name AS name, 
            teacher_ic_number AS ic_number, 
            teacher_gender AS gender, 
            teacher_email AS email, 
            teacher_phone_number AS phone, 
            teacher_address AS address, 
            teacher_join_date AS join_date, 
            teacher_status AS status 
        FROM teacher
    ");

    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'admins' => $admins]);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'searchAdmins') {
    $query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
    $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

    $sql = "
        SELECT 
            teacher_id AS id, 
            teacher_name AS name, 
            teacher_ic_number AS ic_number, 
            teacher_gender AS gender, 
            teacher_email AS email, 
            teacher_phone_number AS phone, 
            teacher_address AS address, 
            teacher_join_date AS join_date, 
            teacher_status AS status 
        FROM teacher 
        WHERE teacher_name LIKE '%$query%'
    ";
    if ($status !== '') {
        $sql .= " AND teacher_status = '$status'";
    }

    $result = $conn->query($sql);
    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'admins' => $admins]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputJSON = file_get_contents("php://input");
    $postData = json_decode($inputJSON, true);

    if ($postData["action"] === "addAdmin") {
        $teacher_name = $conn->real_escape_string($postData["name"]);
        $teacher_ic_number = trim($conn->real_escape_string($postData["ic_number"]));
        $teacher_gender = $conn->real_escape_string($postData["gender"]);
        $teacher_email = $conn->real_escape_string($postData["email"]);
        $teacher_phone = $conn->real_escape_string($postData["phone"]);
        $teacher_address = $conn->real_escape_string($postData["address"]);
        $teacher_join_date = $conn->real_escape_string($postData["join_date"]);
        $teacher_status = $conn->real_escape_string($postData["status"]);
        $teacher_password = $postData["password"];

        if (empty($teacher_password)) {
            $teacher_password = "admin123";
        }

        if (strlen($teacher_password) < 8) {
            logError("Add teacher failed: Password too short for email $teacher_email");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
            exit;
        }

        $hasLower = preg_match('/[a-z]/', $teacher_password);
        $hasUpper = preg_match('/[A-Z]/', $teacher_password);
        $hasNumber = preg_match('/\d/', $teacher_password);
        $hasSpecial = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $teacher_password);
        $strength = $hasLower + $hasUpper + $hasNumber + $hasSpecial;

        if ($strength < 2) {
            logError("Add teacher failed: Password too weak for email $teacher_email");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Password is too weak. Include at least two types: lowercase, uppercase, numbers, or special characters']);
            exit;
        }

        $emailCheck = $conn->query("SELECT teacher_id FROM teacher WHERE teacher_email = '$teacher_email'");
        if ($emailCheck->num_rows > 0) {
            logError("Add teacher failed: Email already exists: $teacher_email");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        $icCheck = $conn->query("SELECT teacher_id FROM teacher WHERE teacher_ic_number = '$teacher_ic_number'");
        if ($icCheck->num_rows > 0) {
            logError("Add teacher failed: IC number already exists: $teacher_ic_number");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'IC number already exists']);
            exit;
        }

        if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $teacher_ic_number)) {
            logError("Add teacher failed: Invalid IC number format: $teacher_ic_number");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid IC number format. Use YYMMDD-PB-####']);
            exit;
        }

        $hashed_password = password_hash($teacher_password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            INSERT INTO teacher 
            (teacher_name, teacher_ic_number, teacher_gender, teacher_email, teacher_phone_number, teacher_address, teacher_join_date, teacher_status, teacher_password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssss", $teacher_name, $teacher_ic_number, $teacher_gender, $teacher_email, $teacher_phone, $teacher_address, $teacher_join_date, $teacher_status, $hashed_password);

        if ($stmt->execute()) {
            logError("Successfully added teacher: $teacher_email");
            header('Content-Type: application/json');
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            logError("Add teacher error: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    if ($postData["action"] === "editAdmin") {
        $teacher_id = $conn->real_escape_string($postData["id"]);
        $teacher_name = $conn->real_escape_string($postData["name"]);
        $teacher_ic_number = trim($conn->real_escape_string($postData["ic_number"]));
        $teacher_gender = $conn->real_escape_string($postData["gender"]);
        $teacher_phone = $conn->real_escape_string($postData["phone"]);
        $teacher_address = $conn->real_escape_string($postData["address"]);
        $teacher_join_date = $conn->real_escape_string($postData["join_date"]);
        $teacher_status = $conn->real_escape_string($postData["status"]);

        $icCheck = $conn->query("SELECT teacher_id FROM teacher WHERE teacher_ic_number = '$teacher_ic_number' AND teacher_id != '$teacher_id'");
        if ($icCheck->num_rows > 0) {
            logError("Edit teacher failed: IC number already exists: $teacher_ic_number");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'IC number already exists']);
            exit;
        }

        if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $teacher_ic_number)) {
            logError("Edit teacher failed: Invalid IC number format: $teacher_ic_number");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid IC number format. Use YYMMDD-PB-####']);
            exit;
        }

        $stmt = $conn->prepare("
            UPDATE teacher 
            SET 
                teacher_name = ?, 
                teacher_ic_number = ?, 
                teacher_gender = ?, 
                teacher_phone_number = ?, 
                teacher_address = ?, 
                teacher_join_date = ?, 
                teacher_status = ?
            WHERE teacher_id = ?
        ");
        $stmt->bind_param("sssssssi", $teacher_name, $teacher_ic_number, $teacher_gender, $teacher_phone, $teacher_address, $teacher_join_date, $teacher_status, $teacher_id);

        if ($stmt->execute()) {
            logError("Successfully updated teacher_id: $teacher_id");
            header('Content-Type: application/json');
            echo json_encode(["success" => true]);
        } else {
            logError("Update teacher error for teacher_id $teacher_id: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    if ($postData["action"] === "deleteAdmin") {
        $teacher_id = $conn->real_escape_string($postData["id"]);
        logError("Delete request received for teacher_id: $teacher_id");

        $examTableCheck = $conn->query("SHOW TABLES LIKE 'exam_result'");
        if ($examTableCheck->num_rows > 0) {
            $checkSql = "SELECT COUNT(*) as count FROM exam_result WHERE teacher_id = '$teacher_id'";
            $checkResult = $conn->query($checkSql);
            if ($checkResult === false) {
                logError("Error checking exam_result for teacher_id $teacher_id: " . $conn->error . " | Query: $checkSql");
                header('Content-Type: application/json');
                echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
                exit;
            }
            $row = $checkResult->fetch_assoc();
            if ($row['count'] > 0) {
                logError("Cannot delete teacher_id $teacher_id: Associated exam results exist");
                header('Content-Type: application/json');
                echo json_encode(["success" => false, "message" => "Cannot delete teacher with associated exam results"]);
                exit;
            }
        }

        $stmt = $conn->prepare("DELETE FROM teacher WHERE teacher_id = ?");
        $stmt->bind_param("i", $teacher_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                logError("Successfully deleted teacher_id: $teacher_id");
                header('Content-Type: application/json');
                echo json_encode(["success" => true]);
            } else {
                logError("Delete teacher failed: No teacher found with teacher_id $teacher_id");
                header('Content-Type: application/json');
                echo json_encode(["success" => false, "message" => "No teacher found with the provided ID"]);
            }
        } else {
            logError("Delete teacher error for teacher_id $teacher_id: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }
}

$conn->close();
?>