<?php
header('Content-Type: application/json');

// Start session to access logged-in admin's ID
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized: No admin logged in"]);
    exit;
}

$host = 'localhost';
$dbname = 'the seeds';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// Check if admin_id column exists in subject table
function hasAdminIdColumn($pdo) {
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM subject LIKE 'admin_id'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

$hasAdminId = hasAdminIdColumn($pdo);
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'getSubjects') {
        try {
            $sql = "SELECT s.subject_id, s.subject_name, s.year, s.subject_price, s.subject_description, s.subject_image, a.admin_name 
                    FROM subject s 
                    LEFT JOIN admin a ON s.admin_id = a.admin_id";
            if (isset($_GET['subject_id'])) {
                $sql .= " WHERE s.subject_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_GET['subject_id']]);
            } else {
                $stmt = $pdo->query($sql);
            }
            $subjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subjects[] = [
                    "subject_id" => $row["subject_id"],
                    "subject_name" => $row["subject_name"],
                    "year" => $row["year"],
                    "subject_price" => (float)$row["subject_price"],
                    "subject_description" => $row["subject_description"],
                    "subject_image" => $row["subject_image"],
                    "admin_name" => $row["admin_name"] ?? 'Unknown'
                ];
            }
            echo json_encode(["success" => true, "subjects" => $subjects]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Error fetching subjects: " . $e->getMessage()]);
        }
    } elseif ($action === 'deleteSubject') {
        $subjectId = $_GET['id'] ?? '';
        if ($subjectId) {
            try {
                // Check if subject has associated classes
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM class WHERE subject_id = ?");
                $stmt->execute([$subjectId]);
                $classCount = $stmt->fetchColumn();

                if ($classCount > 0) {
                    echo json_encode(["success" => false, "message" => "Cannot delete subject with associated classes"]);
                    exit;
                }

                // Get current image path to delete the file
                $stmt = $pdo->prepare("SELECT subject_image FROM subject WHERE subject_id = ?");
                $stmt->execute([$subjectId]);
                $imagePath = $stmt->fetchColumn();
                if ($imagePath && file_exists($imagePath)) {
                    unlink($imagePath);
                }

                $stmt = $pdo->prepare("DELETE FROM subject WHERE subject_id = ?");
                $stmt->execute([$subjectId]);
                echo json_encode(["success" => true]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Error deleting subject: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No subject ID provided"]);
        }
    } elseif ($action === 'searchSubjects') {
        $searchQuery = $_GET['query'] ?? '';
        try {
            $sql = "SELECT s.subject_id, s.subject_name, s.year, s.subject_price, s.subject_description, s.subject_image, a.admin_name 
                    FROM subject s 
                    LEFT JOIN admin a ON s.admin_id = a.admin_id 
                    WHERE s.subject_name LIKE :search";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':search' => "%$searchQuery%"]);
            $subjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subjects[] = [
                    "subject_id" => $row["subject_id"],
                    "subject_name" => $row["subject_name"],
                    "year" => $row["year"],
                    "subject_price" => (float)$row["subject_price"],
                    "subject_description" => $row["subject_description"],
                    "subject_image" => $row["subject_image"],
                    "admin_name" => $row["admin_name"] ?? 'Unknown'
                ];
            }
            echo json_encode(["success" => true, "subjects" => $subjects]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Error searching subjects: " . $e->getMessage()]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    function handleFileUpload($file) {
        if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            throw new Exception("File size exceeds 2MB");
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG, PNG, and JPEG are allowed");
        }

        $uploadDir = 'img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception("Failed to upload file");
        }

        return $filePath;
    }

    if ($_POST['action'] === 'addSubject') {
        try {
            $subjectId = $_POST['subject_id'];
            $year = $_POST['year'];
            $firstChar = substr($subjectId, 0, 1);

            if ($firstChar !== '1' && $firstChar !== '2') {
                throw new Exception("Subject ID must start with '1' or '2'");
            }
            if (($firstChar === '1' && $year !== 'Year 1') || ($firstChar === '2' && $year !== 'Year 2')) {
                throw new Exception("Subject ID starting with '1' must have Year 1, and '2' must have Year 2");
            }

            // Check if subject_id already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM subject WHERE subject_id = ?");
            $stmt->execute([$subjectId]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Subject ID already exists");
            }

            $imagePath = null;
            if (!empty($_FILES['image']['name'])) {
                $imagePath = handleFileUpload($_FILES['image']);
            } else {
                throw new Exception("Image file is required");
            }

            if ($hasAdminId) {
                $sql = "INSERT INTO subject (subject_id, admin_id, subject_name, year, subject_price, subject_description, subject_image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $subjectId,
                    $_SESSION['admin_id'], // Use session admin_id
                    $_POST['subject_name'],
                    $year,
                    $_POST['subject_price'],
                    $_POST['subject_description'] ?: null,
                    $imagePath
                ]);
            } else {
                $sql = "INSERT INTO subject (subject_id, subject_name, year, subject_price, subject_description, subject_image) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $subjectId,
                    $_POST['subject_name'],
                    $year,
                    $_POST['subject_price'],
                    $_POST['subject_description'] ?: null,
                    $imagePath
                ]);
            }
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            // Clean up uploaded file if it exists
            if (isset($imagePath) && file_exists($imagePath)) {
                unlink($imagePath);
            }
            echo json_encode(["success" => false, "message" => "Error adding subject: " . $e->getMessage()]);
        }
    } elseif ($_POST['action'] === 'updateSubject') {
        try {
            $subjectId = $_POST['subject_id'];
            $year = $_POST['year'];
            $firstChar = substr($subjectId, 0, 1);

            if ($firstChar !== '1' && $firstChar !== '2') {
                throw new Exception("Subject ID must start with '1' or '2'");
            }
            if (($firstChar === '1' && $year !== 'Year 1') || ($firstChar === '2' && $year !== 'Year 2')) {
                throw new Exception("Subject ID starting with '1' must have Year 1, and '2' must have Year 2");
            }

            $imagePath = null;
            if (!empty($_FILES['image']['name'])) {
                // Delete old image if exists
                $stmt = $pdo->prepare("SELECT subject_image FROM subject WHERE subject_id = ?");
                $stmt->execute([$subjectId]);
                $oldImage = $stmt->fetchColumn();
                if ($oldImage && file_exists($oldImage)) {
                    unlink($oldImage);
                }

                $imagePath = handleFileUpload($_FILES['image']);
            }

            $sql = "UPDATE subject SET subject_name = ?, year = ?, subject_price = ?, subject_description = ?";
            $params = [
                $_POST['subject_name'],
                $year,
                $_POST['subject_price'],
                $_POST['subject_description'] ?: null
            ];

            if ($hasAdminId) {
                $sql .= ", admin_id = ?";
                $params[] = $_SESSION['admin_id']; // Use session admin_id
            }

            if ($imagePath) {
                $sql .= ", subject_image = ?";
                $params[] = $imagePath;
            }

            $sql .= " WHERE subject_id = ?";
            $params[] = $subjectId;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            // Clean up uploaded file if it exists
            if (isset($imagePath) && file_exists($imagePath)) {
                unlink($imagePath);
            }
            echo json_encode(["success" => false, "message" => "Error updating subject: " . $e->getMessage()]);
        }
    }
}
?>