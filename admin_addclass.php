<?php
header('Content-Type: application/json'); 

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "admin"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectid = $_POST["subjectid"];
    $classid = $_POST["classid"];
    $year = $_POST["year"];
    $part = $_POST["part"];
    $month = $_POST["month"];
    $time = $_POST["time"];
    $teacher = $_POST["teacher"];
    $capacity = (int)$_POST["capacity"]; //Maximum Capacity
    $status = $_POST["status"]; //  Status
    $enrollment = 0; 

    // check capacity is a positive integer
    if ($capacity <= 0) {
        echo json_encode(["success" => false, "message" => "Capacity must be a positive number."]);
        exit;
    }

    // check status 
    if (!in_array($status, ["available", "unavailable"])) {
        echo json_encode(["success" => false, "message" => "Invalid status value."]);
        exit;
    }

    $sql = "INSERT INTO admin_class (subject_id, class_id, year, part, month, time, teacher, enrolled, capacity, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    } else {
        $stmt->bind_param("sssssssiii", $subjectid, $classid, $year, $part, $month, $time, $teacher, $enrollment, $capacity, $status);

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