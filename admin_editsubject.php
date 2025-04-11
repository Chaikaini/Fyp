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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get data
    $subjectID = $_POST['subjectID'];
    $subject = htmlspecialchars($_POST['subject'], ENT_QUOTES, 'UTF-8');
    $year = htmlspecialchars($_POST['year'], ENT_QUOTES, 'UTF-8');
    $price = floatval($_POST['price']);
    $image = htmlspecialchars($_POST['image'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');

   
    if (empty($subjectID) || empty($subject) || empty($year) || empty($price) || empty($image) || empty($description)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    // update
    $sql = "UPDATE admin_subject SET subject = ?, year = ?, price = ?, image = ?, description = ? WHERE subject_ID = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement.']);
        exit;
    }

    $stmt->bind_param("sssdsi", $subject, $year, $price, $image, $description, $subjectID);

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