<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Option 1: If subject_id is provided in GET, use it
    $subject_id = isset($_GET['subject_id']) && !empty(trim($_GET['subject_id'])) 
        ? explode(',', trim($_GET['subject_id'])) 
        : null;

    // Debug: Log all GET parameters and raw input
    error_log("GET parameters: " . print_r($_GET, true));
    error_log("Raw subject_id: " . ($_GET['subject_id'] ?? 'none'));

    // Option 2: If no subject_id is provided, fetch all subject_ids from class table
    if (!$subject_id || empty($subject_id) || in_array('', $subject_id)) {
        $stmt = $conn->prepare("SELECT DISTINCT subject_id FROM class WHERE class_status = 'available'");
        $stmt->execute();
        $subject_id = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Debug: Log the fetched subject_id
        error_log("Fetched subject_id from class: " . print_r($subject_id, true));

        if (empty($subject_id)) {
            error_log("No subject IDs found in class table with status 'available'");
            echo json_encode(['error' => 'No available subjects found']);
            exit;
        }
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

    // Prepare and execute the query with class_status filter
    $stmt = $conn->prepare("
        SELECT c.class_id, c.year, c.class_time, c.class_venue, t.teacher_id, t.teacher_name, t.teacher_gender, 
               t.teacher_email, t.teacher_phone_number, t.teacher_image
        FROM class c
        JOIN teacher t ON c.teacher_id = t.teacher_id
        WHERE c.subject_id IN ($placeholders) AND c.class_status = 'available'
        ORDER BY c.year
    ");
    $stmt->execute($subject_id);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Log the raw query results
    error_log("Query results: " . print_r($results, true));

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

    // Debug: Log the grouped teachers
    error_log("Teachers by year: " . print_r($teachers_by_year, true));

    // If no teachers found, return an error
    if (empty($teachers_by_year)) {
        error_log("No teachers found for subject_id: " . implode(',', $subject_id));
        echo json_encode(['error' => 'No available teachers found for the selected subject(s)']);
        exit;
    }

    echo json_encode($teachers_by_year);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
$conn = null;
?>