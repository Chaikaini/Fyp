<?php
$servername = "127.0.0.1";
$username = "root"; 
$password = ""; 
$dbname = "tuition_centre";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("database connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM cart_items";
$result = $conn->query($sql);


$cartItems = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
}


header('Content-Type: application/json');
echo json_encode($cartItems);


$conn->close();
?>
