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

// Handle Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $parent_id = $conn->real_escape_string($_POST['parent_id']);
    $parent_name = $conn->real_escape_string($_POST['parent_name']);
    $ic_number = $conn->real_escape_string($_POST['ic_number']);
    $parent_gender = $conn->real_escape_string($_POST['parent_gender']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $parent_address = $conn->real_escape_string($_POST['parent_address']);
    $parent_relationship = $conn->real_escape_string($_POST['parent_relationship']);

    if ($parent_id) {
        $sql = "
            UPDATE parent 
            SET 
                parent_name = '$parent_name',
                ic_number = '$ic_number',
                parent_gender = '$parent_gender',
                phone_number = '$phone_number',
                parent_address = '$parent_address',
                parent_relationship = '$parent_relationship'
            WHERE parent_id = '$parent_id'
        ";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        exit;
    } else {
        echo json_encode(["success" => false, "error" => "Parent ID is missing."]);
        exit;
    }
}

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['parent_id'])) {
    $parent_id = $_POST['parent_id'];

    if ($parent_id) {
        $parent_id = $conn->real_escape_string($parent_id);
        $sql = "DELETE FROM parent WHERE parent_id = '$parent_id'";
 
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        exit;
    } else {
        echo json_encode(["success" => false, "error" => "Parent ID is missing."]);
        exit;
    }
}

// Handle Search Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_term'])) {
    $search_term = $conn->real_escape_string($_POST['search_term']);

    $sql = "
    SELECT 
        parent_id,
        parent_name,
        ic_number,
        parent_gender,
        parent_email,
        phone_number,
        parent_address,
        parent_relationship
    FROM parent
    WHERE parent_name LIKE '%$search_term%'
    ORDER BY parent_id ASC
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

// Fetch All Parents
$sql = "
    SELECT 
        parent_id,
        parent_name,
        ic_number,
        parent_gender,
        parent_email,
        phone_number,
        parent_address,
        parent_relationship
    FROM parent
    ORDER BY parent_id ASC
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
?>