<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'search':
        $search_term = '%' . ($conn->real_escape_string($_POST['search_term'] ?? '')) . '%';
        $sql = "
            SELECT 
                r.registration_id, 
                p.parent_name, 
                c.class_id, 
                ch.child_name, 
                s.subject_name, 
                t.teacher_name, 
                pmt.payment_total_amount 
            FROM registration_class r
            LEFT JOIN parent p ON r.parent_id = p.parent_id
            LEFT JOIN class c ON r.class_id = c.class_id
            LEFT JOIN child ch ON r.child_id = ch.child_id
            LEFT JOIN subject s ON c.subject_id = s.subject_id
            LEFT JOIN teacher t ON c.teacher_id = t.teacher_id
            LEFT JOIN payment pmt ON r.payment_id = pmt.payment_id
            WHERE 
                p.parent_name LIKE ? OR
                r.registration_id LIKE ? OR
                c.class_id LIKE ? OR
                ch.child_name LIKE ? OR
                s.subject_name LIKE ? OR
                t.teacher_name LIKE ?
            ORDER BY r.registration_id ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
        $stmt->execute();
        $result = $stmt->get_result();
        $registrations = [];
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
        echo json_encode($registrations);
        $stmt->close();
        break;

    case 'getSubjects':
        $sql = "SELECT subject_id, subject_name FROM subject";
        $result = $conn->query($sql);
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
        echo json_encode($subjects);
        break;

    case 'getClasses':
        $subject_id = $_POST['subject_id'] ?? '';
        if (!empty($subject_id)) {
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
        } else {
            echo json_encode([]);
        }
        break;

    case 'filter':
        $subject_id = $_POST['subject_id'] ?? '';
        $class_id = $_POST['class_id'] ?? '';
        if (!empty($subject_id) && !empty($class_id)) {
            $stmt = $conn->prepare("
                SELECT 
                    r.registration_id, 
                    p.parent_name, 
                    c.class_id, 
                    ch.child_name, 
                    s.subject_name, 
                    t.teacher_name, 
                    pmt.payment_total_amount 
                FROM registration_class r
                LEFT JOIN parent p ON r.parent_id = p.parent_id
                LEFT JOIN class c ON r.class_id = c.class_id
                LEFT JOIN child ch ON r.child_id = ch.child_id
                LEFT JOIN subject s ON c.subject_id = s.subject_id
                LEFT JOIN teacher t ON c.teacher_id = t.teacher_id
                LEFT JOIN payment pmt ON r.payment_id = pmt.payment_id
                WHERE c.subject_id = ? AND r.class_id = ?
                ORDER BY r.registration_id ASC
            ");
            $stmt->bind_param("ss", $subject_id, $class_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $registrations = [];
            while ($row = $result->fetch_assoc()) {
                $registrations[] = $row;
            }
            echo json_encode($registrations);
            $stmt->close();
        } else {
            echo json_encode([]);
        }
        break;

    default:
        // Default: return all registrations
        $sql = "
            SELECT  
                r.registration_id, 
                p.parent_name, 
                c.class_id, 
                ch.child_name, 
                s.subject_name, 
                t.teacher_name, 
                pmt.payment_total_amount 
            FROM registration_class r
            LEFT JOIN parent p ON r.parent_id = p.parent_id
            LEFT JOIN class c ON r.class_id = c.class_id
            LEFT JOIN child ch ON r.child_id = ch.child_id
            LEFT JOIN subject s ON c.subject_id = s.subject_id
            LEFT JOIN teacher t ON c.teacher_id = t.teacher_id
            LEFT JOIN payment pmt ON r.payment_id = pmt.payment_id
            ORDER BY r.registration_id ASC
        ";
        $result = $conn->query($sql);
        $registrations = [];
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
        echo json_encode($registrations);
        break;
}

$conn->close();
?>