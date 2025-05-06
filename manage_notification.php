<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if (!isset($_SESSION['role']) || !isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$sender_id = "Admin_$admin_id";
$action = $_GET['action'] ?? '';

if ($action === 'subject') {
    $stmt = $conn->prepare("SELECT subject_id, subject_name FROM subject");
    $stmt->execute();
    $result = $stmt->get_result();
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    echo json_encode($subjects);
    $stmt->close();
} elseif ($action === 'class' && isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
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
} elseif ($action === 'getNotifications') {
    $stmt = $conn->prepare("SELECT n.notification_id, n.sender_id, n.subject_id, n.class_id, n.notification_title, n.notification_content, n.notification_document, n.notification_created_at, a.admin_name AS sender_name
                            FROM notification n
                            JOIN admin a ON n.sender_id = CONCAT('Admin_', a.admin_id)
                            WHERE n.sender_id = ?");
    $stmt->bind_param("s", $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    echo json_encode($notifications);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $notification_title = $_POST['notification_title'] ?? '';
    $notification_content = $_POST['notification_content'] ?? '';
    $documentPath = null;

    if (empty($subject_id) || empty($class_id) || empty($notification_title) || empty($notification_content)) {
        echo json_encode(['success' => false, 'error' => 'All required fields must be filled']);
        exit;
    }

    if (isset($_FILES['notification_document']) && $_FILES['notification_document']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'Uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $docName = time() . "_" . basename($_FILES["notification_document"]["name"]);
        $targetFile = $uploadDir . $docName;
        if (move_uploaded_file($_FILES["notification_document"]["tmp_name"], $targetFile)) {
            $documentPath = $targetFile;
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO notification (sender_id, subject_id, class_id, notification_title, notification_content, notification_document) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $sender_id, $subject_id, $class_id, $notification_title, $notification_content, $documentPath);
    if ($stmt->execute()) {
        $notification_id = $stmt->insert_id;
        $stmt->close();

        $sql = "SELECT parent_id FROM registration_class WHERE class_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $class_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $values = [];
            while ($row = $result->fetch_assoc()) {
                $parent_id = $row['parent_id'];
                $values[] = "($notification_id, '$parent_id', 'unread')";
            }
            if (!empty($values)) {
                $insert_sql = "INSERT INTO notification_receiver (notification_id, parent_id, read_status) VALUES " . implode(", ", $values);
                if ($conn->query($insert_sql) === TRUE) {
                    echo json_encode(['success' => true, 'message' => 'Announcement successfully sent to class']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to add receivers: ' . $conn->error]);
                }
            } else {
                echo json_encode(['success' => true, 'message' => 'Announcement sent, but no parents found for this class']);
            }
        } else {
            echo json_encode(['success' => true, 'message' => 'Announcement sent, but no parents found for this class']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send announcement: ' . $conn->error]);
        $stmt->close();
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}

$conn->close();
?>