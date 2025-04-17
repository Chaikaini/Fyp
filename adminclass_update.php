<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = filter_var($_POST['class_id'], FILTER_SANITIZE_STRING);

    // check if class_id is provided
    $checkClassQuery = "SELECT class_capacity FROM class WHERE class_id = ?";
    $stmt = $conn->prepare($checkClassQuery);
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Class ID not found."]);
        exit;
    }

    // 从 registration_class get number of students enrolled in the class
    $countQuery = "SELECT COUNT(*) AS total FROM registration_class WHERE class_id = ?";
    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $class_enrolled = $result['total'];

    // update class table in the  class_enrolled 
    $updateQuery = "UPDATE class SET class_enrolled = ? WHERE class_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("is", $class_enrolled, $class_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "class_enrolled" => $class_enrolled]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
