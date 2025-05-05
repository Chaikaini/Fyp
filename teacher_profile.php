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

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Teacher' || !isset($_SESSION['teacher_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

// process update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name'] ?? '';
    $gender  = $_POST['gender'] ?? '';
    $email   = $_POST['email'] ?? '';
    $phone   = $_POST['phone_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $status  = 'Active'; 

   
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp  = $_FILES['image']['tmp_name'];
        $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
        $uploadDir = "uploads/teacher_images/";
        $imagePath = $uploadDir . $imageName;

        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($imageTmp, $imagePath)) {
            // update with image
            $sql = "UPDATE teacher 
                    SET teacher_name = ?, teacher_gender = ?,  teacher_phone_number = ?, teacher_address = ?, teacher_status = ?, teacher_image = ?
                    WHERE teacher_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $name, $gender, $phone, $address, $status, $imagePath, $teacher_id);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
            exit;
        }
    } else {
        // no update image 
        $sql = "UPDATE teacher 
                SET teacher_name = ?, teacher_gender = ?,  teacher_phone_number = ?, teacher_address = ?, teacher_status = ?
                WHERE teacher_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $gender, $phone, $address, $status, $teacher_id);
    }

    // run the update query
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// get teacher profile
$sql = "SELECT 
            teacher_name AS name, 
            teacher_gender AS gender, 
            teacher_email AS email, 
            teacher_phone_number AS phone_number,
            teacher_address AS address,
            teacher_image AS image,
            teacher_status AS status
        FROM teacher 
        WHERE teacher_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode(['status' => 'success', 'data' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}

$stmt->close();
$conn->close();
?>
