<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$username = "root";
$password = "";
$database = "the seeds";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'Admin') {
        $stmt_admin = $conn->prepare("SELECT admin_id, admin_name, admin_password FROM admin WHERE admin_email = ?");
        $stmt_admin->bind_param("s", $email);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();

        if ($result_admin->num_rows === 1) {
            $admin = $result_admin->fetch_assoc();
            if ($password === $admin['admin_password']) {
                unset($_SESSION['teacher_id'], $_SESSION['teacher_name'], $_SESSION['teacher_email']);

                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['admin_name'];
                $_SESSION['admin_email'] = $email;
                $_SESSION['role'] = 'Admin';

                echo json_encode(['success' => true, 'role' => 'Admin']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid administrator password']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin account not found']);
            exit;
        }
    } elseif ($role === 'Teacher') {
        $stmt_teacher = $conn->prepare("SELECT teacher_id, teacher_name, teacher_password FROM teacher WHERE teacher_email = ?");
        $stmt_teacher->bind_param("s", $email);
        $stmt_teacher->execute();
        $result_teacher = $stmt_teacher->get_result();

        if ($result_teacher->num_rows === 1) {
            $teacher = $result_teacher->fetch_assoc();
            if (password_verify($password, $teacher['teacher_password'])) {
                unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_email']);

                $_SESSION['teacher_id'] = $teacher['teacher_id'];
                $_SESSION['teacher_name'] = $teacher['teacher_name'];
                $_SESSION['teacher_email'] = $email;
                $_SESSION['role'] = 'Teacher';

                echo json_encode(['success' => true, 'role' => 'Teacher']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid teacher password']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Teacher account not found']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
        exit;
    }

    $stmt_admin->close();
    $stmt_teacher->close();
    $conn->close();
    exit;
}
?>
