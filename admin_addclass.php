<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "admin"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $subjectid = $_POST["subjectid"];
    $classid = $_POST["classid"];
    $year = $_POST["year"];
    $part = $_POST["part"];
    $month = $_POST["month"];
    $time = $_POST["time"];
    $teacher = $_POST["teacher"];
    $enrollment = $_POST["enrollment"];
    $status = $_POST["status"];

   
$sql = "INSERT INTO admin_class (subject_id, class_id, year, part, month, time, teacher, capacity, status) 
        VALUES ('$subjectid', '$classid', '$year', '$part', '$month', '$time', '$teacher', '$enrollment', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Class added successfully!'); window.location.href='admin class.php';</script>";
      
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
