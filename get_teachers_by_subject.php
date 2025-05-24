<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Debug: Log all GET parameters and raw input
    error_log("GET parameters: " . print_r($_GET, true));
    error_log("Raw subject_id: " . ($_GET['subject_id'] ?? 'none'));

    // Check for subject_id parameter
    $subject_id = isset($_GET['subject_id']) && !empty(trim($_GET['subject_id'])) ? explode(',', trim($_GET['subject_id'])) : null;

    // Debug: Log the exploded subject_id
    error_log("Exploded subject_id: " . print_r($subject_id, true));

    // Validate subject_id
    if (!$subject_id || empty($subject_id) || in_array('', $subject_id)) {
        error_log("Validation failed: subject_id is empty or contains empty values");
        echo json_encode(['error' => 'Valid Subject IDs are required']);
        exit;
    }

    // Sanitize subject_id to ensure they are numeric
    $subject_id = array_filter($subject_id, 'is_numeric');
    if (empty($subject_id)) {
        error_log("Validation failed: No numeric subject IDs after filtering");
        echo json_encode(['error' => 'No valid numeric Subject IDs provided']);
        exit;
    }

    // Debug: Log the sanitized subject_id
    error_log("Sanitized subject_id: " . print_r($subject_id, true));

    // Create placeholders for SQL query
    $placeholders = implode(',', array_fill(0, count($subject_id), '?'));

    // Prepare and execute the query (removed class_status condition)
    $stmt = $conn->prepare("
        SELECT c.class_id, c.year, c.class_time, c.class_venue, t.teacher_id, t.teacher_name, t.teacher_gender, 
               t.teacher_email, t.teacher_phone_number, t.teacher_image
        FROM class c
        JOIN teacher t ON c.teacher_id = t.teacher_id
        WHERE c.subject_id IN ($placeholders)
        ORDER BY c.year
    ");
    $stmt->execute($subject_id);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by year
    $teachers_by_year = [];
    foreach ($results as $row) {
        $year = $row['year'];
        if (!isset($teachers_by_year[$year])) {
            $teachers_by_year[$year] = [];
        }
        $teachers_by_year[$year][] = [
            'teacher_name' => $row['teacher_name'],
            'teacher_gender' => $row['teacher_gender'],
            'teacher_email' => $row['teacher_email'],
            'teacher_phone_number' => $row['teacher_phone_number'],
            'teacher_image' => $row['teacher_image'] ?: 'img/default_teacher.jpg',
            'class_time' => $row['class_time'],
            'class_venue' => $row['class_venue']
        ];
    }

    echo json_encode($teachers_by_year);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
$conn = null;
?>