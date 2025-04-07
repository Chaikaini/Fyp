<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profile";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// make sure user has login
if (!isset($_SESSION['email'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$email = $_SESSION['email']; 
$sql = "SELECT name FROM childreninfo WHERE email = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[] = ["name" => $row["name"]];
}


$stmt->close();
$conn->close();


echo json_encode([
    "email" => $email,
    "children" => $children
]);
?>