<?php
header('Content-Type: application/json'); 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("POST data: " . json_encode($_POST)); 

    $child_name = $_POST['child_name'] ?? '';
    $child_gender = $_POST['child_gender'] ?? '';
    $child_kidNumber = $_POST['child_kidNumber'] ?? '';
    $child_birthday = $_POST['child_birthday'] ?? '';
    $child_school = $_POST['child_school'] ?? '';
    $child_year = isset($_POST['child_year']) && is_numeric($_POST['child_year']) ? intval($_POST['child_year']) : null;
    $child_id = isset($_POST['child_id']) ? intval($_POST['child_id']) : null;

    // check child_id empty or not
    error_log("Received child_id: " . $child_id);
if (empty($child_id)) {
    echo json_encode(["success" => false, "error" => "Invalid child_id."]);
    exit;
}


    // check child_id is exist
    $check_sql = "SELECT * FROM child WHERE child_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt === false) {
        echo json_encode(["success" => false, "error" => "Error preparing check statement: " . $conn->error]);
        exit;
    }
    $check_stmt->bind_param("i", $child_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "child_id does not exist."]);
        exit;
    }
    $check_stmt->close();

    // update data
    $sql = "UPDATE child SET child_name = ?, child_gender = ?, child_kidNumber = ?, child_birthday = ?, child_school = ?, child_year = ? 
    WHERE child_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "error" => "Error preparing statement: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssssi", $child_name, $child_gender, $child_kidNumber, $child_birthday, $child_school, $child_year, $child_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Child information updated successfully!"]);
    } else {
        error_log("SQL Error: " . $stmt->error); 
        echo json_encode(["success" => false, "error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}
?>