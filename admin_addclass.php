<?php
header('Content-Type: application/json'); // 告诉浏览器返回的是 JSON

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
    $enrollment = $_POST["enrollment"];
    $status = $_POST["status"];

    $sql = "INSERT INTO admin_class (subject_id, class_id, year, part, month, time, teacher, capacity, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    } else {
        $stmt->bind_param("sssssssss", $subjectid, $classid, $year, $part, $month, $time, $teacher, $enrollment, $status);

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
