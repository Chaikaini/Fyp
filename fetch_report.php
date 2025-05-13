<?php
// Database configuration (use environment variables or config for production)
$servername = getenv('DB_HOST') ?: 'localhost'; // Fallback to localhost if not set
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'the seeds';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set date range for the current month
$start_date = date("Y-m-01 00:00:00"); // First day of current month
$end_date = date("Y-m-d 23:59:59"); // Current date

$sql = "SELECT p.payment_id, pr.parent_name, p.payment_method, p.payment_status, p.payment_time, p.payment_total_amount 
        FROM payment p
        JOIN parent pr ON p.parent_id = pr.parent_id
        WHERE p.payment_time BETWEEN ? AND ? 
        ORDER BY p.payment_id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Set JSON header and output data
header('Content-Type: application/json');
echo json_encode($data);

$stmt->close();
$conn->close();
?>