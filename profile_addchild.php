<?php
session_start();
header('Content-Type: application/json');

// check session
if (!isset($_SESSION['parent_id']) || empty($_SESSION['parent_id'])) {
    echo json_encode(["success" => false, "error" => "Session expired or user not logged in."]);
    exit;
}

$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "the seeds"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $child_name = $_POST['child_name'] ?? '';
    $child_gender = $_POST['child_gender'] ?? '';
    $child_kidNumber = $_POST['child_kidNumber'] ?? '';
    $child_birthday = $_POST['child_birthday'] ?? '';
    $child_school = $_POST['child_school'] ?? '';
    $child_year = $_POST['child_year'] ?? '';  
    $parent_id = $_SESSION['parent_id']; 

    
    $sql = "INSERT INTO child (parent_id, child_name, child_gender, child_kidNumber, child_birthday, child_school, child_year) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "error" => "Error preparing statement: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("isssssi", $parent_id, $child_name, $child_gender, $child_kidNumber, $child_birthday, $child_school, $child_year);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Child information added successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
