<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'php_errors.log');
}

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "the seeds";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    logError("Connection failed: " . $conn->connect_error);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Handle JSON input for POST requests
$inputJSON = file_get_contents('php://input');
$postData = json_decode($inputJSON, true);

// Handle Update Request for Primary Contact
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($postData['action']) && $postData['action'] === 'update') {
    $parent_id = $conn->real_escape_string($postData['parent_id']);
    $parent_name = $conn->real_escape_string($postData['parent_name']);
    $ic_number = $conn->real_escape_string($postData['ic_number']);
    $parent_gender = $conn->real_escape_string($postData['parent_gender']);
    $phone_number = $conn->real_escape_string($postData['phone_number']);
    $parent_address = $conn->real_escape_string($postData['parent_address']);
    $parent_relationship = $conn->real_escape_string($postData['parent_relationship']);

    if ($parent_id) {
        $stmt = $conn->prepare("
            UPDATE parent 
            SET 
                parent_name = ?, 
                ic_number = ?, 
                parent_gender = ?, 
                phone_number = ?, 
                parent_address = ?, 
                parent_relationship = ?
            WHERE parent_id = ?
        ");
        $stmt->bind_param('ssssssi', $parent_name, $ic_number, $parent_gender, $phone_number, $parent_address, $parent_relationship, $parent_id);

        if ($stmt->execute()) {
            logError("Successfully updated parent_id: $parent_id");
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            logError("Update parent error for parent_id $parent_id: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
        exit;
    } else {
        logError("Update parent failed: Parent ID missing");
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Parent ID is missing']);
        exit;
    }
}

// Handle Update Request for Second Contact
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($postData['action']) && $postData['action'] === 'update_second_contact') {
    $parent_id = $conn->real_escape_string($postData['parent_id']);
    $parent_name2 = $conn->real_escape_string($postData['parent_name2']);
    $parent_relationship2 = $conn->real_escape_string($postData['parent_relationship2']);
    $parent_num2 = $conn->real_escape_string($postData['parent_num2']);

    if ($parent_id) {
        $stmt = $conn->prepare("
            UPDATE parent 
            SET 
                parent_name2 = ?, 
                parent_relationship2 = ?, 
                parent_num2 = ?
            WHERE parent_id = ?
        ");
        $stmt->bind_param('sssi', $parent_name2, $parent_relationship2, $parent_num2, $parent_id);

        if ($stmt->execute()) {
            logError("Successfully updated second contact for parent_id: $parent_id");
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            logError("Update second contact error for parent_id $parent_id: " . $stmt->error);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
        exit;
    } else {
        logError("Update second contact failed: Parent ID missing");
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Parent ID is missing']);
        exit;
    }
}

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($postData['action']) && $postData['action'] === 'deleteParent') {
    $parent_id = $conn->real_escape_string($postData['parent_id']);
    logError("Delete request received for parent_id: $parent_id");

    // Check for child table dependencies
    $childTableCheck = $conn->query("SHOW TABLES LIKE 'child'");
    if ($childTableCheck->num_rows > 0) {
        $checkSql = "SELECT COUNT(*) as count FROM child WHERE parent_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('i', $parent_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $row = $checkResult->fetch_assoc();
        if ($row['count'] > 0) {
            logError("Cannot delete parent_id $parent_id: Associated child records exist");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot delete parent with associated child records']);
            exit;
        }
        $checkStmt->close();
    }

    $stmt = $conn->prepare("DELETE FROM parent WHERE parent_id = ?");
    $stmt->bind_param('i', $parent_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            logError("Successfully deleted parent_id: $parent_id");
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            logError("No rows deleted for parent_id $parent_id: Record not found");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Parent record not found']);
        }
    } else {
        logError("Delete error for parent_id $parent_id: " . $stmt->error);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Handle Search Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($postData['search_term'])) {
    $search_term = $conn->real_escape_string($postData['search_term']);

    $stmt = $conn->prepare("
        SELECT 
            parent_id,
            parent_name,
            ic_number,
            parent_gender,
            parent_email,
            phone_number,
            parent_address,
            parent_relationship,
            parent_name2,
            parent_relationship2,
            parent_num2
        FROM parent
        WHERE parent_name LIKE ?
        ORDER BY parent_id ASC
    ");
    $like_term = '%' . $search_term . '%';
    $stmt->bind_param('s', $like_term);
    $stmt->execute();
    $result = $stmt->get_result();

    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($registrations);
    $stmt->close();
    exit;
}

// Fetch All Parents
$stmt = $conn->prepare("
    SELECT 
        parent_id,
        parent_name,
        ic_number,
        parent_gender,
        parent_email,
        phone_number,
        parent_address,
        parent_relationship,
        parent_name2,
        parent_relationship2,
        parent_num2
    FROM parent
    ORDER BY parent_id ASC
");
$stmt->execute();
$result = $stmt->get_result();

$registrations = [];
while ($row = $result->fetch_assoc()) {
    $registrations[] = $row;
}

header('Content-Type: application/json');
echo json_encode($registrations);
$stmt->close();
$conn->close();
?>