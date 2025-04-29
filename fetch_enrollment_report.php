<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "order";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
} else {
    $start_date = date("Y-m-01");
    $end_date = date("Y-m-d");
}

$sql = "SELECT id, student_name, teacher_name, order_date 
        FROM orders 
        WHERE course_name LIKE '%Math%' AND order_date BETWEEN ? AND ? 
        ORDER BY order_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
