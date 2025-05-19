<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    file_put_contents('debug.log', "update_cart.php: Starting\n", FILE_APPEND);
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    file_put_contents('debug.log', "update_cart.php: Connected to DB\n", FILE_APPEND);

    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }
    file_put_contents('debug.log', "update_cart.php: Session verified, Parent ID: " . $_SESSION['parent_id'] . "\n", FILE_APPEND);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg(), 400);
    }
    file_put_contents('debug.log', "update_cart.php: JSON decoded: " . print_r($data, true) . "\n", FILE_APPEND);

    $required = ['cart_id', 'action'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("Missing field: $field", 400);
        }
    }

    $cart_id = filter_var($data['cart_id'], FILTER_VALIDATE_INT);
    if ($cart_id === false || $cart_id <= 0) {
        throw new Exception("Invalid cart_id", 400);
    }
    $action = trim($data['action']);
    file_put_contents('debug.log', "update_cart.php: Action: $action, cart_id: $cart_id\n", FILE_APPEND);

    // Verify cart item belongs to the parent and join with class to get subject_id
    $stmt = $pdo->prepare("
        SELECT c.cart_id, c.child_id, c.class_id, cl.subject_id 
        FROM cart c
        JOIN class cl ON c.class_id = cl.class_id
        WHERE c.cart_id = ? AND c.parent_id = ? AND c.deleted = 0
    ");
    $stmt->execute([$cart_id, $_SESSION['parent_id']]);
    $cartItem = $stmt->fetch();
    if (!$cartItem) {
        throw new Exception("Cart item not found or not authorized", 404);
    }
    file_put_contents('debug.log', "update_cart.php: Cart item verified: " . print_r($cartItem, true) . "\n", FILE_APPEND);

    if ($action === 'update_child') {
        if (!isset($data['child_id']) || empty(trim($data['child_id']))) {
            throw new Exception("Missing child_id", 400);
        }

        $child_id = trim($data['child_id']);

        // Verify child belongs to the parent
        $stmt = $pdo->prepare("SELECT child_id FROM child WHERE child_id = ? AND parent_id = ?");
        $stmt->execute([$child_id, $_SESSION['parent_id']]);
        $child = $stmt->fetch();
        if (!$child) {
            throw new Exception("Child not found", 404);
        }
        file_put_contents('debug.log', "update_cart.php: Child verified: child_id=$child_id\n", FILE_APPEND);

        // Check if child is already registered for this subject
        $stmt = $pdo->prepare("
            SELECT rc.registration_id 
            FROM registration_class rc
            JOIN class c ON rc.class_id = c.class_id
            WHERE rc.child_id = ? AND c.subject_id = ?
        ");
        $stmt->execute([$child_id, $cartItem['subject_id']]);
        if ($stmt->fetch()) {
            throw new Exception("Child already registered for this subject", 400);
        }

        // Update child_id in cart
        $stmt = $pdo->prepare("UPDATE cart SET child_id = ? WHERE cart_id = ?");
        $stmt->execute([$child_id, $cart_id]);
        file_put_contents('debug.log', "update_cart.php: Updated child_id to $child_id\n", FILE_APPEND);

        echo json_encode(['status' => 'success', 'message' => 'Cart updated successfully']);
    } elseif ($action === 'remove') {
        // Soft delete cart item
        $stmt = $pdo->prepare("UPDATE cart SET deleted = 1 WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        file_put_contents('debug.log', "update_cart.php: Soft deleted cart_id $cart_id\n", FILE_APPEND);

        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
    } else {
        throw new Exception("Invalid action", 400);
    }

} catch (Exception $e) {
    file_put_contents('debug.log', "update_cart.php: Error: " . $e->getMessage() . "\n", FILE_APPEND);
    $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>