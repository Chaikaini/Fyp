<?php
header('Content-Type: application/json'); 

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "the seeds"; 


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_id = $_POST["subject_id"];
    $class_id = $_POST["class_id"];
    $year = $_POST["year"];
    $part = $_POST["part"];
    $month = $_POST["month"];
    $class_time = $_POST["class_time"];
    $teacher = $_POST["teacher"];
    $class_capacity = (int)$_POST["class_capacity"]; //Maximum Capacity
    $class_status = $_POST["class_status"]; //  Status
    $enrollment = 0; 

    // check capacity is a positive integer
    if ($class_capacity <= 0) {
        echo json_encode(["success" => false, "message" => "Capacity must be a positive number."]);
        exit;
    }

    // check status 
    if (!in_array($class_status, ["available", "unavailable"])) {
        echo json_encode(["success" => false, "message" => "Invalid status value."]);
        exit;
    }

    $sql = "INSERT INTO class (subject_id, class_id, year, part, month, class_time, teacher, class_enrolled, class_capacity, class_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    } else {
        $stmt->bind_param("sssssssiis", $subject_id, $class_id, $year, $part, $month, $class_time, $teacher, $class_enrolled, $class_capacity, $class_status);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Class added successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
        }

        $stmt->close();
    }
}

$conn->close();
?>