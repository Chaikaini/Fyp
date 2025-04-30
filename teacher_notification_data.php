<?php
session_start(); 
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$subject_id = $_GET['subject_id'] ?? '';
$action = $_GET['action'] ?? '';
$teacher_id = $_SESSION['teacher_id'] ?? null; 

if (!$teacher_id) {
    echo json_encode(["error" => "Not logged in or teacher ID not set in session."]);
    exit;
}

if ($action === 'subject') {
    //search subject by teacher_id
    $stmt = $conn->prepare("SELECT subject_id, subject_name FROM subject WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    echo json_encode($subjects);
    $stmt->close();
} elseif ($action === 'class' && $subject_id) {
    
    $stmt = $conn->prepare("SELECT class_id FROM class WHERE subject_id = ?");
    $stmt->bind_param("s", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }

    echo json_encode($classes);
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request. Provide action=subject or action=class with subject_id"]);
}

$conn->close();
