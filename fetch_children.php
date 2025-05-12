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

// Function to send JSON response
function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        logError("Connection failed: " . $conn->connect_error);
        sendJsonResponse(["success" => false, "error" => "Database connection failed"]);
    }

    // Check if child table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'child'");
    if ($tableCheck->num_rows === 0) {
        logError("Child table does not exist in database: $dbname");
        sendJsonResponse(["success" => false, "error" => "Child table does not exist"]);
    }

    function calculateBirthdayFromIC($ic) {
        if ($ic && strlen($ic) >= 6) {
            $year = substr($ic, 0, 2);
            $month = substr($ic, 2, 2);
            $day = substr($ic, 4, 2);
            $fullYear = (int)$year >= 0 && (int)$year <= 24 ? "20$year" : "19$year";
            return "$fullYear-$month-$day";
        }
        return null;
    }

    function calculateYear($birthYear, $registerYear) {
        $age = $registerYear - $birthYear;  
        return ($age < 7) ? 1 : $age - 6;
    }

    // Handle Debug Request
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['debug'])) {
        $sql = "SELECT COUNT(*) as count FROM child";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        
        $schemaSql = "SHOW COLUMNS FROM child";
        $schemaResult = $conn->query($schemaSql);
        $schema = [];
        while ($schemaRow = $schemaResult->fetch_assoc()) {
            $schema[] = $schemaRow;
        }
        
        $regTableCheck = $conn->query("SHOW TABLES LIKE 'registration'");
        $examTableCheck = $conn->query("SHOW TABLES LIKE 'exam_result'");
        
        sendJsonResponse([
            "status" => "debug",
            "child_count" => $row['count'],
            "database" => $dbname,
            "connection" => $conn->ping() ? "OK" : "Failed",
            "child_table_schema" => $schema,
            "registration_table_exists" => $regTableCheck->num_rows > 0,
            "exam_result_table_exists" => $examTableCheck->num_rows > 0
        ]);
    }

    // Handle Update Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $child_id = $conn->real_escape_string($_POST['child_id']);
        $child_name = $conn->real_escape_string($_POST['child_name']);
        $child_gender = $conn->real_escape_string($_POST['child_gender']);
        $child_kidNumber = $conn->real_escape_string($_POST['child_kidNumber']);
        $child_school = $conn->real_escape_string($_POST['child_school']);
        $child_year = $conn->real_escape_string($_POST['child_year']);
        
        $child_birthday = calculateBirthdayFromIC($child_kidNumber);
        if (!$child_birthday) {
            sendJsonResponse(["success" => false, "error" => "Invalid IC number"]);
        }

        $imagePath = null;
        if (isset($_FILES['child_image']) && $_FILES['child_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = uniqid() . '_' . basename($_FILES['child_image']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['child_image']['tmp_name'], $targetPath)) {
                $imagePath = $targetPath;
            } else {
                logError("Failed to upload image for child_id $child_id");
                sendJsonResponse(["success" => false, "error" => "Failed to upload image"]);
            }
        }

        if ($child_id) {
            $sql = "
                UPDATE child 
                SET 
                    child_name = '$child_name',
                    child_gender = '$child_gender',
                    child_kidNumber = '$child_kidNumber',
                    child_birthday = '$child_birthday',
                    child_school = '$child_school',
                    child_year = '$child_year'"
                    . ($imagePath ? ", child_image = '$imagePath'" : "") . "
                WHERE child_id = '$child_id'
            ";

            if ($conn->query($sql) === TRUE) {
                sendJsonResponse(["success" => true]);
            } else {
                logError("Update error for child_id $child_id: " . $conn->error . " | Query: $sql");
                sendJsonResponse(["success" => false, "error" => "Database error: " . $conn->error]);
            }
        } else {
            logError("Update request failed: Child ID is missing");
            sendJsonResponse(["success" => false, "error" => "Child ID is missing"]);
        }
    }

    // Handle Delete Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        logError("Delete request received: " . json_encode($_POST));
        if (!isset($_POST['child_id']) || empty($_POST['child_id'])) {
            logError("Delete request failed: Child ID is missing or empty");
            sendJsonResponse(["success" => false, "error" => "Child ID is missing"]);
        }

        $child_id = $conn->real_escape_string($_POST['child_id']);

        $regTableCheck = $conn->query("SHOW TABLES LIKE 'registration'");
        if ($regTableCheck->num_rows > 0) {
            $checkSql = "SELECT COUNT(*) as count FROM registration WHERE child_id = '$child_id'";
            $checkResult = $conn->query($checkSql);
            if ($checkResult === FALSE) {
                logError("Error checking registration dependencies for child_id $child_id: " . $conn->error . " | Query: $checkSql");
            } else {
                $row = $checkResult->fetch_assoc();
                if ($row['count'] > 0) {
                    logError("Cannot delete child_id $child_id: Associated registrations exist");
                    sendJsonResponse(["success" => false, "error" => "Cannot delete child with associated registrations"]);
                }
            }
        }

        $examTableCheck = $conn->query("SHOW TABLES LIKE 'exam_result'");
        if ($examTableCheck->num_rows > 0) {
            $checkSql = "SELECT COUNT(*) as count FROM exam_result WHERE child_id = '$child_id'";
            $checkResult = $conn->query($checkSql);
            if ($checkResult === FALSE) {
                logError("Error checking exam_result dependencies for child_id $child_id: " . $conn->error . " | Query: $checkSql");
            } else {
                $row = $checkResult->fetch_assoc();
                if ($row['count'] > 0) {
                    logError("Cannot delete child_id $child_id: Associated exam results exist");
                    sendJsonResponse(["success" => false, "error" => "Cannot delete child with associated exam results"]);
                }
            }
        }

        $verifySql = "SELECT child_image FROM child WHERE child_id = '$child_id'";
        $verifyResult = $conn->query($verifySql);
        if ($verifyResult === FALSE) {
            logError("Error verifying child_id $child_id: " . $conn->error . " | Query: $verifySql");
            sendJsonResponse(["success" => false, "error" => "Database error: " . $conn->error]);
        }
        if ($verifyResult->num_rows === 0) {
            logError("No child found for child_id $child_id");
            sendJsonResponse(["success" => false, "error" => "Child record not found"]);
        }

        $row = $verifyResult->fetch_assoc();
        $imagePath = $row['child_image'];
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $sql = "DELETE FROM child WHERE child_id = '$child_id'";
        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                logError("Successfully deleted child_id $child_id");
                sendJsonResponse(["success" => true]);
            } else {
                logError("No rows deleted for child_id $child_id: Record not found during deletion");
                sendJsonResponse(["success" => false, "error" => "Child record not found during deletion"]);
            }
        } else {
            logError("Delete error for child_id $child_id: " . $conn->error . " | Query: $sql");
            sendJsonResponse(["success" => false, "error" => "Database error: " . $conn->error]);
        }
    }

    // Handle Search Request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_term'])) {
        $search_term = $conn->real_escape_string($_POST['search_term']);

        $sql = "
            SELECT 
                ch.child_id, 
                ch.child_name, 
                p.parent_name, 
                ch.child_gender, 
                ch.child_kidNumber, 
                ch.child_birthday, 
                ch.child_school, 
                ch.child_year,
                ch.child_image
            FROM child ch
            LEFT JOIN parent p ON ch.parent_id = p.parent_id
            WHERE ch.child_name LIKE '%$search_term%'
            ORDER BY ch.child_id ASC
        ";

        $result = $conn->query($sql);
        if ($result === FALSE) {
            logError("Search query error: " . $conn->error . " | Query: $sql");
            sendJsonResponse(["success" => false, "error" => "Database error: " . $conn->error]);
        }

        $registrations = [];
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }

        sendJsonResponse($registrations);
    }

    // Fetch All Children
    $sql = "
        SELECT 
            ch.child_id, 
            ch.child_name, 
            p.parent_name, 
            ch.child_gender, 
            ch.child_kidNumber, 
            ch.child_birthday, 
            ch.child_school, 
            ch.child_year,
            ch.child_image,
            ch.child_register_date
        FROM child ch
        LEFT JOIN parent p ON ch.parent_id = p.parent_id
        ORDER BY ch.child_id ASC
    ";

    $result = $conn->query($sql);
    if ($result === FALSE) {
        logError("Fetch all query error: " . $conn->error . " | Query: $sql");
        sendJsonResponse(["success" => false, "error" => "Database error: " . $conn->error]);
    }

    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['child_kidNumber']) {
            $calculated_birthday = calculateBirthdayFromIC($row['child_kidNumber']);
            if ($calculated_birthday && $row['child_birthday'] !== $calculated_birthday) {
                $updateSql = "UPDATE child SET child_birthday = '$calculated_birthday' WHERE child_id = " . $conn->real_escape_string($row['child_id']);
                if (!$conn->query($updateSql)) {
                    logError("Birthday update error for child_id {$row['child_id']}: " . $conn->error . " | Query: $updateSql");
                }
                $row['child_birthday'] = $calculated_birthday;
            }
        }

        if ($row['child_birthday'] && $row['child_register_date']) {
            $birthYear = date("Y", strtotime($row['child_birthday']));
            $registerYear = date("Y", strtotime($row['child_register_date']));
            $calculatedYear = calculateYear($birthYear, $registerYear);

            if ($row['child_year'] != $calculatedYear) {
                $updateSql = "UPDATE child SET child_year = '$calculatedYear' WHERE child_id = " . $conn->real_escape_string($row['child_id']);
                if (!$conn->query($updateSql)) {
                    logError("Year update error for child_id {$row['child_id']}: " . $conn->error . " | Query: $updateSql");
                }
                $row['child_year'] = $calculatedYear;
            }
        }

        $registrations[] = $row;
    }

    sendJsonResponse($registrations);
} catch (Exception $e) {
    logError("Unexpected error: " . $e->getMessage());
    sendJsonResponse(["success" => false, "error" => "Unexpected server error: " . $e->getMessage()]);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>