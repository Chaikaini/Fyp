<?php
session_start();
header("Content-Type: application/json");

// Validate session
if (!isset($_SESSION['role']) || !isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Validate admin_id exists
$stmt = $conn->prepare("SELECT admin_id FROM admin WHERE admin_id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid admin ID']);
    exit;
}
$stmt->close();

// Count total children
$sql_children = "SELECT COUNT(*) AS total_children FROM child";
$result_children = $conn->query($sql_children);
$total_children = ($result_children->num_rows > 0) ? $result_children->fetch_assoc()['total_children'] : 0;

// Count total parents
$sql_parents = "SELECT COUNT(*) AS total_parents FROM parent";
$result_parents = $conn->query($sql_parents);
$total_parents = ($result_parents->num_rows > 0) ? $result_parents->fetch_assoc()['total_parents'] : 0;

// Count total subjects
$sql_subjects = "SELECT COUNT(*) AS total_subjects FROM subject";
$result_subjects = $conn->query($sql_subjects);
$total_subjects = ($result_subjects->num_rows > 0) ? $result_subjects->fetch_assoc()['total_subjects'] : 0;

// Count total staff (teachers)
$sql_users = "SELECT COUNT(*) AS total_users FROM teacher WHERE teacher_status = 'Active'";
$result_users = $conn->query($sql_users);
$total_users = ($result_users->num_rows > 0) ? $result_users->fetch_assoc()['total_users'] : 0;

// Count subject enrollment
$sql_enrollment = "SELECT s.subject_name, COUNT(r.registration_id) AS enrolled_count
                  FROM subject s
                  LEFT JOIN class c ON s.subject_id = c.subject_id
                  LEFT JOIN registration_class r ON c.class_id = r.class_id
                  GROUP BY s.subject_id, s.subject_name";
$result_enrollment = $conn->query($sql_enrollment);
$subject_enrollment = [];
while ($row = $result_enrollment->fetch_assoc()) {
    $subject_enrollment[] = $row;
}

// Count children by year
$sql_children_by_year = "SELECT child_year AS year, COUNT(*) AS count 
                        FROM child 
                        GROUP BY child_year 
                        ORDER BY child_year";
$result_children_by_year = $conn->query($sql_children_by_year);
$children_by_year = [];
while ($row = $result_children_by_year->fetch_assoc()) {
    $children_by_year[] = $row;
}

$conn->close();

echo json_encode([
    'total_children' => $total_children,
    'total_parents' => $total_parents,
    'total_subjects' => $total_subjects,
    'total_users' => $total_users,
    'subject_enrollment' => $subject_enrollment,
    'children_by_year' => $children_by_year
]);
?>