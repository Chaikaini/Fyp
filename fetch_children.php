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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['child_id'])) {
    $child_id = $_POST['child_id'];

    if ($child_id) {
        $child_id = $conn->real_escape_string($child_id);
        $sql = "DELETE FROM child WHERE child_id = '$child_id'";
 
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        exit;
    } else {
        echo json_encode(["success" => false, "error" => "Child ID is missing."]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_term'])) {
    $search_term = $conn->real_escape_string($_POST['search_term']);

    $sql = "
    SELECT 
        ch.child_id, 
        ch.child_name, 
        p.parent_name, 
        ch.child_gender, 
        ch.child_kidNumber, 
        ch.child_birthday, 
        ch.child_school, 
        ch.child_year
    FROM child ch
    LEFT JOIN parent p ON ch.parent_id = p.parent_id
    WHERE ch.child_name LIKE '%$search_term%'
    ORDER BY ch.child_id ASC
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
        ch.child_id, 
        ch.child_name, 
        p.parent_name, 
        ch.child_gender, 
        ch.child_kidNumber, 
        ch.child_birthday, 
        ch.child_school, 
        ch.child_year,
        ch.child_register_date
    FROM child ch
    LEFT JOIN parent p ON ch.parent_id = p.parent_id
    ORDER BY ch.child_id ASC
";

$result = $conn->query($sql);

$registrations = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $birthYear = date("Y", strtotime($row['child_birthday']));
        $registerYear = date("Y", strtotime($row['child_register_date']));
$calculatedYear = calculateYear($birthYear, $registerYear);

        if ($row['child_year'] !== $calculatedYear) {
            $updateSql = "UPDATE child SET child_year = '$calculatedYear' WHERE child_id = " . $row['child_id'];
            $conn->query($updateSql);
        }

        $row['child_year'] = $calculatedYear; 
        $registrations[] = $row;
    }
}

echo json_encode($registrations);
$conn->close();



function calculateYear($birthYear, $registerYear) {
    $age = $registerYear - $birthYear;  
    if ($age < 7) {
        return 1;  
    } else {
        return $age - 6; 
    }
}




