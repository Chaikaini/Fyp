<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    file_put_contents('debug.log', "remove_cart.php: Starting\n", FILE_APPEND);
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    file_put_contents('debug.log', "remove_cart.php: Connected to DB\n", FILE_APPEND);

    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg(), 400);
    }

    if (!isset($data['subject_id']) || !isset($data['child_name'])) {
        throw new Exception("Missing subject_id or child_name", 400);
    }

    $subject_id = trim($data['subject_id']);
    $child_name = trim($data['child_name']);

    // Fetch child_id
    $stmt = $pdo->prepare("SELECT child_id FROM child WHERE child_name = ? AND parent_id = ?");
    $stmt->execute([$child_name, $_SESSION['parent_id']]);
    $child = $stmt->fetch();
    if (!$child) {
        throw new Exception("Child not found", 404);
    }
    $child_id = $child['child_id'];
    file_put_contents('debug.log', "remove_cart.php: Child verified: child_name=$child_name, child_id=$child_id\n", FILE_APPEND);

    // Soft delete cart item
    $stmt = $pdo->prepare("UPDATE cart SET deleted = 1 WHERE parent_id = ? AND child_id = ? AND subject_id = ?");
    $stmt->execute([$_SESSION['parent_id'], $child_id, $subject_id]);
    if ($stmt->rowCount() === 0) {
        throw new Exception("Cart item not found", 404);
    }

    file_put_contents('debug.log', "remove_cart.php: Cart item deleted: subject_id=$subject_id, child_id=$child_id\n", FILE_APPEND);
    echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
} catch (Exception $e) {
    file_put_contents('debug.log', "remove_cart.php: Error: " . $e->getMessage() . "\n", FILE_APPEND);
    $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>