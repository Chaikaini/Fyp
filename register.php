<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_name = trim($_POST['username']);
    $parent_email = trim($_POST['email']);
    $parent_address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);
    $parent_gender = trim($_POST['gender']);
    $parent_relationship = trim($_POST['relationship']);
    $parent_password = trim($_POST['password']); // Trim password
    $confirm_password = trim($_POST['confirm_password']); // Trim confirm password

    if ($parent_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match!']);
        exit();
    }

    $hashed_password = password_hash($parent_password, PASSWORD_BCRYPT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO parent (parent_name, parent_email, parent_address, phone_number, parent_gender, parent_relationship, parent_password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $parent_name, $parent_email, $parent_address, $phone_number, $parent_gender, $parent_relationship, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . addslashes($conn->error)]);
    }

    $stmt->close();
}

$conn->close();
?>