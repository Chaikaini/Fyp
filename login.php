<?php
session_start();
header('Content-Type: application/json'); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $email = $conn->real_escape_string($email);
    $sql = "SELECT * FROM parent WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            $_SESSION['parent_id'] = $row['parent_id'];
            $_SESSION['parent_name'] = $row['parent_name'];
            $_SESSION['parent_email'] = $row['parent_email'];

            echo json_encode([
                "success" => true,
                "username" => $row['parent_name'],
                "email" => $row['parent_email']
            ]);
            exit();
        } else {
            echo json_encode(["error" => "Incorrect password"]);
            exit();
        }
    } else {
        echo json_encode(["error" => "User not found"]);
        exit();
    }
}

$conn->close();
?>
