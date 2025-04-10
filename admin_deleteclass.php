<?php
header('Content-Type: application/json'); // return JSON format

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["class_id"])) {
    $class_id = $conn->real_escape_string($_POST["class_id"]);
    $sql = "DELETE FROM admin_class WHERE class_id = '$class_id'";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Class deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request."]);
}

$conn->close();
?>

