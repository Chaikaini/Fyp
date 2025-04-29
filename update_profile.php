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
$data = json_decode(file_get_contents("php://input"), true);

// get data
$parent_name = $data['parent_name'];
$parent_gender = $data['parent_gender'];
$ic_number = $data['ic_number'];
$phone_number = $data['phone_number'];
$phone_number2 = $data['phone_number2'];
$parent_relationship = $data['parent_relationship'];
$parent_address = $data['parent_address'];
$current_password = $data['current_password'];
$new_password = $data['new_password'] ?? '';

// add contact information
$parent_name2 = $data['parent_name2'] ?? null;
$parent_relationship2 = $data['parent_relationship2'] ?? null;
$parent_num2 = $data['parent_num2'] ?? null;

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
