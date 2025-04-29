<?php

$conn = new mysqli("localhost", "root", "", "the seeds");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET["token"])) {
    $token = $conn->real_escape_string($_GET["token"]);
    $result = $conn->query("SELECT email FROM password_resets WHERE token='$token' AND expires_at > NOW()");

    if ($result->num_rows == 0) {
        die("Invalid or expired token.");
    }

    $row = $result->fetch_assoc();
    $email = $row["email"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = password_hash($_POST["password"], PASSWORD_BCRYPT);

        $conn->query("UPDATE users SET password='$new_password' WHERE email='$email'");

        $conn->query("DELETE FROM password_resets WHERE email='$email'");

        echo "<p>Password has been reset successfully! <a href='login.html'>Login here</a></p>";
        exit;
    }
} else {
    die("No token provided.");
}
?>

<form method="POST">
    <input type="password" name="password" required placeholder="Enter new password">
    <button type="submit">Reset Password</button>
</form>
