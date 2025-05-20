<?php
// Database configuration (use environment variables or config for production)
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'the seeds';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Get month from query parameter or default to current month
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$start_date = date("Y-m-01 00:00:00", strtotime($month));
$end_date = date("Y-m-t 23:59:59", strtotime($month));

// Debug: Log the date range
error_log("Fetching payments for month: $month, Start: $start_date, End: $end_date");

// SQL query to fetch payment data
$sql = "SELECT p.payment_id, pr.parent_name, p.payment_method, p.payment_time, p.payment_total_amount 
        FROM payment p
        JOIN credit_cards cc ON p.credit_card_id = cc.credit_card_id
        JOIN parent pr ON cc.parent_id = pr.parent_id
        WHERE p.payment_time BETWEEN ? AND ? 
        ORDER BY p.payment_id ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Debug: Log the number of records fetched
error_log("Records fetched: " . count($data));

// Set JSON header and output data
header('Content-Type: application/json');
echo json_encode($data);

$stmt->close();
$conn->close();
?>