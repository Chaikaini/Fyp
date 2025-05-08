<?php
ob_start();
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

if (isset($_GET['action']) && $_GET['action'] === 'test') {
    $response = [
        "session_set" => isset($_SESSION['role']) && isset($_SESSION['admin_id']),
        "session_role" => $_SESSION['role'] ?? 'not set',
        "session_admin_id" => $_SESSION['admin_id'] ?? 'not set',
        "php_version" => phpversion(),
        "mysql_available" => extension_loaded('mysqli') ? "Yes" : "No"
    ];
    $conn = new mysqli("127.0.0.1", "root", "", "the seeds");
    $response["db_connection"] = $conn->connect_error ? "Failed: " . $conn->connect_error : "Success";
    if (!$conn->connect_error) {
        $conn->close();
    }
    echo json_encode($response);
    ob_end_flush();
    exit;
}

$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
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
$sender_id = $admin_id;
error_log("Setting sender_id: $sender_id");

if (isset($_GET['action']) && $_GET['action'] === 'recipients' && isset($_GET['recipient_type'])) {
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
} elseif (isset($_GET['action']) && $_GET['action'] === 'getNotifications') {
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
        JOIN admin a ON n.sender_id = a.admin_id
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
    $stmt->bind_param("i", $sender_id);
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
        error_log("Notification fetched: notification_id={$row['notification_id']}, sender_id={$row['sender_id']}, recipient_type={$row['recipient_type']}");
    }
    echo json_encode($notifications);
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log all POST data for debugging
    error_log("POST data received: " . json_encode($_POST));

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

    $conn->begin_transaction();
    try {
        $recipient_ids = [];
        if ($recipient_id === 'all') {
            if ($recipient_type === 'Parent') {
                $stmt = $conn->prepare("SELECT parent_id AS id FROM parent");
            } else {
                $stmt = $conn->prepare("SELECT teacher_id AS id FROM teacher");
            }
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $recipient_ids[] = $row['id'];
            }
            $stmt->close();
        } else {
            $recipient_ids = [$recipient_id];
            $stmt = $conn->prepare("SELECT 1 FROM " . ($recipient_type === 'Parent' ? 'parent' : 'teacher') . " WHERE " . ($recipient_type === 'Parent' ? 'parent_id' : 'teacher_id') . " = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $recipient_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                throw new Exception("Invalid recipient ID: $recipient_id");
            }
            $stmt->close();
        }

        if (empty($recipient_ids)) {
            throw new Exception("No recipients available for recipient_type: $recipient_type");
        }

        error_log("Recipient IDs for $recipient_type: " . implode(", ", $recipient_ids));

        if (isset($_FILES['notification_document']) && $_FILES['notification_document']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'Uploads/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception("Failed to create upload directory: $uploadDir");
                }
            }
            if (!is_writable($uploadDir)) {
                throw new Exception("Upload directory is not writable: $uploadDir");
            }
            $docName = time() . "_" . basename($_FILES["notification_document"]["name"]);
            $targetFile = $uploadDir . $docName;
            if (!move_uploaded_file($_FILES["notification_document"]["tmp_name"], $targetFile)) {
                throw new Exception("Failed to upload file: " . $_FILES["notification_document"]["error"]);
            }
            $documentPath = $targetFile;
        }

        $stmt = $conn->prepare("INSERT INTO notification (sender_id, recipient_type, notification_title, notification_content, notification_document, subject_id, class_id) VALUES (?, ?, ?, ?, ?, NULL, NULL)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iisss", $sender_id, $recipient_type, $notification_title, $notification_content, $documentPath);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert notification: " . $stmt->error);
        }
        $notification_id = $stmt->insert_id;
        if ($notification_id == 0) {
            throw new Exception("AUTO_INCREMENT failed, notification_id is 0");
        }
        error_log("Inserted notification: notification_id=$notification_id, sender_id=$sender_id, recipient_type=$recipient_type");
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO notification_receiver (notification_id, parent_id, teacher_id, recipient_type, read_status) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        foreach ($recipient_ids as $id) {
            $parent_id = ($recipient_type === 'Parent') ? $id : null;
            $teacher_id = ($recipient_type === 'Teacher') ? $id : null;
            $read_status = 'unread';
            error_log("Inserting notification_receiver: notification_id=$notification_id, parent_id=$parent_id, teacher_id=$teacher_id, recipient_type=$recipient_type");
            $stmt->bind_param("iiiss", $notification_id, $parent_id, $teacher_id, $recipient_type, $read_status);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert receiver: " . $stmt->error);
            }
        }
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Announcement successfully sent']);
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}

$conn->close();
ob_end_flush();
?>