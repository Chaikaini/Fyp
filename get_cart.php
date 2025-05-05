<?php
session_start();
header('Content-Type: application/json');
require_once('db_connect.php');

try {
    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }

    // 查询购物车数据，包括 class_capacity、class_enrolled、child_name 和 subject_image
    $stmt = $conn->prepare("
        SELECT c.*, s.subject_name, s.subject_image, ch.child_name, cl.class_capacity, cl.class_enrolled 
        FROM cart c
        LEFT JOIN subject s ON c.subject_id = s.subject_id
        LEFT JOIN child ch ON c.child_id = ch.child_id
        LEFT JOIN class cl ON c.class_id = cl.class_id
        WHERE c.parent_id = ? AND c.deleted = 0
    ");
    $stmt->bind_param("i", $_SESSION['parent_id']);
    $stmt->execute();

    $result = $stmt->get_result();
    $cartItems = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'status' => 'success',
        'cart' => $cartItems
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
