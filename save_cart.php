<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    file_put_contents('debug.log', "save_cart.php: Starting\n", FILE_APPEND);
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    file_put_contents('debug.log', "save_cart.php: Connected to DB\n", FILE_APPEND);

    if (!isset($_SESSION['parent_id'])) {
        throw new Exception("Unauthorized", 401);
    }
    file_put_contents('debug.log', "save_cart.php: Session verified, Parent ID: " . $_SESSION['parent_id'] . "\n", FILE_APPEND);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg(), 400);
    }
    file_put_contents('debug.log', "save_cart.php: JSON decoded: " . print_r($data, true) . "\n", FILE_APPEND);

    $required = ['subject_id', 'subject_name', 'child_name', 'class_id', 'teacher_id', 'price'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("Missing field: $field", 400);
        }
    }

    $subject_id = trim($data['subject_id']);
    if (empty($subject_id)) {
        throw new Exception("Invalid subject_id", 400);
    }
    $class_id = trim($data['class_id']);
    if (empty($class_id)) {
        throw new Exception("Invalid class_id", 400);
    }
    $teacher_id = filter_var($data['teacher_id'], FILTER_VALIDATE_INT);
    if ($teacher_id === false || $teacher_id <= 0) {
        throw new Exception("Invalid teacher_id", 400);
    }
    file_put_contents('debug.log', "save_cart.php: Validated input: subject_id=$subject_id, class_id=$class_id, teacher_id=$teacher_id\n", FILE_APPEND);

    $stmt = $pdo->prepare("SELECT subject_name, subject_price FROM subject WHERE subject_id = ?");
    $stmt->execute([$subject_id]);
    $subject = $stmt->fetch();
    if (!$subject) {
        throw new Exception("Invalid subject_id: subject does not exist", 404);
    }
    file_put_contents('debug.log', "save_cart.php: Subject verified: " . print_r($subject, true) . "\n", FILE_APPEND);

    if ($data['subject_name'] !== $subject['subject_name']) {
        throw new Exception("Subject name mismatch", 400);
    }
    if (abs(floatval($data['price']) - floatval($subject['subject_price'])) > 0.01) {
        throw new Exception("Price mismatch", 400);
    }

    $stmt = $pdo->prepare("SELECT class_id, subject_id, teacher_id FROM class WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();
    if (!$class) {
        throw new Exception("Invalid class_id: class does not exist", 404);
    }
    file_put_contents('debug.log', "save_cart.php: Class verified: " . print_r($class, true) . "\n", FILE_APPEND);

    if ($class['subject_id'] !== $subject_id || $class['teacher_id'] != $teacher_id) {
        throw new Exception("Subject or teacher mismatch for the given class", 400);
    }

    $stmt = $pdo->prepare("SELECT teacher_id FROM teacher WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    if (!$stmt->fetch()) {
        throw new Exception("Invalid teacher_id: teacher does not exist", 404);
    }
    file_put_contents('debug.log', "save_cart.php: Teacher verified\n", FILE_APPEND);

    $stmt = $pdo->prepare("SELECT child_id FROM child WHERE child_name = ? AND parent_id = ?");
    $stmt->execute([$data['child_name'], $_SESSION['parent_id']]);
    $child = $stmt->fetch();
    if (!$child) {
        throw new Exception("Child not found", 404);
    }
    $child_id = $child['child_id'];
    file_put_contents('debug.log', "save_cart.php: Child verified: child_id=$child_id\n", FILE_APPEND);

    // Modified query to join with class table
    $stmt = $pdo->prepare("
        SELECT rc.registration_id 
        FROM registration_class rc
        JOIN class c ON rc.class_id = c.class_id
        WHERE rc.child_id = ? AND c.subject_id = ?
    ");
    $stmt->execute([$child_id, $subject_id]);
    if ($stmt->fetch()) {
        throw new Exception("Child already registered for this subject", 400);
    }
    file_put_contents('debug.log', "save_cart.php: Registration checked\n", FILE_APPEND);

    $stmt = $pdo->prepare("SELECT cart_id, deleted FROM cart WHERE parent_id = ? AND child_id = ? AND class_id = ?");
    $stmt->execute([$_SESSION['parent_id'], $child_id, $class_id]);
    $cartItem = $stmt->fetch();
    file_put_contents('debug.log', "save_cart.php: Cart checked: " . print_r($cartItem, true) . "\n", FILE_APPEND);

    if ($cartItem) {
        if ($cartItem['deleted'] == 0) {
            throw new Exception("Class already in cart", 400);
        } else {
            $stmt = $pdo->prepare("UPDATE cart SET deleted = 0 WHERE cart_id = ?");
            $stmt->execute([$cartItem['cart_id']]);
            // message  "Added to cart successfully"
            echo json_encode(['status' => 'success', 'message' => 'Added to cart successfully']);
            exit;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO cart (parent_id, child_id, class_id, deleted) VALUES (?, ?, ?, 0)");
    $stmt->execute([$_SESSION['parent_id'], $child_id, $class_id]);
    file_put_contents('debug.log', "save_cart.php: Inserted into cart\n", FILE_APPEND);

    echo json_encode(['status' => 'success', 'message' => 'Added to cart successfully']);

} catch (Exception $e) {
    file_put_contents('debug.log', "save_cart.php: Error: " . $e->getMessage() . "\n", FILE_APPEND);
    $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>