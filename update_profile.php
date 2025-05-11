<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$parent_id = $_SESSION['parent_id'];

// processing form data
$parent_name = $_POST['parent_name'] ?? '';
$parent_gender = $_POST['parent_gender'] ?? '';
$ic_number = $_POST['ic_number'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$phone_number2 = $_POST['phone_number2'] ?? '';
$parent_relationship = $_POST['parent_relationship'] ?? '';
$parent_address = $_POST['parent_address'] ?? '';
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

$parent_name2 = $_POST['parent_name2'] ?? null;
$parent_relationship2 = $_POST['parent_relationship2'] ?? null;
$parent_num2 = $_POST['parent_num2'] ?? null;

$parent_image_path = null;

if (isset($_FILES['parent_image']) && $_FILES['parent_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/parent_images/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = uniqid() . '-' . basename($_FILES['parent_image']['name']);
    $imageTmp = $_FILES['parent_image']['tmp_name'];
    $parent_image_path = $uploadDir . $imageName;

    if (!move_uploaded_file($imageTmp, $parent_image_path)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
        exit;
    }
}

$conn->begin_transaction();

try {
    if (!empty($current_password) && !empty($new_password)) {
        $stmt = $conn->prepare("SELECT parent_password FROM parent WHERE parent_id = ?");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($current_password, $user['parent_password'])) {
            throw new Exception('Current password is incorrect');
        }

        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE parent SET parent_password = ? WHERE parent_id = ?");
        $stmt->bind_param("si", $hashed_password, $parent_id);
        $stmt->execute();
        $stmt->close();
    }

    // update with image
    if ($parent_image_path) {
        $stmt = $conn->prepare("
            UPDATE parent 
            SET parent_name=?, parent_gender=?, ic_number=?, phone_number=?, phone_number2=?, parent_relationship=?, parent_address=?,
                parent_name2=?, parent_relationship2=?, parent_num2=?, parent_image=?
            WHERE parent_id=?
        ");
        $stmt->bind_param(
            "sssssssssssi",
            $parent_name,
            $parent_gender,
            $ic_number,
            $phone_number,
            $phone_number2,
            $parent_relationship,
            $parent_address,
            $parent_name2,
            $parent_relationship2,
            $parent_num2,
            $parent_image_path,
            $parent_id
        );
    } else {// update without image
        $stmt = $conn->prepare("
            UPDATE parent 
            SET parent_name=?, parent_gender=?, ic_number=?, phone_number=?, phone_number2=?, parent_relationship=?, parent_address=?,
                parent_name2=?, parent_relationship2=?, parent_num2=?
            WHERE parent_id=?
        ");
        $stmt->bind_param(
            "ssssssssssi",
            $parent_name,
            $parent_gender,
            $ic_number,
            $phone_number,
            $phone_number2,
            $parent_relationship,
            $parent_address,
            $parent_name2,
            $parent_relationship2,
            $parent_num2,
            $parent_id
        );
    }

    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
