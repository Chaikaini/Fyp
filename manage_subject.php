<?php
header('Content-Type: application/json');

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

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'getSubjects') {
        try {
            $stmt = $pdo->query("SELECT subject_id, subject_name, year, subject_price, subject_description, subject_image FROM subject");
            $subjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subjects[] = [
                    "subject_id" => $row["subject_id"],
                    "subject_name" => $row["subject_name"],
                    "year" => $row["year"],
                    "subject_price" => $row["subject_price"],
                    "subject_description" => $row["subject_description"],
                    "subject_image" => $row["subject_image"]
                ];
            }
            echo json_encode(["success" => true, "subjects" => $subjects]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } elseif ($action === 'deleteSubject') {
        $subjectId = $_GET['id'] ?? '';
        if ($subjectId) {
            try {
                $stmt = $pdo->prepare("DELETE FROM subject WHERE subject_id = ?");
                $stmt->execute([$subjectId]);
                echo json_encode(["success" => true]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No subject ID provided"]);
        }
    } elseif ($action === 'searchSubjects') {
        $searchQuery = $_GET['query'] ?? '';
        try {
            $sql = "SELECT subject_id, subject_name, year, subject_price, subject_description, subject_image 
                    FROM subject 
                    WHERE subject_id LIKE :search OR subject_name LIKE :search";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':search' => "%$searchQuery%"]);
            $subjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subjects[] = [
                    "subject_id" => $row["subject_id"],
                    "subject_name" => $row["subject_name"],
                    "year" => $row["year"],
                    "subject_price" => $row["subject_price"],
                    "subject_description" => $row["subject_description"],
                    "subject_image" => $row["subject_image"]
                ];
            }
            echo json_encode(["success" => true, "subjects" => $subjects]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input['action'] === 'addSubject') {
        try {
            $stmt = $pdo->prepare("INSERT INTO subject (subject_id, subject_name, year, subject_price, subject_description, subject_image) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['subject_id'],
                $input['subject_name'],
                $input['year'],
                $input['subject_price'],
                $input['subject_description'] ?: null,
                $input['subject_image'] ?: null
            ]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } elseif ($input['action'] === 'updateSubject') {
        try {
            $stmt = $pdo->prepare("UPDATE subject 
                                   SET subject_name = ?, year = ?, subject_price = ?, subject_description = ?, subject_image = ? 
                                   WHERE subject_id = ?");
            $stmt->execute([
                $input['subject_name'],
                $input['year'],
                $input['subject_price'],
                $input['subject_description'] ?: null,
                $input['subject_image'] ?: null,
                $input['subject_id']
            ]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }
}
?>