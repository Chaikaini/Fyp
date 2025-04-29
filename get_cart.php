<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();

// 验证登录状态
if (!isset($_SESSION['parent_id'])) {
    echo json_encode(['status' => 'error', 'message' => '请先登录']);
    exit();
}

include('db_connect.php');
$conn = dbConnect();

try {
    // 只获取当前用户的购物车项目
    $sql = "SELECT 
            id, subject_id, subject as subject_name, price, 
            child_name, image, teacher, time, class_id, capacity
            FROM cart_items 
            WHERE parent_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['parent_id']);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $cart = [];
    
    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'cart' => $cart,
        'count' => count($cart)
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>