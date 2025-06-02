<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start(); // Start output buffering
header('Content-Type: application/json'); // Set JSON content type

// Include PHPMailer manually
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Verify PHPMailer files
$phpmailer_path = 'PHPMailer.php';
$smtp_path = 'SMTP.php';
$exception_path = 'Exception.php';

if (!file_exists($phpmailer_path) || !file_exists($smtp_path) || !file_exists($exception_path)) {
    throw new Exception("PHPMailer files not found. Check paths.");
}

require $phpmailer_path;
require $smtp_path;
require $exception_path;

// Verify config.php
if (!file_exists('config.php')) {
    throw new Exception("Configuration file (config.php) not found.");
}
require 'config.php';

// Verify SMTP credentials
if (!defined('SMTP_USERNAME') || !defined('SMTP_PASSWORD')) {
    throw new Exception("SMTP_USERNAME or SMTP_PASSWORD not defined in config.php.");
}

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

// Initialize response array
$response = ["success" => false, "error" => ""];

try {
    // Check OpenSSL extension
    if (!extension_loaded('openssl')) {
        throw new Exception("OpenSSL extension not enabled.");
    }

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['send_otp'])) {
        $email = $_POST['email'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        $stmt = $conn->prepare("SELECT * FROM parent WHERE parent_email = ?");
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the recipient's name
            $user = $result->fetch_assoc();
            $recipient_name = $user['parent_name'] ?? 'User'; // Use 'User' as fallback if name is not available

            $otp = mt_rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['otp_time'] = time(); // Store OTP generation time

            // Send OTP via email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Enable debug output (for testing)
                $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Remove in production
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                // Recipients
                $mail->setFrom(SMTP_USERNAME, 'The Seeds Learning Tuition Centre');
                $mail->addAddress($email); // Recipient's email

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';

                // HTML Email Template
                $emailTemplate = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Password Reset OTP</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f9f9f9;
                            color: #333;
                            padding: 20px;
                            margin: 0;
                        }
                        .email-container {
                            background-color: #ffffff;
                            padding: 30px;
                            border-radius: 8px;
                            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                            max-width: 600px;
                            margin: 0 auto;
                        }
                        .email-header {
                            font-size: 20px;
                            font-weight: bold;
                            margin-bottom: 20px;
                            color: #4CAF50;
                        }
                        .email-content {
                            font-size: 16px;
                            line-height: 1.6;
                        }
                        .email-footer {
                            margin-top: 30px;
                            font-size: 14px;
                            color: #777;
                        }
                        .otp-code {
                            font-size: 24px;
                            font-weight: bold;
                            color: #4CAF50;
                            text-align: center;
                            margin: 20px 0;
                            padding: 10px;
                            background-color: #f5f5f5;
                            border-radius: 4px;
                        }
                        a {
                            color: #4CAF50;
                            text-decoration: none;
                            font-weight: bold;
                        }
                        a:hover {
                            text-decoration: underline;
                        }
                    </style>
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            <div>Hello ' . htmlspecialchars($recipient_name) . ',</div>
                        </div>
                        <div class="email-content">
                            <p>You have requested to reset your password. Please use the following OTP code to proceed:</p>
                            <div class="otp-code">' . htmlspecialchars($otp) . '</div>
                            <p>If you did not request this, please ignore this email or contact <a href="mailto:support@theseeds.com">support</a>.</p>
                        </div>
                        <div class="email-footer">
                            <p>Best regards,<br>The Seeds Learning Tuition Centre</p>
                            <p>This is an automated message, please do not reply to this email.</p>
                        </div>
                    </div>
                </body>
                </html>';

                $mail->Body = $emailTemplate;
                // Fallback for non-HTML email clients
                $mail->AltBody = "Hello $recipient_name,\n\nYou have requested to reset your password. Your OTP code is: $otp\nThis OTP is valid for 10 minutes.\n\nIf you did not request this, please ignore this email or contact support@theseeds.com.\n\nBest regards,\nThe Seeds Learning Tuition Centre";

                $mail->send();
                $response["success"] = true;
                // Remove in production: $response["otp"] = $otp; // For testing only
            } catch (Exception $e) {
                throw new Exception("Failed to send OTP email: " . $mail->ErrorInfo);
            }
        } else {
            throw new Exception("Email not found.");
        }
        $stmt->close();
        
    } elseif (isset($_POST['verify_otp'])) {
        $inputOtp = $_POST['input_otp'] ?? '';
        $storedOtp = $_SESSION['otp'] ?? '';
        
        // Check if OTP is expired (10 minutes)
        if (!isset($_SESSION['otp_time']) || (time() - $_SESSION['otp_time']) > 600) {
            throw new Exception("OTP has expired.");
        }
        
        if ($inputOtp == $storedOtp) {
            $response["success"] = true;
        } else {
            throw new Exception("Invalid OTP.");
        }
        
    } elseif (isset($_POST['reset_password'])) {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $email = $_SESSION['email'] ?? '';

        if (empty($email)) {
            throw new Exception("Session expired.");
        }

        if ($newPassword !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }
        
        // Validate password requirements
        $missingRequirements = [];
        if (strlen($newPassword) < 8) {
            $missingRequirements[] = "be at least 8 characters long";
        }
        if (!preg_match('/[A-Z]/', $newPassword)) {
            $missingRequirements[] = "contain at least one uppercase letter";
        }
        if (!preg_match('/[a-z]/', $newPassword)) {
            $missingRequirements[] = "contain at least one lowercase letter";
        }
        if (!preg_match('/[!@#$%^&*]/', $newPassword)) {
            $missingRequirements[] = "contain at least one special character (!@#$%^&*)";
        }

        if (!empty($missingRequirements)) {
            $errorText = "Password must ";
            if (count($missingRequirements) === 1) {
                $errorText .= $missingRequirements[0] . ".";
            } elseif (count($missingRequirements) === 2) {
                $errorText .= $missingRequirements[0] . " and " . $missingRequirements[1] . ".";
            } else {
                $errorText .= implode(", ", array_slice($missingRequirements, 0, -1)) . ", and " . $missingRequirements[count($missingRequirements) - 1] . ".";
            }
            throw new Exception($errorText);
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE parent SET parent_password = ? WHERE parent_email = ?");
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $hashedPassword, $email);
        if (!$stmt->execute()) {
            throw new Exception("Error updating password: " . $conn->error);
        }
        
        session_destroy();
        $response["success"] = true;
        $stmt->close();
    }

} catch (Exception $e) {
    $response["error"] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    
    ob_end_clean(); // Clean output buffer
    echo json_encode($response);
    exit;
}
?>