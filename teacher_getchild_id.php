<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debugging: Log received POST data
file_put_contents("debug.log", print_r($_POST, true), FILE_APPEND);

$class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
$child_name = isset($_POST['child_name']) ? $_POST['child_name'] : '';

if (empty($class_id) || empty($child_name)) {
    echo json_encode(["error" => "class_id and child_name are required."]);
    exit;
}

// Query to find child_id
$sql = "SELECT c.child_id 
        FROM registration_class rc
        JOIN child c ON rc.child_id = c.child_id
        WHERE rc.class_id = ? AND c.child_name = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $class_id, $child_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["child_id" => $row['child_id']]);
} else {
    echo json_encode(["error" => "No matching child found."]);
}

$stmt->close();
$conn->close();
?>