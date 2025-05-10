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
    $child_name = $_POST['child_name'] ?? '';
    $child_gender = $_POST['child_gender'] ?? '';
    $child_kidNumber = $_POST['child_kidNumber'] ?? '';
    $child_birthday = $_POST['child_birthday'] ?? '';
    $child_school = $_POST['child_school'] ?? '';
    $child_year = isset($_POST['child_year']) && is_numeric($_POST['child_year']) ? intval($_POST['child_year']) : null;
    $child_id = isset($_POST['child_id']) ? intval($_POST['child_id']) : null;

    if (empty($child_id)) {
        echo json_encode(["success" => false, "error" => "Invalid child_id."]);
        exit;
    }

    // check child_id exists
    $check_sql = "SELECT * FROM child WHERE child_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $child_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "child_id does not exist."]);
        exit;
    }
    $check_stmt->close();

    // Handle image upload if present
$imagePath = null;
if (isset($_FILES['child_image']) && $_FILES['child_image']['error'] === UPLOAD_ERR_OK) {
    // Create base uploads directory if it doesn't exist
    if (!file_exists('uploads')) {
        mkdir('uploads', 0755);
    }
    
    // Create child_images subdirectory if it doesn't exist
    $uploadDir = "uploads/child_images/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    
    $imageName = uniqid() . '-' . basename($_FILES['child_image']['name']);
    $imageTmp = $_FILES['child_image']['tmp_name'];
    $imagePath = $uploadDir . $imageName;

    if (!move_uploaded_file($imageTmp, $imagePath)) {
        echo json_encode(["success" => false, "error" => "Failed to upload image: " . error_get_last()['message']]);
        exit;
    }
}
// update with image
    if ($imagePath) {
        $sql = "UPDATE child SET child_name = ?, child_gender = ?, child_kidNumber = ?, child_birthday = ?, child_school = ?, child_year = ?, child_image = ? WHERE child_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $child_name, $child_gender, $child_kidNumber, $child_birthday, $child_school, $child_year, $imagePath, $child_id);
    } else {// no update with image
        $sql = "UPDATE child SET child_name = ?, child_gender = ?, child_kidNumber = ?, child_birthday = ?, child_school = ?, child_year = ? WHERE child_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $child_name, $child_gender, $child_kidNumber, $child_birthday, $child_school, $child_year, $child_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Child information updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
