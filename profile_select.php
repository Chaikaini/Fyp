<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// make sure user has login
if (!isset($_SESSION['parent_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$parent_id = $_SESSION['parent_id'];

$sql = "SELECT child_id, child_name FROM child WHERE parent_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[] = [
        "id" => $row["child_id"],   
        "name" => $row["child_name"]
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    "parent_id" => $parent_id,
    "children" => $children
]);
?>
