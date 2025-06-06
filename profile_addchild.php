<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "the seeds";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    if (!isset($_SESSION['parent_id'])) {
        throw new Exception('Unauthorized access');
    }

    $parent_id = $_SESSION['parent_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate required fields
        $required_fields = ['child_name', 'child_gender', 'child_kidNumber', 'child_birthday', 'child_school', 'child_year'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        $child_name      = $_POST['child_name'];
        $child_gender    = $_POST['child_gender'];
        $child_kidNumber = $_POST['child_kidNumber'];
        $child_birthday  = $_POST['child_birthday'];
        $child_school    = $_POST['child_school'];
        $child_year = intval($_POST['child_year']);
        
        // Handle image upload
        $child_image = 'img/user.jpg'; // Default image
        
        if (isset($_FILES['child_image']) && $_FILES['child_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "uploads/child_images/";
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['child_image']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid('child_', true) . '.' . $file_extension;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['child_image']['tmp_name'], $target_file)) {
                $child_image = $target_file;
            }
        }

        // Insert into database
        $sql = "INSERT INTO child (parent_id, child_name, child_gender, child_kidNumber, child_birthday, child_school, child_year, child_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement');
        }

        $stmt->bind_param("isssssss", $parent_id, $child_name, $child_gender, $child_kidNumber, $child_birthday, $child_school, $child_year, $child_image);

        if (!$stmt->execute()) {
            throw new Exception('Failed to add child: ' . $stmt->error);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Child profile information added successfully',
        ]);
        
    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) { $stmt->close();}
    if (isset($conn)) { $conn->close();}
}