<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the_seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function dbConnect() {
    $conn = new mysqli("localhost", "root", "", "the_seeds");
    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
    }
    return $conn;
}
?>