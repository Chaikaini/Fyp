<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

if (!isset($_SESSION['parent_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$parent_id = $_SESSION['parent_id'];

// Handle POST request for teacher info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];
    
    $stmt = $conn->prepare("SELECT teacher_id, teacher_name, teacher_gender, teacher_email, teacher_phone_number, teacher_image FROM teacher WHERE teacher_id = ?");
    if (!$stmt) {
        die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
    }
    
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
if ($teacher = $result->fetch_assoc()) {
    // process teacher image
    if (!empty($teacher['teacher_image'])) {
        // clean up the path with repeated 
         $image_path = str_replace('\\', '/', $teacher['teacher_image']);
        $image_path = preg_replace('/^uploads\/teacher_images\/+/', '', $image_path);
        $full_path = 'uploads/teacher_images/' . $image_path;
        
        // check if the image exists
        if (file_exists($full_path)) {
            $teacher['teacher_image'] = $full_path;
        } else {
            $teacher['teacher_image'] = 'img/user.jpg';
            error_log("Teacher image not found: " . $full_path);
        }
    } else {
        $teacher['teacher_image'] = 'img/user.jpg';
        error_log("No teacher image specified");
    }
    
    
    error_log("Teacher image final path: " . $teacher['teacher_image']);
    
    echo json_encode($teacher);
}else {
        echo json_encode(["error" => "Teacher not found"]);
    }
    
    $stmt->close();
    $conn->close();
    exit;
}

// Handle get request for learning status
if (!isset($_GET['child_id'])) {
    echo json_encode(["error" => "Child ID is required"]);
    exit;
}

$child_id = $_GET['child_id'];

$sql = "
SELECT 
    s.subject_name,
    s.year,
    t.teacher_id,
    t.teacher_name,
    c.class_id,
    c.class_time,
    p.part_name,
    p.part_duration
FROM registration_class rc
JOIN class c ON rc.class_id = c.class_id
JOIN subject s ON c.subject_id = s.subject_id
JOIN teacher t ON c.teacher_id = t.teacher_id
JOIN part p ON c.part_id = p.part_id
WHERE rc.child_id = ? AND rc.parent_id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

$stmt->bind_param("ii", $child_id, $parent_id);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = [
        "subject_name" => $row["subject_name"],
        "year" => $row["year"],
        "teacher_id" => $row["teacher_id"],
        "teacher_name" => $row["teacher_name"],
        "class_id" => $row["class_id"],
        "class_time" => $row["class_time"],
        "part_name" => $row["part_name"],
        "part_duration" => $row["part_duration"]
    ];
}

$stmt->close();
$conn->close();

if (empty($classes)) {
    echo json_encode(["message" => "No classes found for this child"]);
} else {
    echo json_encode($classes);
}