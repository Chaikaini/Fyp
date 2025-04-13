<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = filter_var($_POST['class_id'], FILTER_SANITIZE_STRING);

    // check class_id exist or not
    $checkClassQuery = "SELECT class_capacity FROM class WHERE class_id = ?";
    $stmt = $conn->prepare($checkClassQuery);
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Class ID not found."]);
        exit;
    }

    $classData = $result->fetch_assoc();
    $class_capacity = $classData['class_capacity'];

    // go oredrs database to get the new enrolled 
    $conn->select_db($order_dbname);
    $orderQuery = "SELECT COUNT(*) as total FROM orders WHERE class_id = ?";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $enrolled = $result['total'];

    // back to admin database，update enrolled
    $conn->select_db($admin_dbname);
    $updateQuery = "UPDATE class SET class_enrolled = ? WHERE class_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("is", $class_enrolled, $class_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "class_enrolled" => $class_enrolled]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
