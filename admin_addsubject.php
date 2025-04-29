您说：
<?php
session_start(); 
header('Content-Type: application/json'); 

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
    
    // get the admin_id from session
    if (!isset($_SESSION["admin_id"])) {
        echo json_encode(["success" => false, "error" => "Admin not logged in."]);
        exit;
    }

    $admin_id = $_SESSION["admin_id"];
    $subject_id = $_POST["subject_id"];
    $subject_name = $_POST["subject_name"]; 
    $subject_price = $_POST["subject_price"];
    $subject_image = $_POST["subject_image"];
    $subject_description = $_POST["subject_description"];

    $stmt = $conn->prepare("INSERT INTO subject (admin_id, subject_id, subject_name, subject_price, subject_image, subject_description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $admin_id, $subject_id, $subject_name, $subject_price, $subject_image, $subject_description);

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

