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
            $stmt = $pdo->query("
                SELECT c.*, s.subject_name, p.part_name, t.teacher_name
                FROM class c
                JOIN subject s ON c.subject_id = s.subject_id
                JOIN part p ON c.part_id = p.part_id
                JOIN teacher t ON c.teacher_id = t.teacher_id
            ");
            $classes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $classes[] = [
                    "class_id" => $row["class_id"],
                    "subject_id" => $row["subject_id"],
                    "subject_name" => $row["subject_name"],
                    "path" => $row["part_name"],
                    "teacher_id" => $row["teacher_id"],
                    "teacher_name" => $row["teacher_name"],
                    "term" => $row["term"],
                    "year" => $row["year"],
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

        $sql = "
            SELECT c.*, s.subject_name, p.part_name, t.teacher_name
            FROM class c
            JOIN subject s ON c.subject_id = s.subject_id
            JOIN part p ON c.part_id = p.part_id
            JOIN teacher t ON c.teacher_id = t.teacher_id
            WHERE c.class_id LIKE :search OR s.subject_name LIKE :search
        ";
        $params = [':search' => "%$classId%"];

        if (!empty($classId)) {
            $sql .= " AND c.class_id = :class_id";
            $params[':class_id'] = $classId;
        }

        if (!empty($subjectId)) {
            $sql .= " AND c.subject_id = :subject_id";
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
                    "subject_name" => $row["subject_name"],
                    "path" => $row["part_name"],
                    "teacher_id" => $row["teacher_id"],
                    "teacher_name" => $row["teacher_name"],
                    "term" => $row["term"],
                    "year" => $row["year"],
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
    } elseif ($action === 'getTeachers') {
        try {
            $stmt = $pdo->query("SELECT teacher_id, teacher_name FROM teacher");
            $teachers = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $teachers[] = [
                    "teacher_id" => $row["teacher_id"],
                    "teacher_name" => $row["teacher_name"]
                ];
            }
            echo json_encode(["success" => true, "teachers" => $teachers]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } elseif ($action === 'getYears') {
        try {
            $stmt = $pdo->query("SELECT DISTINCT year FROM class ORDER BY year");
            $years = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $years[] = $row["year"];
            }
            echo json_encode(["success" => true, "years" => $years]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } elseif ($action === 'getNextClassId') {
        try {
            $subjectId = $_GET['subject_id'] ?? '';
            $year = $_GET['year'] ?? '';

            if (!$subjectId || !$year) {
                echo json_encode(["success" => false, "message" => "Subject ID and year are required"]);
                exit;
            }

            // Map subject_id to abbreviation
            $stmt = $pdo->prepare("SELECT subject_name FROM subject WHERE subject_id = ?");
            $stmt->execute([$subjectId]);
            $subjectName = $stmt->fetchColumn();
            $abbreviation = '';
            if ($subjectName === 'Math') $abbreviation = 'Mat';
            elseif ($subjectName === 'English') $abbreviation = 'Eng';
            elseif ($subjectName === 'Melayu') $abbreviation = 'Mly';
            else {
                echo json_encode(["success" => false, "message" => "Unsupported subject"]);
                exit;
            }

            // Map year to first digit
            $yearDigit = '';
            if ($year === 'Year 1') $yearDigit = '0';
            elseif ($year === 'Year 2') $yearDigit = '2';
            else {
                echo json_encode(["success" => false, "message" => "Unsupported year"]);
                exit;
            }

            // Find the highest number for the prefix
            $prefix = $abbreviation . $yearDigit;
            $stmt = $pdo->prepare("SELECT class_id FROM class WHERE class_id LIKE ? ORDER BY class_id DESC LIMIT 1");
            $stmt->execute([$prefix . '%']);
            $lastClassId = $stmt->fetchColumn();

            $nextNumber = 1;
            if ($lastClassId) {
                $numberPart = substr($lastClassId, 4, 3); // Extract the last 3 digits (e.g., "001" from "Mat0001")
                $currentNumber = (int)$numberPart;
                $nextNumber = $currentNumber + 1;
            }

            $nextClassId = $prefix . str_pad((string)$nextNumber, 3, '0', STR_PAD_LEFT); // e.g., "Mat0003"
            echo json_encode(["success" => true, "class_id" => $nextClassId]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input['action'] === 'addClass') {
        try {
            $stmt = $pdo->prepare("INSERT INTO class (class_id, subject_id, part_id, teacher_id, term, year, class_time, class_capacity, class_enrolled, class_status)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
            $stmt->execute([
                $input['class_id'],
                $input['subject_id'],
                $input['path'],
                $input['teacher_id'],
                $input['term'],
                $input['year'],
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
        $stmt = $pdo->query("SELECT subject_id, subject_name FROM subject");
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