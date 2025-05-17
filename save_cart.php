<?php
session_start();
header('Content-Type: application/json');

// Database connection using PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . $e->getMessage()]);
    exit;
}

try {
    // Verify user is logged in
    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }

    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON", 400);
    }

    // Validate required fields
    $required = ['subject_id', 'subject_name', 'child_name', 'class_id', 'teacher_id', 'price'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("Missing field: $field", 400);
        }
    }

    // Validate subject_id (integer)
    $subject_id = filter_var($data['subject_id'], FILTER_VALIDATE_INT);
    if ($subject_id === false || $subject_id <= 0) {
        throw new Exception("Invalid subject_id", 400);
    }

    // Clean class_id (string)
    $class_id = trim($data['class_id']);
    if (empty($class_id)) {
        throw new Exception("Invalid class_id", 400);
    }

    // Validate teacher_id (integer)
    $teacher_id = filter_var($data['teacher_id'], FILTER_VALIDATE_INT);
    if ($teacher_id === false || $teacher_id <= 0) {
        throw new Exception("Invalid teacher_id", 400);
    }

    // Verify subject_id exists
    $stmt = $pdo->prepare("SELECT subject_name, subject_price FROM subject WHERE subject_id = ?");
    $stmt->execute([$subject_id]);
    $subject = $stmt->fetch();
    if (!$subject) {
        throw new Exception("Invalid subject_id: subject does not exist", 404);
    }

    // Verify subject_name and price
    if ($data['subject_name'] !== $subject['subject_name']) {
        throw new Exception("Subject name mismatch", 400);
    }
    if (abs(floatval($data['price']) - floatval($subject['subject_price'])) > 0.01) {
        throw new Exception("Price mismatch", 400);
    }

    // Verify class_id exists and matches subject_id and teacher_id
    $stmt = $pdo->prepare("SELECT class_id, subject_id, teacher_id FROM class WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();
    if (!$class) {
        throw new Exception("Invalid class_id: class does not exist", 404);
    }
    if ($class['subject_id'] != $subject_id || $class['teacher_id'] != $teacher_id) {
        throw new Exception("Subject or teacher mismatch for the given class", 400);
    }

    // Get child_id
    $stmt = $pdo->prepare("SELECT child_id FROM child WHERE child_name = ? AND parent_id = ?");
    $stmt->execute([$data['child_name'], $_SESSION['parent_id']]);
    $child = $stmt->fetch();
    if (!$child) {
        throw new Exception("Child not found", 404);
    }
    $child_id = $child['child_id'];

    // Check registration
    $stmt = $pdo->prepare("SELECT registration_id FROM registration_class WHERE child_id = ? AND subject_id = ?");
    $stmt->execute([$child_id, $subject_id]);
    if ($stmt->fetch()) {
        throw new Exception("Child already registered for this subject", 400);
    }

    // Check cart
    $stmt = $pdo->prepare("SELECT cart_id, deleted FROM cart WHERE parent_id = ? AND child_id = ? AND class_id = ? AND subject_id = ?");
    $stmt->execute([$_SESSION['parent_id'], $child_id, $class_id, $subject_id]);
    $cartItem = $stmt->fetch();
    if ($cartItem) {
        if ($cartItem['deleted'] == 0) {
            throw new Exception("Class already in cart", 400);
        } else {
            $stmt = $pdo->prepare("UPDATE cart SET deleted = 0 WHERE cart_id = ?");
            $stmt->execute([$cartItem['cart_id']]);
            echo json_encode(['status' => 'success', 'message' => 'Cart item restored']);
            exit;
        }
    }

    // Insert into cart (include subject_id)
    $stmt = $pdo->prepare("INSERT INTO cart (parent_id, child_id, class_id, subject_id, deleted) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$_SESSION['parent_id'], $child_id, $class_id, $subject_id]);

    echo json_encode(['status' => 'success', 'message' => 'Added to cart successfully']);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>