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
    $parent_email = $_POST['email'];
    $parent_password = $_POST['password'];

    $parent_email = $conn->real_escape_string($parent_email);
    $sql = "SELECT * FROM parent WHERE parent_email = '$parent_email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['parent_password'];

        if (password_verify($parent_password, $hashed_password)) {
            $_SESSION['parent_id'] = $row['parent_id'];
            $_SESSION['parent_name'] = $row['parent_name'];
            $_SESSION['parent_email'] = $row['parent_email'];

            echo json_encode([
                "success" => true,
                "parent_id" => $row['parent_id'],
                "parent_name" => $row['parent_name'],
                "parent_email" => $row['parent_email']
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
