<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure no output before JSON
ob_start();

// Database configuration (use environment variables or config for production)
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'the seeds';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    ob_end_flush();
    exit;
}

// Verify table existence
$result = $conn->query("SHOW TABLES LIKE 'child'");
if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Table 'child' does not exist"]);
    ob_end_flush();
    exit;
}

// Verify column existence
$required_columns = ['child_id', 'child_name', 'child_gender', 'child_kidNumber', 'child_birthday', 'child_register_date'];
$columns = [];
$result = $conn->query("SHOW COLUMNS FROM child");
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}
$missing_columns = array_diff($required_columns, $columns);
if (!empty($missing_columns)) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Missing columns in 'child': " . implode(", ", $missing_columns)]);
    ob_end_flush();
    exit;
}

// Get month from query parameter or default to current month
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$start_date = date("Y-m-01 00:00:00", strtotime($month));
$end_date = date("Y-m-t 23:59:59", strtotime($month));

// Debug: Log the date range
error_log("Fetching children for month: $month, Start: $start_date, End: $end_date");

$sql = "SELECT child_id AS id, child_name AS name, child_gender AS gender, child_kidNumber AS kidNumber, 
        child_birthday AS birthday, child_register_date AS join_date 
        FROM child 
        WHERE child_register_date BETWEEN ? AND ? 
        ORDER BY child_id ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "SQL Error: " . $conn->error]);
    ob_end_flush();
    exit;
}

$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

// Debug: Log the number of records fetched
error_log("Records fetched: " . count($children));

// Set JSON header and output data
header('Content-Type: application/json');
echo json_encode($children);

$stmt->close();
$conn->close();
ob_end_flush();
?>