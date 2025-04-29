<?php
// process_pending_pages.php

// 数据库连接
try {
    $pdo = new PDO("mysql:host=localhost;dbname=your_database", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Connection Failed: " . $e->getMessage());
    exit;
}

// 查询待处理的 subject_id
try {
    $stmt = $pdo->prepare("SELECT subject_id FROM pending_subject_pages");
    $stmt->execute();
    $pending_subjects = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("The Chat Data Detail Failed: " . $e->getMessage());
    exit;
}

foreach ($pending_subjects as $subject) {
    $subject_id = $subject['subject_id'];

    // 验证主题
    try {
        $stmt = $pdo->prepare("SELECT subject_name, year FROM subject WHERE subject_id = ?");
        $stmt->execute([$subject_id]);
        $subject = $stmt->fetch();
        if (!$subject) {
            error_log("The id $subject_id Not Search");
            continue;
        }
    } catch (PDOException $e) {
        error_log("Chat id $subject_id Failed: " . $e->getMessage());
        continue;
    }

    // 生成动态页面 URL
    $page_path = "generate_subject_page.php?id=" . $subject_id;

    // 更新 subject 表
    try {
        $update = "UPDATE subject SET page_generated = 1, page_path = ? WHERE subject_id = ?";
        $stmt = $pdo->prepare($update);
        $stmt->execute([$page_path, $subject_id]);
    } catch (PDOException $e) {
        error_log("Updated id $subject_id Failed: " . $e->getMessage());
        continue;
    }

    // 删除已处理记录
    try {
        $delete = "DELETE FROM pending_subject_pages WHERE subject_id = ?";
        $stmt = $pdo->prepare($delete);
        $stmt->execute([$subject_id]);
    } catch (PDOException $e) {
        error_log("Deleted id $subject_id Failed: " . $e->getMessage());
    }
}
?>