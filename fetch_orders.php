<?php
file_put_contents('debug_log.txt', print_r($_POST, true), FILE_APPEND);

session_start();

$servername = "localhost";
$username = "root";  
$password = "";      
$dbname = "the seeds";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_id'])) {
    $registration_id = $_POST['registration_id'];

    if ($registration_id) {
        $registration_id = $conn->real_escape_string($registration_id);
        $sql = "DELETE FROM registration_class WHERE registration_id = '$registration_id'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        exit;
    } else {
        echo json_encode(["success" => false, "error" => "Registration ID is missing."]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_term'])) {
    $search_term = $conn->real_escape_string($_POST['search_term']);

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
        LEFT JOIN subject s ON r.subject_id = s.subject_id
        LEFT JOIN teacher t ON r.teacher_id = t.teacher_id
        LEFT JOIN payment pmt ON r.payment_id = pmt.payment_id
        WHERE 
            p.parent_name LIKE '%$search_term%' OR
            r.registration_id LIKE '%$search_term%' OR
            c.class_id LIKE '%$search_term%' OR
            ch.child_name LIKE '%$search_term%' OR
            s.subject_name LIKE '%$search_term%' OR
            t.teacher_name LIKE '%$search_term%'
        ORDER BY r.registration_id ASC
    ";

    $result = $conn->query($sql);

    $registrations = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
    }

    echo json_encode($registrations);
    exit;
}

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
    LEFT JOIN subject s ON r.subject_id = s.subject_id
    LEFT JOIN teacher t ON r.teacher_id = t.teacher_id
    LEFT JOIN payment pmt ON r.payment_id = pmt.payment_id
    ORDER BY r.registration_id ASC
";

$result = $conn->query($sql);

$registrations = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
}

echo json_encode($registrations);
$conn->close();
