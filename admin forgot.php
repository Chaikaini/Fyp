<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

function renderForm($content) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Forgot Password</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link href='img/the seeds.jpg' rel='icon' type='image/png'>
        <link href='https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&display=swap' rel='stylesheet'>
        <style>
            body {
                margin: 0;
                font-family: 'Heebo', sans-serif;
                display: flex;
                height: 100vh;
                justify-content: center;
                align-items: center;
                background-color: #f4f4f4;
            }
            .container {
                width: 100%;
                max-width: 400px;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            form input, form select {
                width: 95%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
            }
            form button {
                width: 100%;
                padding: 10px;
                background-color: #17a2b8;
                border: none;
                border-radius: 5px;
                color: #ffffff;
                font-size: 16px;
                cursor: pointer;
            }
            form button:hover {
                background-color: #138496;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            $content
        </div>
    </body>
    </html>";
}

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($role == 'admin') {
        $query = mysqli_query($conn, "SELECT * FROM admin WHERE admin_email='$email'");
    } elseif ($role == 'teacher') {
        $query = mysqli_query($conn, "SELECT * FROM teacher WHERE teacher_email='$email'");
    } else {
        renderForm("<p>Invalid role selected.</p>");
        exit;
    }

    if (mysqli_num_rows($query) > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = $role;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.sendgrid.net';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SENDGRID_USERNAME'];
            $mail->Password   = $_ENV['SENDGRID_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('chaikaini@gmail.com', 'Forgot Password System');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your OTP code is <b>$otp</b>";

            $mail->send();

            renderForm("
                <form method='post'>
                    <h2>Verify OTP</h2>
                    <input type='text' name='input_otp' placeholder='Enter OTP' required>
                    <button type='submit' name='verify_otp'>Verify OTP</button>
                </form>
            ");
        } catch (Exception $e) {
            renderForm("<p>Failed to send email. Error: {$mail->ErrorInfo}</p>");
        }
    } else {
        renderForm("<p>Email not found for selected role.</p>");
    }
}

elseif (isset($_POST['verify_otp'])) {
    $inputOtp = $_POST['input_otp'];
    if ($_SESSION['otp'] == $inputOtp) {
        renderForm("
            <form method='post'>
                <h2>Reset Password</h2>
                <input type='password' name='new_password' placeholder='New Password' required>
                <input type='password' name='confirm_password' placeholder='Confirm Password' required>
                <button type='submit' name='reset_password'>Reset Password</button>
            </form>
        ");
    } else {
        renderForm("<p>Invalid OTP.</p>");
    }
}

elseif (isset($_POST['reset_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_SESSION['email'];
    $userType = $_SESSION['user_type'];

    if ($newPassword == $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($userType == 'admin') {
            $update = mysqli_query($conn, "UPDATE admin SET admin_password='$hashedPassword' WHERE admin_email='$email'");
        } elseif ($userType == 'teacher') {
            $update = mysqli_query($conn, "UPDATE teacher SET teacher_password='$hashedPassword' WHERE teacher_email='$email'");
        }

        if ($update) {
            session_destroy();
            header("Location: admin login.html");
            exit();
        } else {
            renderForm("<p>Error updating password.</p>");
        }
    } else {
        renderForm("<p>Passwords do not match.</p>");
    }
}
?>
