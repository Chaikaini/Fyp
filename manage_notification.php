<?php
ob_start();
session_start();

// Include email sending functionality
require 'send_email_notification.php';

// Disable error display and enable logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'path/to/your/error.log'); // Replace with your server's error log path
error_reporting(E_ALL);

header("Content-Type: application/json");

$conn = new mysqli("127.0.0.1", "root", "", "the seeds");
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    ob_end_flush();
    exit;
}
$conn->set_charset("utf8mb4");

// Handle test action
if (isset($_GET['action']) && $_GET['action'] === 'test') {
    $response = [
        "session_set" => isset($_SESSION['role']) && isset($_SESSION['admin_id']),
        "session_role" => $_SESSION['role'] ?? 'not set',
        "session_admin_id" => $_SESSION['admin_id'] ?? 'not set',
        "php_version" => phpversion(),
        "mysql_available" => extension_loaded('mysqli') ? "Yes" : "No",
        "db_connection" => $conn->connect_error ? "Failed: " . $conn->connect_error : "Success"
    ];
    echo json_encode($response);
    $conn->close();
    ob_end_flush();
    exit;
}

// Check authentication
if (!isset($_SESSION['role']) || !isset($_SESSION['admin_id']) || !in_array($_SESSION['role'], ['Admin', 'Super Admin'])) {
    error_log("Unauthorized access attempt by " . ($_SESSION['admin_id'] ?? 'unknown'));
    echo json_encode(["success" => false, "error" => "Unauthorized access"]);
    $conn->close();
    ob_end_flush();
    exit;
}

$admin_id = $_SESSION['admin_id'];
$sender_id = $admin_id;
error_log("Setting sender_id: $sender_id");

// Get sender (admin) information
$adminQuery = $conn->prepare("SELECT admin_name FROM admin WHERE admin_id = ?");
$adminQuery->bind_param("i", $sender_id);
$adminQuery->execute();
$adminData = $adminQuery->get_result()->fetch_assoc();
$sender_name = $adminData['admin_name'] ?? 'Unknown Admin';
$adminQuery->close();

// Handle recipients request
if (isset($_GET['action']) && $_GET['action'] === 'recipients' && isset($_GET['recipient_type'])) {
    $recipient_type = $_GET['recipient_type'];
    error_log("Fetching recipients for recipient_type: $recipient_type");
    if ($recipient_type === 'Teacher') {
        $stmt = $conn->prepare("SELECT teacher_id AS id, teacher_name AS name FROM teacher");
    } elseif ($recipient_type === 'Parent') {
        $stmt = $conn->prepare("SELECT parent_id AS id, parent_name AS name FROM parent");
    } else {
        error_log("Invalid recipient type: $recipient_type");
        echo json_encode(["success" => false, "error" => "Invalid recipient type"]);
        $conn->close();
        ob_end_flush();
        exit;
    }
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "error" => "Failed to prepare statement"]);
        $conn->close();
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
    $conn->close();
    ob_end_flush();
    exit;
}

// Handle get notifications request
if (isset($_GET['action']) && $_GET['action'] === 'getNotifications') {
    error_log("Fetching all notifications");
    $stmt = $conn->prepare("
        SELECT 
            n.notification_id, 
            n.sender_id, 
            n.recipient_type, 
            n.class_id, 
            n.notification_title, 
            n.notification_content, 
            n.notification_document, 
            n.notification_created_at,
            CASE 
                WHEN n.sender_id IN (SELECT admin_id FROM admin) THEN 'Admin'
                WHEN n.sender_id IN (SELECT teacher_id FROM teacher) THEN 'Teacher'
                ELSE 'Unknown'
            END AS sender_type,
            CASE  
                WHEN n.sender_id IN (SELECT admin_id FROM admin) THEN (SELECT admin_name FROM admin WHERE admin_id = n.sender_id)
                WHEN n.sender_id IN (SELECT teacher_id FROM teacher) THEN (SELECT teacher_name FROM teacher WHERE teacher_id = n.sender_id)
                ELSE 'Unknown Sender'
            END AS sender_name,
            GROUP_CONCAT(nr.parent_id) AS parent_ids, 
            GROUP_CONCAT(nr.teacher_id) AS teacher_ids,
            GROUP_CONCAT(nr.recipient_type) AS recipient_types,
            GROUP_CONCAT(CASE 
                WHEN nr.recipient_type = 'Teacher' THEN t.teacher_name 
                WHEN nr.recipient_type = 'Parent' THEN p.parent_name 
                ELSE NULL
            END) AS recipient_names,
            c.subject_id,
            s.subject_name
        FROM notification n
        LEFT JOIN notification_receiver nr ON n.notification_id = nr.notification_id
        LEFT JOIN teacher t ON nr.teacher_id = t.teacher_id AND nr.recipient_type = 'Teacher'
        LEFT JOIN parent p ON nr.parent_id = p.parent_id AND nr.recipient_type = 'Parent'
        LEFT JOIN class c ON n.class_id = c.class_id
        LEFT JOIN subject s ON c.subject_id = s.subject_id
        GROUP BY n.notification_id
    ");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "error" => "Failed to prepare statement"]);
        $conn->close();
        ob_end_flush();
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $recipient_ids = [];
        if ($row['recipient_type'] === 'Teacher' && $row['teacher_ids']) {
            $recipient_ids = explode(',', $row['teacher_ids']);
        } elseif ($row['recipient_type'] === 'Parent' && $row['parent_ids']) {
            $recipient_ids = explode(',', $row['parent_ids']);
        }
        $recipient_names = $row['recipient_names'] ? explode(',', $row['recipient_names']) : [];
        $recipient_types = $row['recipient_types'] ? explode(',', $row['recipient_types']) : [];
        $recipients = [];
        for ($i = 0; $i < count($recipient_ids); $i++) {
            if ($recipient_ids[$i] && isset($recipient_names[$i])) {
                $recipients[] = [
                    'recipient_id' => $recipient_ids[$i],
                    'recipient_name' => $recipient_names[$i],
                    'recipient_type' => $recipient_types[$i]
                ];
            }
        }
        $notifications[] = [
            'notification_id' => $row['notification_id'],
            'sender_id' => $row['sender_id'],
            'sender_type' => $row['sender_type'],
            'sender_name' => $row['sender_name'],
            'recipient_type' => $row['recipient_type'],
            'subject_id' => $row['subject_id'],
            'subject_name' => $row['subject_name'],
            'class_id' => $row['class_id'],
            'notification_title' => $row['notification_title'],
            'notification_content' => $row['notification_content'],
            'notification_document' => $row['notification_document'],
            'notification_created_at' => $row['notification_created_at'],
            'recipients' => $recipients
        ];
        error_log("Notification fetched: notification_id={$row['notification_id']}, sender_id={$row['sender_id']}, sender_type={$row['sender_type']}, sender_name={$row['sender_name']}, recipient_type={$row['recipient_type']}, subject_id={$row['subject_id']}, class_id={$row['class_id']}");
    }
    echo json_encode($notifications);
    $stmt->close();
    $conn->close();
    ob_end_flush();
    exit;
}

// Handle POST request for sending notification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received: " . json_encode($_POST));
    
    $recipient_type = $_POST['recipient_type'] ?? '';
    $recipient_id = $_POST['recipient_id'] ?? '';
    $notification_title = $_POST['notification_title'] ?? '';
    $notification_content = $_POST['notification_content'] ?? '';
    $documentPath = null;

    // Validate inputs
    if (empty($recipient_type) || empty($recipient_id) || empty($notification_title) || empty($notification_content)) {
        error_log("Missing required fields: recipient_type=$recipient_type, recipient_id=$recipient_id, title=$notification_title, content=$notification_content");
        echo json_encode(['success' => false, 'error' => 'All required fields must be filled']);
        $conn->close();
        ob_end_flush();
        exit;
    }

    if (!in_array($recipient_type, ['Teacher', 'Parent'])) {
        error_log("Invalid recipient type: $recipient_type");
        echo json_encode(['success' => false, 'error' => 'Invalid recipient type. Must be Teacher or Parent']);
        $conn->close();
        ob_end_flush();
        exit;
    }

    $conn->begin_transaction();
    try {
        $recipient_ids = [];
        if ($recipient_id === 'all') {
            if ($recipient_type === 'Parent') {
                $stmt = $conn->prepare("SELECT parent_id AS id, parent_name AS name, parent_email AS email FROM parent");
            } else {
                $stmt = $conn->prepare("SELECT teacher_id AS id, teacher_name AS name, teacher_email AS email FROM teacher");
            }
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $recipient_ids[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email']
                ];
            }
            $stmt->close();
        } else {
            $table = $recipient_type === 'Parent' ? 'parent' : 'teacher';
            $id_field = $recipient_type === 'Parent' ? 'parent_id' : 'teacher_id';
            $name_field = $recipient_type === 'Parent' ? 'parent_name' : 'teacher_name';
            $email_field = $recipient_type === 'Parent' ? 'parent_email' : 'teacher_email';
            $stmt = $conn->prepare("SELECT $id_field AS id, $name_field AS name, $email_field AS email FROM $table WHERE $id_field = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $recipient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("Invalid recipient ID: $recipient_id");
            }
            $row = $result->fetch_assoc();
            $recipient_ids[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'email' => $row['email']
            ];
            $stmt->close();
        }

        if (empty($recipient_ids)) {
            throw new Exception("No recipients available for recipient_type: $recipient_type");
        }

        error_log("Recipient IDs for $recipient_type: " . implode(", ", array_column($recipient_ids, 'id')));

        // Handle file upload
        if (isset($_FILES['notification_document']) && $_FILES['notification_document']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'Uploads/';
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['notification_document']['type'];
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
            }
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

        // Insert into notification table
        error_log("Inserting notification with recipient_type: $recipient_type");
        $stmt = $conn->prepare("INSERT INTO notification (sender_id, recipient_type, notification_title, notification_content, notification_document) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("issss", $sender_id, $recipient_type, $notification_title, $notification_content, $documentPath);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert notification: " . $stmt->error);
        }
        $notification_id = $stmt->insert_id;
        if ($notification_id == 0) {
            throw new Exception("AUTO_INCREMENT failed, notification_id is 0");
        }
        error_log("Inserted notification: notification_id=$notification_id, sender_id=$sender_id, recipient_type=$recipient_type");
        $stmt->close();

        // Verify inserted recipient_type
        $stmt = $conn->prepare("SELECT recipient_type FROM notification WHERE notification_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $notification_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        error_log("Verified notification: recipient_type=" . ($row['recipient_type'] ?? 'NULL'));
        $stmt->close();

        // Insert into notification_receiver table and send emails
        $stmt = $conn->prepare("INSERT INTO notification_receiver (notification_id, parent_id, teacher_id, recipient_type, read_status) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Load email template
        $template = file_get_contents('admin_notification_template.html');
        $notificationLink = "http://localhost/Fyp/login.html"; // Adjust to your actual login page URL
        $emailSubject = "The Seeds Learning Tuition Centre";

        foreach ($recipient_ids as $recipient) {
            $id = $recipient['id'];
            $name = $recipient['name'];
            $email = $recipient['email'];
            $parent_id = ($recipient_type === 'Parent') ? $id : null;
            $teacher_id = ($recipient_type === 'Teacher') ? $id : null;
            $read_status = 'unread';
            error_log("Inserting notification_receiver: notification_id=$notification_id, parent_id=$parent_id, teacher_id=$teacher_id, recipient_type=$recipient_type");
            $stmt->bind_param("iiiss", $notification_id, $parent_id, $teacher_id, $recipient_type, $read_status);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert receiver: " . $stmt->error);
            }

            // Prepare attachment link
            $attachmentLink = $documentPath ? "<p><a href='$documentPath'>View Attachment</a></p>" : '';

            // Send email to recipient
            if ($email) {
                $emailBody = str_replace(
                    [
                        '{{recipient_name}}',
                        '{{sender_name}}',
                        '{{notification_title}}',
                        '{{notification_content}}',
                        '{{notification_link}}',
                        '{{attachment_link}}'
                    ],
                    [
                        $name,
                        $sender_name,
                        $notification_title,
                        $notification_content,
                        $notificationLink,
                        $attachmentLink
                    ],
                    $template
                );
                if (!sendEmailToParent($email, $name, $emailSubject, $emailBody)) {
                    error_log("Failed to send email to $email for notification_id=$notification_id");
                    // Continue processing to avoid rolling back the transaction
                } else {
                    error_log("Email sent to $email for notification_id=$notification_id");
                }
            } else {
                error_log("No email address for recipient ID: $id");
            }
        }
        $stmt->close();

        // Verify inserted recipient_type in notification_receiver
        $stmt = $conn->prepare("SELECT recipient_type FROM notification_receiver WHERE notification_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $notification_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            error_log("Verified recipient_type in notification_receiver: " . ($row['recipient_type'] ?? 'NULL'));
        }
        $stmt->close();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Announcement successfully sent']);
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    $conn->close();
    ob_end_flush();
    exit;
}

// Handle invalid requests
error_log("Invalid request received: method={$_SERVER['REQUEST_METHOD']}, action=" . ($_GET['action'] ?? 'none'));
echo json_encode(["success" => false, "error" => "Invalid request"]);
$conn->close();
ob_end_flush();
exit;
?>