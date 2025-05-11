<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$parent_id = $_SESSION['parent_id'];

$sql = "SELECT parent_name, ic_number, parent_email, parent_address, phone_number, phone_number2, parent_relationship, parent_gender, parent_password, 
       parent_image, parent_name2, parent_relationship2, parent_num2
        FROM parent 
        WHERE parent_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode(['status' => 'success', 'data' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$stmt->close();
$conn->close();
?>
