<?php
$servername = "127.0.0.1";
$username = "root";
$password = ""; 
$dbname = "the seeds";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

$conn->set_charset("utf8mb4");
?>