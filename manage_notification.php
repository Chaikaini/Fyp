<?php
// Start output buffering to prevent accidental output
ob_start();
session_start();

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure JSON content type
header("Content-Type: application/json");

// Test endpoint for debugging
if (isset($_GET['action']) && $_GET['action'] === 'test') {
    $response = [
        "session_set" => isset($_SESSION['role']) && isset($_SESSION['admin_id']),
        "session_role" => $_SESSION['role'] ?? 'not set',
        "session_admin_id" => $_SESSION['admin_id'] ?? 'not set',
        "php_version" => phpversion(),
        "mysql_available" => extension_loaded('mysqli') ? "Yes" : "No"
    ];
    $conn = new mysqli("localhost", "root", "", "the seeds");
    $response["db_connection"] = $conn->connect_error ? "Failed: " . $conn->connect_error : "Success";
    if (!$conn->connect_error) {
        $conn->close();
    }
    echo json_encode($response);
    ob_end_flush();
    exit;
}

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    ob_end_flush();
    exit;
}

if (!isset($_SESSION['role']) || !isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'Admin') {
    error_log("Unauthorized access attempt by " . ($_SESSION['admin_id'] ?? 'unknown'));
    echo json_encode(["success" => false, "error" => "Unauthorized access"]);
    ob_end_flush();
    exit;
}

$admin_id = $_SESSION['admin_id'];
$sender_id = "Admin_$admin_id";
$action = $_GET['action'] ?? '';

if ($action === 'recipients' && isset($_GET['recipient_type'])) {
    $recipient_type = $_GET['recipient_type'];
    if ($recipient_type === 'Teacher') {
        $stmt = $conn->prepare("SELECT teacher_id AS id, teacher_name AS name FROM teacher");
    } elseif ($recipient_type === 'Parent') {
        $stmt = $conn->prepare("SELECT parent_id AS id, parent_name AS name FROM parent");
    } else {
        echo json_encode(["success" => false, "error" => "Invalid recipient type"]);
        ob_end_flush();
        exit;
    }
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "error" => "Failed to prepare statement: " . $conn->error]);
        ob_end_flush();
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $recipients = [];
    while ($row = $result->fetch_assoc()) {
        $recipients[] = $row;
    }
    echo json_encode($recipients);
    $stmt->close();
} elseif ($action === 'getNotifications') {
    $stmt = $conn->prepare("
        SELECT n.notification_id, n.sender_id, n.recipient_type, n.subject_id, n.class_id, n.notification_title, 
               n.notification_content, n.notification_document, n.notification_created_at, a.admin_name AS sender_name,
               GROUP_CONCAT(nr.parent_id) AS parent_ids, GROUP_CONCAT(nr.teacher_id) AS teacher_ids,
               GROUP_CONCAT(nr.recipient_type) AS recipient_types,
               GROUP_CONCAT(CASE 
                   WHEN nr.recipient_type = 'Teacher' THEN t.teacher_name 
                   WHEN nr.recipient_type = 'Parent' THEN p.parent_name 
                   WHEN nr.recipient_type = 'Class' THEN NULL
               END) AS recipient_names
        FROM notification n
        JOIN admin a ON n.sender_id = CONCAT('Admin_', a.admin_id)
        LEFT JOIN notification_receiver nr ON n.notification_id = nr.notification_id
        LEFT JOIN teacher t ON nr.teacher_id = t.teacher_id AND nr.recipient_type = 'Teacher'
        LEFT JOIN parent p ON nr.parent_id = p.parent_id AND nr.recipient_type = 'Parent'
        WHERE n.sender_id = ?
        GROUP BY n.notification_id
    ");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "error" => "Failed to prepare statement: " . $conn->error]);
        ob_end_flush();
        exit;
    }
    $stmt->bind_param("s", $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $recipients = [];
        if ($row['recipient_type'] === 'Class') {
            $recipients = [];
        } else {
            $recipient_ids = [];
            if ($row['recipient_type'] === 'Teacher' && $row['teacher_ids']) {
                $recipient_ids = explode(',', $row['teacher_ids']);
            } elseif ($row['recipient_type'] === 'Parent' && $row['parent_ids']) {
                $recipient_ids = explode(',', $row['parent_ids']);
            }
            $recipient_names = $row['recipient_names'] ? explode(',', $row['recipient_names']) : [];
            $recipient_types = $row['recipient_types'] ? explode(',', $row['recipient_types']) : [];
            for ($i = 0; $i < count($recipient_ids); $i++) {
                if ($recipient_ids[$i] && isset($recipient_names[$i])) {
                    $recipients[] = [
                        'recipient_id' => $recipient_ids[$i],
                        'recipient_name' => $recipient_names[$i],
                        'recipient_type' => $recipient_types[$i]
                    ];
                }
            }
        }
        $notifications[] = [
            'notification_id' => $row['notification_id'],
            'sender_id' => $row['sender_id'],
            'sender_name' => $row['sender_name'],
            'recipient_type' => $row['recipient_type'],
            'subject_id' => $row['subject_id'],
            'class_id' => $row['class_id'],
            'notification_title' => $row['notification_title'],
            'notification_content' => $row['notification_content'],
            'notification_document' => $row['notification_document'],
            'notification_created_at' => $row['notification_created_at'],
            'recipients' => $recipients
        ];
    }
    echo json_encode($notifications);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient_type = $_POST['recipient_type'] ?? '';
    $recipient_id = $_POST['recipient_id'] ?? '';
    $notification_title = $_POST['notification_title'] ?? '';
    $notification_content = $_POST['notification_content'] ?? '';
    $documentPath = null;

    if (empty($recipient_type) || empty($recipient_id) || empty($notification_title) || empty($notification_content)) {
        error_log("Missing required fields: recipient_type=$recipient_type, recipient_id=$recipient_id, title=$notification_title, content=$notification_content");
        echo json_encode(['success' => false, 'error' => 'All required fields must be filled']);
        ob_end_flush();
        exit;
    }

    if (!in_array($recipient_type, ['Teacher', 'Parent'])) {
        error_log("Invalid recipient type: $recipient_type");
        echo json_encode(['success' => false, 'error' => 'Invalid recipient type']);
        ob_end_flush();
        exit;
    }

    // Handle "All" option
    $recipient_ids = [];
    if ($recipient_id === 'all') {
        if ($recipient_type === 'Teacher') {
            $stmt = $conn->prepare("SELECT teacher_id AS id FROM teacher");
        } else {
            $stmt = $conn->prepare("SELECT parent_id AS id FROM parent");
        }
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(["success" => false, "error" => "Failed to prepare statement: " . $conn->error]);
            ob_end_flush();
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recipient_ids[] = $row['id'];
        }
        $stmt->close();
    } else {
        $recipient_ids = [$recipient_id];
    }

    if (empty($recipient_ids)) {
        error_log("No recipients available for recipient_type: $recipient_type");
        echo json_encode(['success' => false, 'error' => 'No recipients available for the selected type']);
        ob_end_flush();
        exit;
    }

    if (isset($_FILES['notification_document']) && $_FILES['notification_document']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'Uploads/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("Failed to create upload directory: $uploadDir");
                echo json_encode(['success' => false, 'error' => 'Failed to create upload directory']);
                ob_end_flush();
                exit;
            }
        }
        $docName = time() . "_" . basename($_FILES["notification_document"]["name"]);
        $targetFile = $uploadDir . $docName;
        if (!move_uploaded_file($_FILES["notification_document"]["tmp_name"], $targetFile)) {
            error_log("Failed to upload file: " . $_FILES["notification_document"]["error"]);
            echo json_encode(['success' => false, 'error' => 'Failed to upload file: ' . $_FILES["notification_document"]["error"]]);
            ob_end_flush();
            exit;
        }
        $documentPath = $targetFile;
    }

    // Insert notification with NULL for subject_id and class_id
    $stmt = $conn->prepare("
        INSERT INTO notification (sender_id, recipient_type, notification_title, notification_content, notification_document, subject_id, class_id)
        VALUES (?, ?, ?, ?, ?, NULL, NULL)
    ");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "error" => "Failed to prepare statement: " . $conn->error]);
        ob_end_flush();
        exit;
    }
    $stmt->bind_param("sssss", $sender_id, $recipient_type, $notification_title, $notification_content, $documentPath);
    if (!$stmt->execute()) {
        error_log("Failed to insert notification: " . $stmt->error);
        echo json_encode(['success' => false, 'error' => 'Failed to send announcement: ' . $stmt->error]);
        $stmt->close();
        ob_end_flush();
        exit;
    }
    $notification_id = $stmt->insert_id;
    $stmt->close();

    $values = [];
    foreach ($recipient_ids as $id) {
        if ($recipient_type === 'Teacher') {
            $values[] = "($notification_id, NULL, '$id', '$recipient_type', 'unread')";
        } else {
            $values[] = "($notification_id, '$id', NULL, '$recipient_type', 'unread')";
        }
    }
    if (!empty($values)) {
        $insert_sql = "INSERT INTO notification_receiver (notification_id, parent_id, teacher_id, recipient_type, read_status) VALUES " . implode(", ", $values);
        if (!$conn->query($insert_sql)) {
            error_log("Failed to insert receivers: " . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Failed to add receivers: ' . $conn->error]);
            ob_end_flush();
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No recipients selected']);
        ob_end_flush();
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Announcement successfully sent']);
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}

$conn->close();
ob_end_flush();
?>