<?php
header('Content-Type: application/json'); 

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "admin"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $subjectID = $_POST["subjectID"];
    $subject = $_POST["subject"];
    $year = $_POST["year"];
    $price = $_POST["price"];
    $image = $_POST["image"];
    $description = $_POST["description"];

   
    $stmt = $conn->prepare("INSERT INTO admin_subject (subject_ID, subject, year, price, image, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $subjectID, $subject, $year, $price, $image, $description);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Subject added successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}

$conn->close();
?>
