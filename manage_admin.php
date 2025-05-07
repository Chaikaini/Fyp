<?php
session_start();

if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if (isset($_GET['action']) && $_GET['action'] === 'getAdmins') {
    $result = $conn->query("SELECT teacher_id AS id, teacher_name AS name, teacher_gender AS gender, teacher_email AS email, teacher_phone_number AS phone, teacher_address AS address, teacher_join_date AS join_date, teacher_status AS status FROM teacher");

    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }

    echo json_encode(['success' => true, 'admins' => $admins, 'userRole' => $_SESSION['role']]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputJSON = file_get_contents("php://input");
    $postData = json_decode($inputJSON, true);

    if ($postData["action"] === "addAdmin") {
        $teacher_name = $postData["name"];
        $teacher_gender = $postData["gender"];
        $teacher_email = $postData["email"];
        $teacher_phone = $postData["phone"];
        $teacher_address = $postData["address"];
        $teacher_join_date = $postData["join_date"];
        $teacher_status = $postData["status"];
        $teacher_password = $postData["password"];

        if (empty($teacher_password)) {
            $teacher_password = "admin123";
        }

        $hashed_password = password_hash($teacher_password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO teacher 
            (teacher_name, teacher_gender, teacher_email, teacher_phone_number, teacher_address, teacher_join_date, teacher_status, teacher_password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $teacher_name, $teacher_gender, $teacher_email, $teacher_phone, $teacher_address, $teacher_join_date, $teacher_status, $hashed_password);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }
        exit;
    }

    if ($postData["action"] === "editAdmin") {
        $teacher_id = $postData["id"];
        $teacher_name = $postData["name"];
        $teacher_gender = $postData["gender"];
        $teacher_phone = $postData["phone"];
        $teacher_address = $postData["address"];
        $teacher_join_date = $postData["join_date"];
        $teacher_status = $postData["status"];

        $stmt = $conn->prepare("UPDATE teacher 
            SET teacher_name = ?, teacher_gender = ?, teacher_phone_number = ?, teacher_address = ?, teacher_join_date = ?, teacher_status = ?
            WHERE teacher_id = ?");
        $stmt->bind_param("ssssssi", $teacher_name, $teacher_gender, $teacher_phone, $teacher_address, $teacher_join_date, $teacher_status, $teacher_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
        }
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'searchAdmins' && isset($_GET['query'])) {
    $query = $conn->real_escape_string($_GET['query']);
    $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

    $sql = "SELECT teacher_id AS id, teacher_name AS name, teacher_gender AS gender, teacher_email AS email, teacher_phone_number AS phone, teacher_address AS address, teacher_join_date AS join_date, teacher_status AS status  
            FROM teacher 
            WHERE teacher_name LIKE '%$query%'";

    if (!empty($status)) {
        $sql .= " AND teacher_status = '$status'";
    }

    $result = $conn->query($sql);
    $admins = [];

    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }

    echo json_encode(['success' => true, 'admins' => $admins, 'userRole' => $_SESSION['role']]);
    exit;
}

$conn->close();
?>