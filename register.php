<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_name = $_POST['parent_name'];
    $parent_email = $_POST['parent_email'];
    $parent_address = $_POST['parent_address'];
    $phone_number = $_POST['phone_number'];
    $parent_gender = $_POST['parent_gender'];
    $parent_relationship = $_POST['parent_relationship'];
    $parent_password = $_POST['parent_password'];
    $confirm_password = $_POST['confirm_password']; 

    if ($parent_password !== $confirm_password) {
        echo "Error: Passwords do not match!";
        exit();
    }

    $hashed_password = password_hash($parent_password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO parent (parent_name, parent_email, parent_address, phone_number, parent_gender, parent_relationship, parent_password) 
            VALUES ('$parent_name', '$parent_email', '$parent_address', '$phone_number', '$parent_gender', '$parent_relationship', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . addslashes($conn->error);
    }
}

$conn->close();
?>
