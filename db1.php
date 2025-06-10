<?php


$db_host = 'localhost';  
$db_user = 'root';       
$db_pass = '';           
$db_name = 'the seeds';      


$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);


if ($conn->connect_error) {
    die("Connection Fail: " . $conn->connect_error);  
} else {
    echo "Connection Successful！";  
}
?>
