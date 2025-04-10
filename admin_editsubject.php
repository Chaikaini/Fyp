<?php
// filepath: c:\xampp\htdocs\TWP-Project\admin_editsubject.php
include 'db_connection.php'; // 确保数据库连接文件正确

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $subjectID = $_POST['subjectID'];
    $subject = $_POST['subject'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $description = $_POST['description'];

    // 检查是否所有字段都存在
    if (empty($subjectID) || empty($subject) || empty($year) || empty($price) || empty($image) || empty($description)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    // 更新数据库
    $sql = "UPDATE admin_subject SET subject = ?, year = ?, price = ?, image = ?, description = ? WHERE subject_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $subject, $year, $price, $image, $description, $subjectID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>