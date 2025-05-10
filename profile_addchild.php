<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "the seeds";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}


if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$parent_id = $_SESSION['parent_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_name      = $_POST['child_name'] ?? '';
    $child_gender    = $_POST['child_gender'] ?? '';
    $child_kidNumber = $_POST['child_kidNumber'] ?? '';
    $child_birthday  = $_POST['child_birthday'] ?? '';
    $child_school    = $_POST['child_school'] ?? '';
    $child_year      = $_POST['child_year'] ?? '';

    // 默认头像路径
    $child_image = 'img/user.jpg';

    // if image is uploaded, process it
    if (isset($_FILES['child_image']) && $_FILES['child_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName    = $_FILES['child_image']['tmp_name'];
        $imageName  = uniqid('child_', true) . '.' . pathinfo($_FILES['child_image']['name'], PATHINFO_EXTENSION);
        $uploadDir  = 'img/';  // 存入 img 目录
        $imagePath  = $uploadDir . $imageName;

       
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

       
        if (move_uploaded_file($tmpName, $imagePath)) {
            $child_image = $imagePath;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
            exit;
        }
    }

    // insert child info into database
    $sql = "INSERT INTO child (parent_id, child_name, child_gender, child_kidNumber, child_birthday, child_school, child_year, child_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
        exit;
    }

    $stmt->bind_param("isssssis", $parent_id, $child_name, $child_gender, $child_kidNumber, $child_birthday, $child_school, $child_year, $child_image);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Child profile added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add child']);
    }

    $stmt->close();
    $conn->close();
}
?>
