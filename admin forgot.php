<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function renderForm($content, $error = '') {
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
            .error {
                color: red;
                margin-bottom: 15px;
                font-size: 14px;
            }
            form select {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
            }
            form input {
                width: 95%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #b3d9ff;
                border-radius: 5px;
                font-size: 14px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                outline: none;
            }
            form input:focus {
                border-color: #80bdff;
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
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
            a {
                color: #17a2b8;
            }
            .strength-bar {
                width: 100%;
                height: 10px;
                background-color: #e6f0fa;
                border-radius: 5px;
                margin-bottom: 10px;
                overflow: hidden;
            }
            .strength-fill {
                height: 100%;
                border-radius: 5px;
                transition: width 0.3s ease, background-color 0.3s ease;
            }
            .strength-text {
                font-size: 14px;
                color: #666;
                text-align: left;
                margin-bottom: 15px;
            }
            .weak { background-color: #ff4d4d; }
            .medium { background-color: #ffd700; }
            .strong { background-color: #28a745; }
            .very-strong { background-color:rgb(21, 143, 127); }
        </style>
    </head>
    <body>
        <div class='container'>
    $content
    " . (!empty($error) ? "<div class='error'>$error</div>" : "") . "
</div>
    </body>
    </html>";
}

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($role == 'admin') {
        $query = mysqli_query($conn, "SELECT admin_name FROM admin WHERE admin_email='$email'");
    } elseif ($role == 'teacher') {
        $query = mysqli_query($conn, "SELECT teacher_name FROM teacher WHERE teacher_email='$email'");
    } else {
        renderForm("", "Invalid role selected.");
        exit;
    }

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $recipient_name = ($role == 'admin') ? $row['admin_name'] : $row['teacher_name'];
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = $role;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'theseeds11@gmail.com';
            $mail->Password = 'hqwq cpmp zrhv bbby';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('theseeds11@gmail.com', 'The Seeds Learning Tuition Centre');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code for Password Reset';

            $template = file_get_contents('otp_email_template.html');
            $emailBody = str_replace(
                ['{{recipient_name}}', '{{otp_code}}'],
                [$recipient_name, $otp],
                $template
            );
            $mail->Body = $emailBody;

            $mail->send();

            renderForm("
                <form method='post'>
                    <h2>Verify OTP</h2>
                    <input type='text' name='input_otp' placeholder='Enter OTP' required>
                    <button type='submit' name='verify_otp'>Verify OTP</button>
                </form>
            ");
        } catch (Exception $e) {
            renderForm("", "Failed to send email. Error: {$mail->ErrorInfo}");
        }
    } else {
        renderForm("
            <form action='' method='post'>
                <h2>Forgot Password</h2>
                <p>Select your role and enter your email to receive a reset OTP.</p>
                <select name='role' required>
                    <option value='' disabled>Select Role</option>
                    <option value='admin' " . ($role == 'admin' ? 'selected' : '') . ">Admin</option>
                    <option value='teacher' " . ($role == 'teacher' ? 'selected' : '') . ">Teacher</option>
                </select>
                <input type='email' name='email' placeholder='Enter your email' value='$email' required>
                <button type='submit' name='send_otp'>Send OTP</button>
                <p>Remember your password? <a href='admin login.html'>Login here</a></p>
            </form>
        ", "Email not found for selected role.");
    }
}

elseif (isset($_POST['verify_otp'])) {
    $inputOtp = $_POST['input_otp'];
    if ($_SESSION['otp'] == $inputOtp) {
        renderForm("
            <form method='post'>
                <h2>Reset Password</h2>
                <input type='password' name='new_password' id='new_password' placeholder='New Password' required>
                <div class='strength-bar'>
                    <div class='strength-fill' id='strength-fill'></div>
                </div>
                <div class='strength-text' id='strength-text'>Enter a password</div>
                <input type='password' name='confirm_password' placeholder='Confirm Password' required>
                <button type='submit' name='reset_password'>Reset Password</button>
            </form>
            <script>
                function evaluatePasswordStrength(password) {
                    let strength = 0;
                    const hasLower = /[a-z]/.test(password);
                    const hasUpper = /[A-Z]/.test(password);
                    const hasNumber = /\d/.test(password);
                    const hasSpecial = /[!@#$%^&*(),.?\":{}|<>]/.test(password);
                    const length = password.length;

                    if (hasLower) strength++;
                    if (hasUpper) strength++;
                    if (hasNumber) strength++;
                    if (hasSpecial) strength++;

                    if (length >= 12) strength += 1;
                    else if (length < 8) strength = Math.min(strength, 1);

                    let strengthLevel = 'weak';
                    let strengthText = '';
                    let progressWidth = 0;
                    let progressClass = 'weak';

                    if (length === 0) {
                        strengthLevel = 'none';
                        strengthText = 'Enter a password';
                        progressWidth = 0;
                    } else if (length < 8) {
                        strengthLevel = 'weak';
                        strengthText = 'Weak: Must be at least 8 characters';
                        progressWidth = 25;
                    } else if (strength <= 2) {
                        strengthLevel = 'weak';
                        strengthText = 'Weak: Add uppercase, numbers, or special characters';
                        progressWidth = 25;
                    } else if (strength === 3) {
                        strengthLevel = 'medium';
                        strengthText = 'Medium: Add special characters for stronger password';
                        progressWidth = 50;
                        progressClass = 'medium';
                    } else if (strength === 4) {
                        strengthLevel = 'strong';
                        strengthText = 'Strong: Good password!';
                        progressWidth = 75;
                        progressClass = 'strong';
                    } else {
                        strengthLevel = 'very-strong';
                        strengthText = 'Very Strong: Excellent password!';
                        progressWidth = 100;
                        progressClass = 'very-strong';
                    }

                    return { strengthLevel, strengthText, progressWidth, progressClass };
                }

                const passwordInput = document.getElementById('new_password');
                const strengthFill = document.getElementById('strength-fill');
                const strengthText = document.getElementById('strength-text');

                passwordInput.addEventListener('input', function() {
                    const result = evaluatePasswordStrength(this.value);
                    strengthFill.style.width = result.progressWidth + '%';
                    strengthFill.className = 'strength-fill ' + result.progressClass;
                    strengthText.textContent = result.strengthText;
                    strengthText.style.color = result.progressClass === 'weak' ? '#ff4d4d' : result.progressClass === 'medium' ? '#ffd700' : '#28a745';
                });
            </script>
        ");
    } else {
        renderForm("
            <form method='post'>
                <h2>Verify OTP</h2>
                <input type='text' name='input_otp' placeholder='Enter OTP' required>
                <button type='submit' name='verify_otp'>Verify OTP</button>
            </form>
        ", "Invalid OTP.");
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
            renderForm("
                <form method='post'>
                    <h2>Reset Password</h2>
                    <input type='password' name='new_password' id='new_password' placeholder='New Password' required>
                    <div class='strength-bar'>
                        <div class='strength-fill' id='strength-fill'></div>
                    </div>
                    <div class='strength-text' id='strength-text'>Enter a password</div>
                    <input type='password' name='confirm_password' placeholder='Confirm Password' required>
                    <button type='submit' name='reset_password'>Reset Password</button>
                </form>
                <script>
                    function evaluatePasswordStrength(password) {
                        let strength = 0;
                        const hasLower = /[a-z]/.test(password);
                        const hasUpper = /[A-Z]/.test(password);
                        const hasNumber = /\d/.test(password);
                        const hasSpecial = /[!@#$%^&*(),.?\":{}|<>]/.test(password);
                        const length = password.length;

                        if (hasLower) strength++;
                        if (hasUpper) strength++;
                        if (hasNumber) strength++;
                        if (hasSpecial) strength++;

                        if (length >= 12) strength += 1;
                        else if (length < 8) strength = Math.min(strength, 1);

                        let strengthLevel = 'weak';
                        let strengthText = '';
                        let progressWidth = 0;
                        let progressClass = 'weak';

                        if (length === 0) {
                            strengthLevel = 'none';
                            strengthText = 'Enter a password';
                            progressWidth = 0;
                        } else if (length < 8) {
                            strengthLevel = 'weak';
                            strengthText = 'Weak: Must be at least 8 characters';
                            progressWidth = 25;
                        } else if (strength <= 2) {
                            strengthLevel = 'weak';
                            strengthText = 'Weak: Add uppercase, numbers, or special characters';
                            progressWidth = 25;
                        } else if (strength === 3) {
                            strengthLevel = 'medium';
                            strengthText = 'Medium: Add special characters for stronger password';
                            progressWidth = 50;
                            progressClass = 'medium';
                        } else if (strength === 4) {
                            strengthLevel = 'strong';
                            strengthText = 'Strong: Good password!';
                            progressWidth = 75;
                            progressClass = 'strong';
                        } else {
                            strengthLevel = 'very-strong';
                            strengthText = 'Very Strong: Excellent password!';
                            progressWidth = 100;
                            progressClass = 'very-strong';
                        }

                        return { strengthLevel, strengthText, progressWidth, progressClass };
                    }

                    const passwordInput = document.getElementById('new_password');
                    const strengthFill = document.getElementById('strength-fill');
                    const strengthText = document.getElementById('strength-text');

                    passwordInput.addEventListener('input', function() {
                        const result = evaluatePasswordStrength(this.value);
                        strengthFill.style.width = result.progressWidth + '%';
                        strengthFill.className = 'strength-fill ' + result.progressClass;
                        strengthText.textContent = result.strengthText;
                        strengthText.style.color = result.progressClass === 'weak' ? '#ff4d4d' : result.progressClass === 'medium' ? '#ffd700' : '#28a745';
                    });
                </script>
            ", "Error updating password.");
        }
    } else {
        renderForm("
            <form method='post'>
                <h2>Reset Password</h2>
                <input type='password' name='new_password' id='new_password' placeholder='New Password' required>
                <div class='strength-bar'>
                    <div class='strength-fill' id='strength-fill'></div>
                </div>
                <div class='strength-text' id='strength-text'>Enter a password</div>
                <input type='password' name='confirm_password' placeholder='Confirm Password' required>
                <button type='submit' name='reset_password'>Reset Password</button>
            </form>
            <script>
                function evaluatePasswordStrength(password) {
                    let strength = 0;
                    const hasLower = /[a-z]/.test(password);
                    const hasUpper = /[A-Z]/.test(password);
                    const hasNumber = /\d/.test(password);
                    const hasSpecial = /[!@#$%^&*(),.?\":{}|<>]/.test(password);
                    const length = password.length;

                    if (hasLower) strength++;
                    if (hasUpper) strength++;
                    if (hasNumber) strength++;
                    if (hasSpecial) strength++;

                    if (length >= 12) strength += 1;
                    else if (length < 8) strength = Math.min(strength, 1);

                    let strengthLevel = 'weak';
                    let strengthText = '';
                    let progressWidth = 0;
                    let progressClass = 'weak';

                    if (length === 0) {
                        strengthLevel = 'none';
                        strengthText = 'Enter a password';
                        progressWidth = 0;
                    } else if (length < 8) {
                        strengthLevel = 'weak';
                        strengthText = 'Weak: Must be at least 8 characters';
                        progressWidth = 25;
                    } else if (strength <= 2) {
                        strengthLevel = 'weak';
                        strengthText = 'Weak: Add uppercase, numbers, or special characters';
                        progressWidth = 25;
                    } else if (strength === 3) {
                        strengthLevel = 'medium';
                        strengthText = 'Medium: Add special characters for stronger password';
                        progressWidth = 50;
                        progressClass = 'medium';
                    } else if (strength === 4) {
                        strengthLevel = 'strong';
                        strengthText = 'Strong: Good password!';
                        progressWidth = 75;
                        progressClass = 'strong';
                    } else {
                        strengthLevel = 'very-strong';
                        strengthText = 'Very Strong: Excellent password!';
                        progressWidth = 100;
                        progressClass = 'very-strong';
                    }

                    return { strengthLevel, strengthText, progressWidth, progressClass };
                }

                const passwordInput = document.getElementById('new_password');
                const strengthFill = document.getElementById('strength-fill');
                const strengthText = document.getElementById('strength-text');

                passwordInput.addEventListener('input', function() {
                    const result = evaluatePasswordStrength(this.value);
                    strengthFill.style.width = result.progressWidth + '%';
                    strengthFill.className = 'strength-fill ' + result.progressClass;
                    strengthText.textContent = result.strengthText;
                    strengthText.style.color = result.progressClass === 'weak' ? '#ff4d4d' : result.progressClass === 'medium' ? '#ffd700' : '#28a745';
                });
            </script>
        ", "Passwords do not match.");
    }
}
?>