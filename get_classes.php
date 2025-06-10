<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "Connection failed: " . $conn->connect_error
    ]);
    exit;
}

// use JOIN check teacher_name，and get part_id = 1 or 2
$sql = "
    SELECT 
        c.*,
        t.teacher_name
    FROM class c
    LEFT JOIN teacher t ON c.teacher_id = t.teacher_id
    WHERE c.part_id IN (1, 2)
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "error" => "Query failed: " . $conn->error
    ]);
    exit;
}

$allClasses = $result->fetch_all(MYSQLI_ASSOC);

$partA = [];
$partB = [];

foreach ($allClasses as $class) {
    if ($class['part_id'] == 1) {
        $partA[] = $class;
    } elseif ($class['part_id'] == 2) {
        $partB[] = $class;
    }
}

$response = [
    "success" => true,
    "data" => [
        "partA" => $partA,
        "partB" => $partB
    ]
];

echo json_encode($response);
$conn->close();
?>
