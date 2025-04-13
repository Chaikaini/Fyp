<?php
header("Content-Type: application/json");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["child_kidNumber"])) {
    $kidNumber = $_POST["child_kidNumber"];

    $stmt = $conn->prepare("DELETE FROM child WHERE child_kidNumber = ?");
    $stmt->bind_param("s", $child_kidNumber); 

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}

$conn->close();
?>
