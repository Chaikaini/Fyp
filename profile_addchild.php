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

    // default image path
    $child_image = 'img/user.jpg';

    // handle image upload
    if (isset($_FILES['child_image']) && $_FILES['child_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/child/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tmpName = $_FILES['child_image']['tmp_name'];
        $fileName = uniqid('child_', true) . '.' . pathinfo($_FILES['child_image']['name'], PATHINFO_EXTENSION);
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $destination)) {
            $child_image = $destination;
        }
    }

    $sql = "INSERT INTO child (parent_id, child_name, child_gender, child_kidNumber, child_birthday, child_school, child_year, child_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "error" => "Error preparing statement: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("isssssis", $parent_id, $child_name, $child_gender, $child_kidNumber, $child_birthday, $child_school, $child_year, $child_image);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Child information added successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
