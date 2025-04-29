<?php

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get data
    $subject_id = $_POST['subject_id'];
    $subject_name = htmlspecialchars($_POST['subject_name'], ENT_QUOTES, 'UTF-8');
    $subject_price = floatval($_POST['subject_price']);
    $subject_image = htmlspecialchars($_POST['subject_image'], ENT_QUOTES, 'UTF-8');
    $subject_description = htmlspecialchars($_POST['subject_description'], ENT_QUOTES, 'UTF-8');

   
    if (empty($subject_id) || empty($subject_name) || empty($subject_price) || empty($subject_image) || empty($subject_description)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    // update
    $sql = "UPDATE subject SET subject_name = ?, subject_price = ?, subject_image = ?, subject_description = ? WHERE subject_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement.']);
        exit;
    }

    $stmt->bind_param("ssdsi", $subject_name, $subject_price, $subject_image, $subject_description, $subject_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>