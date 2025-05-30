<?php
header('Content-Type: application/json');

session_start();
require 'send_email_notification.php';

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the teacher_id from session
$sender_id = $_SESSION['teacher_id'] ?? '';
if (!$sender_id) {
    die(json_encode(['error' => 'Teacher ID not found. Please login.']));
}

// Get form data
$class_id = $_POST['class_id'] ?? ($_POST['subject_id'] ?? ''); 
$notification_title = $_POST['notification_title'] ?? '';
$notification_content = $_POST['notification_content'] ?? ''; 
$documentPath = null;
$recipient_type = 'Class'; 

// Get form data
$class_id = $_POST['class_id'] ?? ($_POST['subject_id'] ?? ''); 
$notification_title = $_POST['notification_title'] ?? '';
$notification_content = $_POST['notification_content'] ?? ''; 
$documentPath = null;
$recipient_type = 'Class'; 

// Get sender (teacher) information
$teacherQuery = $conn->prepare("SELECT teacher_name FROM teacher WHERE teacher_id = ?");
$teacherQuery->bind_param("s", $sender_id);
$teacherQuery->execute();
$teacherData = $teacherQuery->get_result()->fetch_assoc();
$sender_name = $teacherData['teacher_name'];

// Get class information
$classQuery = $conn->prepare("
    SELECT c.class_id, s.subject_name, s.year
    FROM class c 
    JOIN subject s ON c.subject_id = s.subject_id 
    WHERE c.class_id = ?
");
$classQuery->bind_param("s", $class_id);
$classQuery->execute();
$classData = $classQuery->get_result()->fetch_assoc();
$class_info = " {$classData['year']} - {$classData['subject_name']}";

// Upload the document if it exists
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

// Insert into notification table
$insertNotification = $conn->prepare("
    INSERT INTO notification 
    (sender_id, recipient_type, class_id, notification_title, notification_content, notification_document) 
    VALUES (?, ?, ?, ?, ?, ?)");
$insertNotification->bind_param("ssssss", $sender_id, $recipient_type, $class_id, $notification_title, $notification_content, $documentPath);
$insertNotification->execute();

$notification_id = $insertNotification->insert_id;
$insertNotification->close();

// Find all parents in the class
$sql = "SELECT parent_id FROM registration_class WHERE class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $class_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $parent_id = $row['parent_id'];
        $values[] = "($notification_id, '$parent_id', NULL, 'Class', 'unread')";

        // Get parent email and name
        $parentQuery = $conn->prepare("SELECT parent_email, parent_name FROM parent WHERE parent_id = ?");
        $parentQuery->bind_param("s", $parent_id);
        $parentQuery->execute();
        $parentData = $parentQuery->get_result()->fetch_assoc();
        
        if ($parentData) {
            $toEmail = $parentData['parent_email'];
            $toName = $parentData['parent_name'];
            $emailSubject = "The Seeds Learning Tuition Centre";

            // Load HTML template
            $template = file_get_contents('http://localhost/Fyp/notification_email_template.html');
            $notificationLink = "http://localhost/Fyp/login.html";

            
            $emailBody = str_replace(
                [
                   
                    '{{parent_name}}',
                    '{{sender_name}}',
                    '{{class_name}}',
                    '{{notification_title}}',
                    '{{notification_link}}'
                ],
                [
                    
                    $toName,
                    $sender_name,
                    $class_info,
                    $notification_title,
                    $notificationLink
                ],
                $template
            );

            // send email
            sendEmailToParent($toEmail, $toName, $emailSubject, $emailBody);
        }
        $parentQuery->close();
    }



    $insert_sql = "INSERT INTO notification_receiver 
        (notification_id, parent_id, teacher_id, recipient_type, read_status) 
        VALUES " . implode(", ", $values);

    if ($conn->query($insert_sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Announcement successfully sent to class!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Announcement failed, try again. ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No parents found for this class!']);
}

$stmt->close();
$conn->close();
?>

