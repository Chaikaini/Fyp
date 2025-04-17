<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

if (!isset($_SESSION['parent_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$parent_id = $_SESSION['parent_id'];

$sql = "
SELECT 
    c.child_name, 
    s.subject_name,
    p.payment_total_amount,
    p.payment_method,
    p.payment_time
FROM registration_class rc
JOIN child c ON rc.child_id = c.child_id
JOIN class cls ON rc.class_id = cls.class_id
JOIN subject s ON cls.subject_id = s.subject_id
JOIN payment p ON rc.payment_id = p.payment_id
WHERE rc.parent_id = ?
ORDER BY p.payment_time DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "SQL prepare failed: " . $conn->error]));
}

$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = [
        "child_name" => $row["child_name"],
        "subject_name" => $row["subject_name"],
        "payment_total_amount" => $row["payment_total_amount"],
        "payment_method" => $row["payment_method"],
        "payment_time" => $row["payment_time"]
    ];
}

$stmt->close();
$conn->close();

if (!empty($history)) {
    echo json_encode(["status" => "success", "data" => $history]);
} else {
    echo json_encode(["status" => "error", "message" => "No payment history found"]);
}
?>
