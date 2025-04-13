<?php
header('Content-Type: application/json'); 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $child_name = mysqli_real_escape_string($connect, $_POST['child_name']);
    $child_gender = mysqli_real_escape_string($connect, $_POST['child_gender']);
    $child_kidNumber = mysqli_real_escape_string($connect, $_POST['child_kidNumber']);
    $child_birthday = mysqli_real_escape_string($connect, $_POST['child_birthday']);
    $child_school = mysqli_real_escape_string($connect, $_POST['child_school']);
    $child_year = mysqli_real_escape_string($connect, $_POST['child_year']);
    $parent_id = $_POST['parent_id'];

    
    $sql = "UPDATE child SET 
                child_name = ?, 
                child_gender = ?, 
                child_birthday = ?, 
                child_school = ?, 
                child_year = ?
            WHERE child_kidNumber = ? AND parent_id = ?";

   
    $stmt = mysqli_prepare($connect, $sql);
    if ($stmt) {
        
        mysqli_stmt_bind_param($stmt, "sssssss", $child_name, $child_gender, $child_birthday, $child_school, $child_year, $child_kidNumber, $parent_id);
        
      
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true]); 
        } else {
            echo json_encode(["success" => false, "error" => "Error updating child information: " . mysqli_error($connect)]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["success" => false, "error" => "SQL statement preparation failed: " . mysqli_error($connect)]);
    }

   
    mysqli_close($connect);
}
?>
