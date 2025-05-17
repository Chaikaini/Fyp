<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $child_id = intval($_POST['child_id']);
    $class_id = $_POST['class_id'];

    if (empty($child_id) || empty($class_id)) {
        echo json_encode(["error" => "Missing data"]);
        exit;
    }

   // get teacher_id from class table using class_id
    $getTeacherStmt = $conn->prepare("SELECT teacher_id FROM class WHERE class_id = ?");
    $getTeacherStmt->bind_param("s", $class_id);
    $getTeacherStmt->execute();
    $getTeacherResult = $getTeacherStmt->get_result();

    if ($getTeacherRow = $getTeacherResult->fetch_assoc()) {
    $teacher_id = intval($getTeacherRow['teacher_id']);
    } else {
    echo json_encode(["error" => "Invalid class_id or teacher not found."]);
    exit;
    }

    $getTeacherStmt->close();

    // check if session teacher is authorized to access this class
    if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(["error" => "No teacher ID in session"]);
    exit;
    }

    $session_teacher_id = intval($_SESSION['teacher_id']);

    if ($teacher_id !== $session_teacher_id) {
    echo json_encode(["error" => "Unauthorized access to this class."]);
    exit;
    }


    if (isset($_POST['comment_text'])) {
        // Save comment
        $comment_text = $_POST['comment_text'];
        $created_at = date('Y-m-d H:i:s');

        $checkStmt = $conn->prepare("SELECT teacher_comment_id FROM teacher_comment WHERE  child_id = ? AND class_id = ?");
        $checkStmt->bind_param("ss", $child_id, $class_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Update
            $updateStmt = $conn->prepare("UPDATE teacher_comment SET teacher_comment_text = ?, teacher_comment_created_at = ? WHERE  child_id = ? AND class_id = ?");
            $updateStmt->bind_param("ssss", $comment_text, $created_at, $child_id, $class_id);

            if ($updateStmt->execute()) {
                echo json_encode(["success" => "Comment updated successfully."]);
            } else {
                echo json_encode(["error" => "Failed to update comment: " . $updateStmt->error]);
            }

            $updateStmt->close();
        } else {
            // insert
            $insertStmt = $conn->prepare("INSERT INTO teacher_comment (child_id, class_id, teacher_comment_text, teacher_comment_created_at) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("ssss",$child_id, $class_id, $comment_text, $created_at);

            if ($insertStmt->execute()) {
                echo json_encode(["success" => "Comment saved successfully."]);
            } else {
                echo json_encode(["error" => "Failed to save comment: " . $insertStmt->error]);
            }

            $insertStmt->close();
        }

        $checkStmt->close();
    } else {
        // get comment
        $stmt = $conn->prepare("SELECT teacher_comment_text FROM teacher_comment WHERE  child_id = ? AND class_id = ? ORDER BY teacher_comment_created_at DESC LIMIT 1");
        $stmt->bind_param("ss", $child_id, $class_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode(["comment_text" => $row['teacher_comment_text']]);
        } else {
            echo json_encode(["comment_text" => ""]);
        }

        $stmt->close();
    }
}

$conn->close();
?>
