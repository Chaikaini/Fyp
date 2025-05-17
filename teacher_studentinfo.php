<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'the seeds';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$class_id = $_POST['class_id'] ?? '';
if (empty($class_id)) {
    echo json_encode(["error" => "Class ID is required"]);
    exit;
}

$sql = "SELECT 
            s.subject_name, 
            ch.child_id,
            ch.child_name, 
            ch.child_gender, 
            ch.child_kidnumber, 
            ch.child_image,
            p.parent_name, 
            p.phone_number,
            p.parent_relationship,
            p.parent_id   
        FROM registration_class rc
        JOIN class c ON rc.class_id = c.class_id
        JOIN subject s ON c.subject_id = s.subject_id
        JOIN child ch ON rc.child_id = ch.child_id
        JOIN parent p ON ch.parent_id = p.parent_id
        WHERE rc.class_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $class_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$subject_name = null;

while ($row = $result->fetch_assoc()) {
    if ($subject_name === null) {
        $subject_name = $row['subject_name'];
    }

    $data[] = [
        'child_id' => $row['child_id'],
        'child_name' => $row['child_name'],
        'child_gender' => $row['child_gender'],
        'child_kidnumber' => $row['child_kidnumber'],
        'child_image' => $row['child_image'],
        'parent_name' => $row['parent_name'],
        'phone_number' => $row['phone_number'],
        'parent_relationship' => $row['parent_relationship'],
        'parent_id' => $row['parent_id']
    ];
}

//no student record but still need to get the subject name
if ($subject_name === null) {
    $subjectQuery = "SELECT s.subject_name FROM class c 
                     JOIN subject s ON c.subject_id = s.subject_id 
                     WHERE c.class_id = ?";
    $subjectStmt = $conn->prepare($subjectQuery);
    $subjectStmt->bind_param("s", $class_id);
    $subjectStmt->execute();
    $subjectResult = $subjectStmt->get_result();
    if ($subjectRow = $subjectResult->fetch_assoc()) {
        $subject_name = $subjectRow['subject_name'];
    }
}

echo json_encode(['subject_name' => $subject_name, 'students' => $data]);
$conn->close();
?>
