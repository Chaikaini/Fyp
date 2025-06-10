<?php
header('Content-Type: application/json');
session_start();

// get now  parent_id
$parent_id = $_SESSION['parent_id'] ?? '';

if (empty($parent_id)) {
    echo json_encode(["error" => "User is not logged in"]);
    exit();
}

// get subject_year
$subject_year = isset($_GET['subject_year']) ? (int)$_GET['subject_year'] : null;

// connection to the database
include 'db.php';


$sql = "SELECT child_id,child_name, child_year FROM child WHERE parent_id = ?";


if ($subject_year !== null) {
    $sql .= " AND child_year = ?";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL prepare error: " . $conn->error]);
    exit();
}


if ($subject_year !== null) {
    $stmt->bind_param("ii", $parent_id, $subject_year);
} else {
    $stmt->bind_param("i", $parent_id);
}

$stmt->execute();
$result = $stmt->get_result();

$children = [];

while ($row = $result->fetch_assoc()) {
    $children[] = $row;
}

if (empty($children)) {
    echo json_encode(["message" => "No children found"]);
} else {
    echo json_encode($children);
}

$stmt->close();
$conn->close();
?>