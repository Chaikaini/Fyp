<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$host = 'localhost';
$dbname = 'the seeds';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verify admin_id exists in the admin table
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE admin_id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    if ($stmt->fetchColumn() == 0) {
        echo json_encode(["success" => false, "message" => "Invalid admin ID in session"]);
        exit;
    }
} catch (PDOException $e) {
    error_log(date('[Y-m-d H:i:s] ') . "Connection failed: " . $e->getMessage() . "\n", 3, 'php_errors.log');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'php_errors.log');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'getAdmins') {
        try {
            $stmt = $pdo->query("
                SELECT 
                    t.teacher_id AS id, 
                    t.teacher_name AS name, 
                    t.teacher_ic_number AS ic_number, 
                    t.teacher_gender AS gender, 
                    t.teacher_email AS email, 
                    t.teacher_phone_number AS phone, 
                    t.teacher_address AS address, 
                    t.teacher_join_date AS join_date, 
                    t.teacher_status AS status,
                    a.admin_name
                FROM teacher t
                LEFT JOIN admin a ON t.admin_id = a.admin_id
            ");
            $admins = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $admins[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'ic_number' => $row['ic_number'],
                    'gender' => $row['gender'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'address' => $row['address'],
                    'join_date' => $row['join_date'],
                    'status' => $row['status'],
                    'admin_name' => $row['admin_name'] ?? 'Unknown'
                ];
            }
            echo json_encode(['success' => true, 'admins' => $admins]);
        } catch (Exception $e) {
            logError("Error fetching teachers: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error fetching teachers']);
        }
        exit;
    }

    if (isset($_GET['action']) && $_GET['action'] === 'searchAdmins') {
        try {
            $query = isset($_GET['query']) ? '%' . trim($_GET['query']) . '%' : '%';
            $status = isset($_GET['status']) ? trim($_GET['status']) : '';

            $sql = "
                SELECT 
                    t.teacher_id AS id, 
                    t.teacher_name AS name, 
                    t.teacher_ic_number AS ic_number, 
                    t.teacher_gender AS gender, 
                    t.teacher_email AS email, 
                    t.teacher_phone_number AS phone, 
                    t.teacher_address AS address, 
                    t.teacher_join_date AS join_date, 
                    t.teacher_status AS status,
                    a.admin_name
                FROM teacher t
                LEFT JOIN admin a ON t.admin_id = a.admin_id
                WHERE t.teacher_name LIKE ?
            ";
            $params = [$query];

            if ($status !== '') {
                $sql .= " AND t.teacher_status = ?";
                $params[] = $status;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $admins = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $admins[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'ic_number' => $row['ic_number'],
                    'gender' => $row['gender'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'address' => $row['address'],
                    'join_date' => $row['join_date'],
                    'status' => $row['status'],
                    'admin_name' => $row['admin_name'] ?? 'Unknown'
                ];
            }
            echo json_encode(['success' => true, 'admins' => $admins]);
        } catch (Exception $e) {
            logError("Error searching teachers: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error searching teachers']);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $postData = json_decode($inputJSON, true);

    if ($postData['action'] === 'addAdmin') {
        try {
            $teacher_name = trim($postData['name']);
            $teacher_ic_number = trim($postData['ic_number']);
            $teacher_gender = $postData['gender'];
            $teacher_email = trim($postData['email']);
            $teacher_phone = trim($postData['phone']);
            $teacher_address = trim($postData['address']);
            $teacher_join_date = $postData['join_date'];
            $teacher_status = $postData['status'];
            $teacher_password = $postData['password'];

            // Password validation
            if (empty($teacher_password)) {
                $teacher_password = 'admin123';
            }
            if (strlen($teacher_password) < 8) {
                logError("Add teacher failed: Password too short for email $teacher_email");
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
                echo json_encode(['success' => false, 'message' => 'Password is too weak. Include at least two types: lowercase, uppercase, numbers, or special characters']);
                exit;
            }

            // Email and IC number uniqueness check
            $stmt = $pdo->prepare("SELECT teacher_id FROM teacher WHERE teacher_email = ?");
            $stmt->execute([$teacher_email]);
            if ($stmt->fetchColumn()) {
                logError("Add teacher failed: Email already exists: $teacher_email");
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT teacher_id FROM teacher WHERE teacher_ic_number = ?");
            $stmt->execute([$teacher_ic_number]);
            if ($stmt->fetchColumn()) {
                logError("Add teacher failed: IC number already exists: $teacher_ic_number");
                echo json_encode(['success' => false, 'message' => 'IC number already exists']);
                exit;
            }

            // IC number format validation
            if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $teacher_ic_number)) {
                logError("Add teacher failed: Invalid IC number format: $teacher_ic_number");
                echo json_encode(['success' => false, 'message' => 'Invalid IC number format. Use YYMMDD-PB-####']);
                exit;
            }

            // Validate gender and status
            if (!in_array($teacher_gender, ['Male', 'Female'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid gender']);
                exit;
            }
            if (!in_array($teacher_status, ['Active', 'Inactive'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                exit;
            }

            $hashed_password = password_hash($teacher_password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("
                INSERT INTO teacher 
                (teacher_id, admin_id, teacher_name, teacher_ic_number, teacher_gender, teacher_email, teacher_phone_number, teacher_address, teacher_join_date, teacher_status, teacher_password) 
                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['admin_id'],
                $teacher_name,
                $teacher_ic_number,
                $teacher_gender,
                $teacher_email,
                $teacher_phone,
                $teacher_address,
                $teacher_join_date,
                $teacher_status,
                $hashed_password
            ]);

            logError("Successfully added teacher: $teacher_email");
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (Exception $e) {
            logError("Add teacher error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($postData['action'] === 'editAdmin') {
        try {
            $teacher_id = $postData['id'];
            $teacher_name = trim($postData['name']);
            $teacher_ic_number = trim($postData['ic_number']);
            $teacher_gender = $postData['gender'];
            $teacher_phone = trim($postData['phone']);
            $teacher_address = trim($postData['address']);
            $teacher_join_date = $postData['join_date'];
            $teacher_status = $postData['status'];

            // IC number uniqueness check
            $stmt = $pdo->prepare("SELECT teacher_id FROM teacher WHERE teacher_ic_number = ? AND teacher_id != ?");
            $stmt->execute([$teacher_ic_number, $teacher_id]);
            if ($stmt->fetchColumn()) {
                logError("Edit teacher failed: IC number already exists: $teacher_ic_number");
                echo json_encode(['success' => false, 'message' => 'IC number already exists']);
                exit;
            }

            // IC number format validation
            if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $teacher_ic_number)) {
                logError("Edit teacher failed: Invalid IC number format: $teacher_ic_number");
                echo json_encode(['success' => false, 'message' => 'Invalid IC number format. Use YYMMDD-PB-####']);
                exit;
            }

            // Validate gender and status
            if (!in_array($teacher_gender, ['Male', 'Female'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid gender']);
                exit;
            }
            if (!in_array($teacher_status, ['Active', 'Inactive'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE teacher 
                SET 
                    teacher_name = ?, 
                    teacher_ic_number = ?, 
                    teacher_gender = ?, 
                    teacher_phone_number = ?, 
                    teacher_address = ?, 
                    teacher_join_date = ?, 
                    teacher_status = ?,
                    admin_id = ?
                WHERE teacher_id = ?
            ");
            $stmt->execute([
                $teacher_name,
                $teacher_ic_number,
                $teacher_gender,
                $teacher_phone,
                $teacher_address,
                $teacher_join_date,
                $teacher_status,
                $_SESSION['admin_id'],
                $teacher_id
            ]);

            logError("Successfully updated teacher_id: $teacher_id");
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            logError("Update teacher error for teacher_id $teacher_id: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($postData['action'] === 'deleteAdmin') {
        try {
            $teacher_id = $postData['id'];
            logError("Delete request received for teacher_id: $teacher_id");

            // Check if exam_result table exists and has teacher_id column
            $stmt = $pdo->query("SHOW TABLES LIKE 'exam_result'");
            if ($stmt->fetch()) {
                // Check if teacher_id column exists in exam_result
                $stmt = $pdo->query("SHOW COLUMNS FROM exam_result LIKE 'teacher_id'");
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM exam_result WHERE teacher_id = ?");
                    $stmt->execute([$teacher_id]);
                    if ($stmt->fetchColumn() > 0) {
                        logError("Cannot delete teacher_id $teacher_id: Associated exam results exist");
                        echo json_encode(['success' => false, 'message' => 'Cannot delete teacher with associated exam results']);
                        exit;
                    }
                } else {
                    logError("teacher_id column not found in exam_result table, skipping exam_result check for teacher_id: $teacher_id");
                }
            } else {
                logError("exam_result table not found, skipping exam_result check for teacher_id: $teacher_id");
            }

            // Check if class table exists and has teacher_id column
            $stmt = $pdo->query("SHOW TABLES LIKE 'class'");
            if ($stmt->fetch()) {
                // Check if teacher_id column exists in class
                $stmt = $pdo->query("SHOW COLUMNS FROM class LIKE 'teacher_id'");
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM class WHERE teacher_id = ?");
                    $stmt->execute([$teacher_id]);
                    if ($stmt->fetchColumn() > 0) {
                        logError("Cannot delete teacher_id $teacher_id: Associated classes exist");
                        echo json_encode(['success' => false, 'message' => 'Cannot delete teacher with associated classes']);
                        exit;
                    }
                } else {
                    logError("teacher_id column not found in class table, skipping class check for teacher_id: $teacher_id");
                }
            } else {
                logError("class table not found, skipping class check for teacher_id: $teacher_id");
            }

            // Proceed with deletion
            $stmt = $pdo->prepare("DELETE FROM teacher WHERE teacher_id = ?");
            $stmt->execute([$teacher_id]);

            if ($stmt->rowCount() > 0) {
                logError("Successfully deleted teacher_id: $teacher_id");
                echo json_encode(['success' => true]);
            } else {
                logError("Delete teacher failed: No teacher found with teacher_id $teacher_id");
                echo json_encode(['success' => false, 'message' => 'No teacher found with the provided ID']);
            }
        } catch (Exception $e) {
            logError("Delete teacher error for teacher_id $teacher_id: " . $e->getMessage() . " in query: " . (isset($stmt) ? $stmt->queryString : 'unknown query'));
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
}
?>