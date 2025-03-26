<?php
$servername = "127.0.0.1";
$username = "root";  // use database username
$password = "";      // use database password
$dbname = "profile"; // use databse name

// create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

}
