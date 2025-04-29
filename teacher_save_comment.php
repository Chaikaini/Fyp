<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['teacher_id'])) {
        echo json_encode(["error" => "No teacher ID in session"]);
        exit;
    }

    $teacher_id = intval($_SESSION['teacher_id']);
    $child_id = intval($_POST['child_id']);
    $class_id = $_POST['class_id'];

    if (empty($teacher_id) || empty($child_id) || empty($class_id)) {
        echo json_encode(["error" => "Missing data"]);
        exit;
    }

    //  check is Save or Get
    if (isset($_POST['comment_text'])) {
        // save Comment 
        $comment_text = $_POST['comment_text'];
        $created_at = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO teacher_comment (teacher_id, child_id, class_id, teacher_comment_text, teacher_comment_created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $teacher_id, $child_id, $class_id, $comment_text, $created_at);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Comment saved successfully."]);
        } else {
            echo json_encode(["error" => "Failed to save comment: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        // read Comment 
        $stmt = $conn->prepare("SELECT teacher_comment_text FROM teacher_comment WHERE teacher_id = ? AND child_id = ? AND class_id = ? ORDER BY teacher_comment_created_at DESC LIMIT 1");
        $stmt->bind_param("iss", $teacher_id, $child_id, $class_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode(["comment_text" => $row['teacher_comment_text']]);
        } else {
            echo json_encode(["comment_text" => ""]); // if no comment emty
        }

        $stmt->close();
    }

    $conn->close();
}
?>
