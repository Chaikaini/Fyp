<?php
header('Content-Type: application/json');

try {
    // correct connection
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM subject");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($subjects);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>