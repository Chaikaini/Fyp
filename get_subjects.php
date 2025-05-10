<?php
header('Content-Type: application/json');

try {
    // 正确的PDO连接方式
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM subject");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($subjects);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>