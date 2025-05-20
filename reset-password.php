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
                $mail->setFrom(SMTP_USERNAME, 'The Seeds Learning Centre');
                $mail->addAddress($email); // Recipient's email

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body = "Dear User,<br><br>Your OTP for password reset is: <b>$otp</b><br>This OTP is valid for 10 minutes.<br><br>Best regards,<br>The Seeds Learning Centre";
                $mail->AltBody = "Your OTP for password reset is: $otp\nThis OTP is valid for 10 minutes.\n\nBest regards,\nThe Seeds Learning Centre";

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