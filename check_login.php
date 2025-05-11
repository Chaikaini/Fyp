<?php
session_start();
$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['isLoggedIn' => false, 'error' => 'Database connection failed']);
    exit;
}
function getParentIdByEmail($conn, $email) {
    $sql = "SELECT parent_id FROM parent WHERE parent_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['parent_id'] : null;
}
header('Content-Type: application/json');
$isLoggedIn = false;
if (isset($_SESSION['parent_email']) && !empty($_SESSION['parent_email'])) {
    if (getParentIdByEmail($conn, $_SESSION['parent_email'])) {
        $isLoggedIn = true;
    }
}
echo json_encode(['isLoggedIn' => $isLoggedIn]);
$conn->close();
?>