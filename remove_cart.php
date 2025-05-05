<?php
session_start();
header('Content-Type: application/json');
require_once('db_connect.php');

try {
    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON", 400);
    }

    $required = ['subject_id', 'child_name'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing field: " . $field, 400);
        }
    }

    $child_name = $data['child_name'];

    // 找到记录并软删除
    $stmt = $conn->prepare("UPDATE cart SET deleted = 1 WHERE parent_id = ? AND child_name = ? AND subject_id = ? AND deleted = 0");
    $stmt->bind_param("isi", $_SESSION['parent_id'], $child_name, $data['subject_id']);
    
    if (!$stmt->execute() || $stmt->affected_rows === 0) {
        throw new Exception("Cart item not found or already deleted", 404);
    }    

    echo json_encode([
        'status' => 'success',
        'message' => 'Removed from cart successfully'
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
