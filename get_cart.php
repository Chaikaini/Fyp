<?php
session_start();
header('Content-Type: application/json');
require_once('db_connect.php');

try {
    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }

    // Query cart data with necessary joins
    $stmt = $conn->prepare("
        SELECT 
            c.cart_id,
            c.parent_id,
            c.child_id,
            c.class_id,
            c.deleted,
            ch.child_name,
            cl.class_capacity,
            cl.class_enrolled,
            cl.part_id,
            cl.class_time,
            cl.year,
            cl.subject_id,
            cl.teacher_id,
            s.subject_name,
            s.subject_image,
            s.subject_price AS price,
            t.teacher_name
        FROM cart c
        LEFT JOIN child ch ON c.child_id = ch.child_id
        LEFT JOIN class cl ON c.class_id = cl.class_id
        LEFT JOIN subject s ON cl.subject_id = s.subject_id
        LEFT JOIN teacher t ON cl.teacher_id = t.teacher_id
        WHERE c.parent_id = ? AND c.deleted = 0
    ");

    $stmt->bind_param("i", $_SESSION['parent_id']);
    $stmt->execute();

    $result = $stmt->get_result();
    $cartItems = $result->fetch_all(MYSQLI_ASSOC);

    // Clean up the response
    $cleanedCartItems = array_map(function($item) {
        return [
            'cart_id' => $item['cart_id'],
            'subject_id' => $item['subject_id'],
            'subject_name' => $item['subject_name'],
            'subject_image' => $item['subject_image'],
            'child_id' => $item['child_id'],
            'child_name' => $item['child_name'],
            'price' => $item['price'],
            'class_id' => $item['class_id'],
            'class_capacity' => $item['class_capacity'],
            'class_enrolled' => $item['class_enrolled'],
            'part_id' => $item['part_id'],
            'class_time' => $item['class_time'],
            'year' => $item['year'],
            'teacher_id' => $item['teacher_id'],
            'teacher_name' => $item['teacher_name']
        ];
    }, $cartItems);

    echo json_encode([
        'status' => 'success',
        'cart' => $cleanedCartItems
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