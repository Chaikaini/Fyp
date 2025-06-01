<?php
ob_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

include 'db_connect.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log("Starting search.php");

$query = isset($_GET['query']) ? '%' . trim($_GET['query']) . '%' : '';
error_log("Received query: $query");
$results = [];

if (empty(trim($_GET['query']))) {
    error_log("Query is empty");
    echo json_encode($results);
    ob_end_flush();
    exit;
}

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    echo json_encode(['error' => 'Database connection failed']);
    ob_end_flush();
    exit;
}

$sql = "SELECT 
        s.subject_id,
        s.subject_name as name,
        s.subject_image as image,
        s.subject_price as price,
        s.year
    FROM subject s
    WHERE s.subject_name LIKE ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    ob_end_flush();
    exit;
}

$stmt->bind_param("s", $query);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
    ob_end_flush();
    exit;
}

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['page'] = 'generate_subject_page.php?id=' . $row['subject_id'];
    $results[] = $row;
}

error_log("Search results found: " . count($results));
echo json_encode($results, JSON_PRETTY_PRINT);

$stmt->close();
$conn->close();
ob_end_flush();
?>