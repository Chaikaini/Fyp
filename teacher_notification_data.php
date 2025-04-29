<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}


$subject_id = $_GET['subject_id'] ?? '';
$action = $_GET['action'] ?? '';

if ($action === 'subject') {
    
    $query = "SELECT subject_id, subject_name FROM subject";
    $result = $conn->query($query);

    if ($result) {
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
        echo json_encode($subjects);
    } else {
        echo json_encode(["error" => "Query error: " . $conn->error]);
    }
} elseif ($action === 'class' && $subject_id) {
    
    $stmt = $conn->prepare("SELECT class_id FROM class WHERE subject_id = ?");
    if ($stmt) {
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
        echo json_encode(["error" => "Prepare statement error: " . $conn->error]);
    }
} else {
   
    echo json_encode(["error" => "Invalid request. Provide action=subject or action=class with subject_id"]);
}

$conn->close();
?>
