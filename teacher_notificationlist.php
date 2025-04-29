<?php
session_start();

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get the teacher_id from session
$sender_id = $_SESSION['teacher_id'] ?? '';
if (!$sender_id) {
    die(json_encode(['error' => 'Sender ID not found. Please login.']));
}

// get form data
$subject_id = $_POST['subject_id'] ?? ''; 
$class_id = $_POST['class_id'] ?? ''; 
$notification_title = $_POST['notification_title'] ?? '';
$notification_content = $_POST['notification_content'] ?? ''; 
$documentPath = null;

// upload the document if it exists
if (isset($_FILES['notification_document']) && $_FILES['notification_document']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $docName = time() . "_" . basename($_FILES["notification_document"]["name"]);
    $targetFile = $uploadDir . $docName;
    move_uploaded_file($_FILES["notification_document"]["tmp_name"], $targetFile);
    $documentPath = $targetFile;
}

//Insert into notification table
$insertNotification = $conn->prepare("INSERT INTO notification (sender_id, subject_id, class_id, notification_title, notification_content, notification_document) VALUES (?, ?, ?, ?, ?, ?)");
$insertNotification->bind_param("ssssss", $sender_id, $subject_id, $class_id, $notification_title, $notification_content, $documentPath);
$insertNotification->execute();

$notification_id = $insertNotification->insert_id; // Get the newly inserted notification's ID
$insertNotification->close();

// Find all parents in the class
$sql = "SELECT parent_id FROM registration_class WHERE class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $class_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Collect all parent_id
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $parent_id = $row['parent_id'];
        // sql format inserting into notification_receiver table
        $values[] = "($notification_id, '$parent_id', 'unread')";
    }
    
    if (!empty($values)) {
        // Insert into notification_receiver table
        $insert_sql = "INSERT INTO notification_receiver (notification_id, parent_id, read_status) VALUES " . implode(", ", $values);
        if ($conn->query($insert_sql) === TRUE) {
            echo json_encode(['success' => true, 'message' => 'Announcement successfully send to class!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Announcement failed, try again ' . $conn->error]);
        }
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No parents found for this class!']);
}

$stmt->close();
$conn->close();
?>
