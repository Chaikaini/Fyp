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

    if ($action === 'getClasses') {
        try {
            $stmt = $pdo->query("SELECT * FROM class");
            $classes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $classes[] = [
                    "class_id" => $row["class_id"],
                    "subject_id" => $row["subject_id"],
                    "path" => $row["part_id"],
                    "time" => $row["class_time"],
                    "capacity" => $row["class_capacity"],
                    "enrolled" => $row["class_enrolled"],
                    "status" => $row["class_status"]
                ];
            }
            echo json_encode(["success" => true, "classes" => $classes]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }

    } elseif ($action === 'deleteClass') {
        $classId = $_GET['id'] ?? '';
        if ($classId) {
            try {
                $stmt = $pdo->prepare("DELETE FROM class WHERE class_id = ?");
                $stmt->execute([$classId]);
                echo json_encode(["success" => true]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No class ID provided"]);
        }

    } elseif ($action === 'getStudentsBySubjectId') {
        $subjectId = $_GET['subject_id'] ?? '';
        if ($subjectId) {
            try {
                $stmt = $pdo->prepare("
                    SELECT c.child_id, c.child_name, c.child_gender, c.child_birthday, c.child_school, c.child_year
                    FROM class cl
                    JOIN registration_class rc ON cl.class_id = rc.class_id
                    JOIN child c ON rc.child_id = c.child_id
                    WHERE cl.subject_id = ?
                ");
                $stmt->execute([$subjectId]);
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["success" => true, "students" => $students]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No subject ID provided"]);
        }

    } elseif ($action === 'searchClasses') {
        $classId = $_GET['class_id'] ?? '';
        $subjectId = $_GET['subject_id'] ?? '';

        $sql = "SELECT * FROM class WHERE class_id LIKE :search OR subject_id LIKE :search";
        $params = [':search' => "%$classId%"];

        if (!empty($classId)) {
            $sql .= " AND class_id = :class_id";
            $params[':class_id'] = $classId;
        }

        if (!empty($subjectId)) {
            $sql .= " AND subject_id = :subject_id";
            $params[':subject_id'] = $subjectId;
        }

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $classes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $classes[] = [
                    "class_id" => $row["class_id"],
                    "subject_id" => $row["subject_id"],
                    "path" => $row["part_id"],
                    "time" => $row["class_time"],
                    "capacity" => $row["class_capacity"],
                    "enrolled" => $row["class_enrolled"],
                    "status" => $row["class_status"]
                ];
            }
            echo json_encode(["success" => true, "classes" => $classes]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input['action'] === 'addClass') {
        try {
            $stmt = $pdo->prepare("INSERT INTO class (class_id, subject_id, part_id, class_time, class_capacity, class_enrolled, class_status)
                                   VALUES (?, ?, ?, ?, ?, 0, ?)");
            $stmt->execute([
                $input['class_id'],
                $input['subject_id'],
                $input['path'],
                $input['time'],
                $input['capacity'],
                $input['status']
            ]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }

    } elseif ($input['action'] === 'registerStudent') {
        $classId = $input['class_id'] ?? '';
        $childId = $input['child_id'] ?? '';
        if ($classId && $childId) {
            try {
                $stmt = $pdo->prepare("INSERT INTO registration_class (class_id, child_id) VALUES (?, ?)");
                $stmt->execute([$classId, $childId]);

                $stmt = $pdo->prepare("UPDATE class SET class_enrolled = class_enrolled + 1 WHERE class_id = ?");
                $stmt->execute([$classId]);

                echo json_encode(["success" => true]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Missing class_id or child_id"]);
        }
    }
}

elseif ($action === 'getSubjects') {
    try {
        $stmt = $pdo->query("SELECT subject_id,subject_name FROM subject");
        $subjects = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subjects[] = [
                "subject_id" => $row["subject_id"],
                "subject_name" => $row["subject_name"]
            ];
        }
        echo json_encode(["success" => true, "subjects" => $subjects]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

elseif ($action === 'getParts') {
    try {
        $stmt = $pdo->query("SELECT part_id, part_name FROM part");
        $parts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parts[] = [
                "part_id" => $row["part_id"],
                "part_name" => $row["part_name"]
            ];
        }
        echo json_encode(["success" => true, "parts" => $parts]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

?>