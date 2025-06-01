<?php
ob_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

include 'db_connect.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log("Starting subject.php");

$year = isset($_GET['year']) ? trim($_GET['year']) : '';
error_log("Received year: '$year'");

$subjects = [];

if (empty($year)) {
    error_log("Year is empty");
    echo json_encode($subjects);
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
    WHERE s.year = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    ob_end_flush();
    exit;
}

$stmt->bind_param("s", $year);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
    ob_end_flush();
    exit;
}

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['page'] = 'generate_subject_page.php?id=' . $row['subject_id'];
    $subjects[] = $row;
}

error_log("Subjects found: " . count($subjects));
echo json_encode($subjects, JSON_PRETTY_PRINT);

$stmt->close();
$conn->close();
ob_end_flush();
?>