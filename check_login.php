<?php

session_start();


$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['isLoggedIn' => false, 'error' => 'Database connection failed']);
    exit;
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}


function getParentIdByEmail($conn, $email) {
    if (empty($email)) return null;
    $email = sanitizeInput($email);
    $sql = "SELECT parent_id FROM parent WHERE parent_email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return null;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['parent_id'] : null;
}


header('Content-Type: application/json');


$isLoggedIn = false;


if (isset($_SESSION['parent_email']) && !empty($_SESSION['parent_email'])) {
    $parentId = getParentIdByEmail($conn, $_SESSION['parent_email']);
    if ($parentId !== null) {
        $isLoggedIn = true;
    } else {
       
        unset($_SESSION['parent_email']);
    }
}


echo json_encode(['isLoggedIn' => $isLoggedIn]);


$conn->close();
?>