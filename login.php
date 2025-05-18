<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $parent_email = trim($_POST['email'] ?? '');
        $parent_password = trim($_POST['password'] ?? '');

        if (empty($parent_email) || empty($parent_password)) {
            echo json_encode(['success' => false, 'error' => 'Email and password are required']);
            exit();
        }

        // Use prepared statement
        $stmt = $conn->prepare("SELECT parent_id, parent_name, parent_email, parent_password FROM parent WHERE parent_email = ?");
        $stmt->bind_param("s", $parent_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['parent_password'];

            if (password_verify($parent_password, $hashed_password)) {
                $_SESSION['parent_id'] = $row['parent_id'];
                $_SESSION['parent_name'] = $row['parent_name'];
                $_SESSION['parent_email'] = $row['parent_email'];

                echo json_encode([
                    'success' => true,
                    'parent_id' => $row['parent_id'],
                    'parent_name' => $row['parent_name'],
                    'parent_email' => $row['parent_email']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Incorrect password']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>