<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    $userType = '';
    $query = mysqli_query($conn, "SELECT * FROM admin WHERE admin_email='$email'");
    if (mysqli_num_rows($query) > 0) {
        $userType = 'admin';
    } else {
        $query = mysqli_query($conn, "SELECT * FROM teacher WHERE teacher_email='$email'");
        if (mysqli_num_rows($query) > 0) {
            $userType = 'teacher';
        }
    }

    if ($userType != '') {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = $userType;

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
            echo "OTP sent to your email. <br><br>
                <form method='post'>
                    <input type='text' name='input_otp' placeholder='Enter OTP' required><br>
                    <button type='submit' name='verify_otp'>Verify OTP</button>
                </form>";
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found in system.";
    }
}

elseif (isset($_POST['verify_otp'])) {
    $inputOtp = $_POST['input_otp'];

    if ($_SESSION['otp'] == $inputOtp) {
        echo "OTP Verified. <br><br>
            <form method='post'>
                <input type='password' name='new_password' placeholder='New Password' required><br>
                <input type='password' name='confirm_password' placeholder='Confirm Password' required><br>
                <button type='submit' name='reset_password'>Reset Password</button>
            </form>";
    } else {
        echo "Invalid OTP.";
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
            echo "Password successfully changed. <a href='admin login.html'>Go to Login</a>";
        } else {
            echo "Error updating password.";
        }
    } else {
        echo "Passwords do not match.";
    }
}

else {
?>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your Email" required><br>
        <button type="submit" name="send_otp">Send OTP</button>
    </form>
<?php
}
?>