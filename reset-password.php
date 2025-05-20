<?php
ob_start(); // Start output buffering at the very beginning
header('Content-Type: application/json'); // Set this at the very beginning

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

// Initialize response array
$response = ["success" => false, "error" => ""];

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['send_otp'])) {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        $stmt = $conn->prepare("SELECT * FROM parent WHERE parent_email = ?");
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $conn->error);
        }
        
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $otp = mt_rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['otp_time'] = time(); // Store OTP generation time
            
            // TODO: Implement actual email sending here
            // mail($email, "Password Reset OTP", "Your OTP is: $otp");
            
            $response["success"] = true;
            $response["otp"] = $otp; // For testing only
        } else {
            throw new Exception("Email not found.");
        }
        $stmt->close();
        
    } elseif (isset($_POST['verify_otp'])) {
        $inputOtp = $_POST['input_otp'];
        $storedOtp = $_SESSION['otp'] ?? '';
        
        // Check if OTP is expired (e.g., 10 minutes)
        if (!isset($_SESSION['otp_time']) || (time() - $_SESSION['otp_time']) > 600) {
            throw new Exception("OTP has expired.");
        }
        
        if ($inputOtp == $storedOtp) {
            $response["success"] = true;
        } else {
            throw new Exception("Invalid OTP.");
        }
        
    } elseif (isset($_POST['reset_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        $email = $_SESSION['email'] ?? '';

        if (empty($email)) {
            throw new Exception("Session expired.");
        }

        if ($newPassword !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }
        
        if (strlen($newPassword) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
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
    
    ob_end_clean(); // Clean any output
    echo json_encode($response);
    exit;
}

$conn->close();
ob_end_flush(); // Send output
?>