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

    if (!isset($data['cart_items']) || !is_array($data['cart_items'])) {
        throw new Exception("Invalid cart items", 400);
    }

    $cartItems = $data['cart_items'];
    $conflicts = [];
    $validItems = [];

    // Check each (child_id, subject_id) for existing registration
    foreach ($cartItems as $item) {
        $child_id = $item['child_id'];
        $subject_id = $item['subject_id'];

        $stmt = $conn->prepare("
            SELECT rc.registration_id 
            FROM registration_class rc 
            JOIN class c ON rc.class_id = c.class_id 
            WHERE rc.child_id = ? AND c.subject_id = ?
        ");
        $stmt->bind_param("ii", $child_id, $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $conflicts[] = [
                'child_id' => $child_id,
                'subject_id' => $subject_id,
                'message' => "Child with ID $child_id has already registered for subject with ID $subject_id"
            ];
        } else {
            $validItems[] = $item;
        }
        $stmt->close();
    }

    echo json_encode([
        'success' => true,
        'message' => empty($conflicts) ? 'All items are valid for registration' : 'Some items are already registered, proceeding with remaining items',
        'conflicts' => $conflicts,
        'valid_items' => $validItems
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>