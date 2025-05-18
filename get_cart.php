<?php
session_start();
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    file_put_contents('debug.log', "get_cart.php: Connected to DB\n", FILE_APPEND);

    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }

    $stmt = $pdo->prepare("
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
    $stmt->execute([$_SESSION['parent_id']]);
    $cartItems = $stmt->fetchAll();
    file_put_contents('debug.log', "get_cart.php: Fetched cart items: " . print_r($cartItems, true) . "\n", FILE_APPEND);

    $cleanedCartItems = array_map(function($item) {
        return [
            'cart_id' => $item['cart_id'],
            'subject_id' => $item['subject_id'],
            'subject_name' => $item['subject_name'],
            'subject_image' => $item['subject_image'] ?? 'img/default-subject.jpg',
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
    file_put_contents('debug.log', "get_cart.php: Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>