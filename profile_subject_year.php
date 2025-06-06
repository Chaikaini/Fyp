<?php

header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$sql = "SELECT DISTINCT year FROM subject ORDER BY year ASC";
$result = $conn->query($sql);

$years = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // get the number from "Year X" 
        $yearNum = intval(str_replace('Year ', '', $row['year']));
        if ($yearNum > 0) {
            $years[] = $yearNum;
        }
    }
    echo json_encode(['success' => true, 'years' => $years]);
} else {
    echo json_encode(['success' => false, 'error' => 'No years found']);
}

$conn->close();
?>