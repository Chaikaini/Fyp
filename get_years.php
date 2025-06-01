<?php
header('Content-Type: application/json');
include 'db_connect.php';

// Check if connection is established
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Query all unique year values
$sql = "SELECT DISTINCT year FROM subject ORDER BY year ASC";
$result = $conn->query($sql);

if (!$result) {
    error_log("Query failed: " . $conn->error);
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    $conn->close();
    exit;
}

$years = [];
while ($row = $result->fetch_assoc()) {
    $years[] = $row['year'];
}

// If no years found, optionally include a message
if (empty($years)) {
    error_log("No years found in subject table");
    echo json_encode(['error' => 'No years found']);
} else {
    echo json_encode($years);
}

// Cleanup
$result->free();
$conn->close();
?>