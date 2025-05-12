<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Remove date range logic since search is not needed
$start_date = date("Y-m-01 00:00:00"); // First day of current month
$end_date = date("Y-m-d 23:59:59"); // Current date

$sql = "SELECT payment_id, parent_id, payment_method, payment_status, payment_time, payment_total_amount 
        FROM payment 
        WHERE payment_time BETWEEN ? AND ? 
        ORDER BY payment_id ASC"; // Changed to order by payment_id

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>