<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Teacher' || !isset($_SESSION['teacher_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;

    $name = $input['name'] ?? '';
    $gender = $input['gender'] ?? '';
    $email = $input['email'] ?? '';
    $phone = $input['phone_number'] ?? '';
    $address = $input['address'] ?? '';
    $status = $input['status'] ?? 'Active';

    // Check if image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = uniqid() . '-' . $_FILES['image']['name'];
        $imagePath = "uploads/teacher_images/" . $imageName;

        if (move_uploaded_file($imageTmp, $imagePath)) {
            // Update with new image
            $sql = "UPDATE teacher SET teacher_name = ?, teacher_gender = ?, teacher_email = ?, teacher_phone_number = ?, teacher_address = ?, teacher_status = ?, teacher_image = ? WHERE teacher_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $name, $gender, $email, $phone, $address, $status, $imagePath, $teacher_id);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
            exit;
        }
    } else {
        // Update without image
        $sql = "UPDATE teacher SET teacher_name = ?, teacher_gender = ?, teacher_email = ?, teacher_phone_number = ?, teacher_address = ?, teacher_status = ? WHERE teacher_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $gender, $email, $phone, $address, $status, $teacher_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

//load teacher profile data
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
