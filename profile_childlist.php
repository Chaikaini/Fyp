<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// check parent login
if (!isset($_SESSION['parent_id'])) { 
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$parent_id = $_SESSION['parent_id']; 

// check parent child information
$sql = "SELECT child_id, child_name, child_gender, child_kidNumber, child_birthday, child_school, child_year, child_image FROM child WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id); 
$stmt->execute();
$result = $stmt->get_result();

$children = [];
while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

if (!empty($children)) {
    echo json_encode(['status' => 'success', 'data' => $children]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No child information yet']);
}

$stmt->close();
$conn->close();
?>
