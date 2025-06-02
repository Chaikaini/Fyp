<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and retrieve POST data
    $parent_name = trim($_POST['username'] ?? '');
    $parent_email = trim($_POST['email'] ?? '');
    $parent_address = trim($_POST['address'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $parent_gender = trim($_POST['gender'] ?? '');
    $parent_relationship = trim($_POST['relationship'] ?? '');
    $parent_password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Initialize error messages array
    $errors = [];

    // Username validation
    if (empty($parent_name)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match("/^[a-zA-Z][a-zA-Z\s]{2,29}$/", $parent_name)) {
        $errors[] = "Username must be 3-30 characters long, start with a letter, and contain only English letters or spaces.";
    } elseif (preg_match("/([a-zA-Z])\1{2,}/", str_replace(' ', '', $parent_name))) {
        $errors[] = "Username cannot contain more than two identical consecutive letters (e.g., 'aaa').";
    } else {
        // Check for reserved words
        $reserved_words = ['admin', 'root', 'null', 'system'];
        if (in_array(strtolower(str_replace(' ', '', $parent_name)), $reserved_words)) {
            $errors[] = "Username cannot be a reserved word (e.g., admin, root, null, system).";
        }
        // Check for duplicate username
        $stmt = $conn->prepare("SELECT parent_name FROM parent WHERE parent_name = ?");
        $stmt->bind_param("s", $parent_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Username is already taken. Please choose another.";
        }
        $stmt->close();
    }

    // Validate email
    if (empty($parent_email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        // Check for duplicate email
        $stmt = $conn->prepare("SELECT parent_email FROM parent WHERE parent_email = ?");
        $stmt->bind_param("s", $parent_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Email is already registered.";
        }
        $stmt->close();
    }

    // Validate address
    if (empty($parent_address)) {
        $errors[] = "Address is required.";
    }

    // Validate phone number
    if (empty($phone_number)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match("/^0\d{2}-\d{3}\s\d{4,5}$/", $phone_number)) {
        $errors[] = "Phone number must be in the format 0xx-xxx xxxx or 0xx-xxx xxxxx.";
    }

    // Validate gender
    if (empty($parent_gender) || !in_array($parent_gender, ['Male', 'Female'])) {
        $errors[] = "Valid gender is required.";
    }

    // Validate relationship
    if (empty($parent_relationship) || !in_array($parent_relationship, ['Mother', 'Father', 'Guardian'])) {
        $errors[] = "Valid relationship is required.";
    }

    // Validate password requirements
    if (empty($parent_password)) {
        $errors[] = "Password is required.";
    } else {
        $missing_requirements = [];
        if (strlen($parent_password) < 8) {
            $missing_requirements[] = "be at least 8 characters long";
        }
        if (!preg_match("/[A-Z]/", $parent_password)) {
            $missing_requirements[] = "contain at least one uppercase letter";
        }
        if (!preg_match("/[a-z]/", $parent_password)) {
            $missing_requirements[] = "contain at least one lowercase letter";
        }
        if (!preg_match("/[!@#$%^&*]/", $parent_password)) {
            $missing_requirements[] = "contain at least one special character (!@#$%^&*)";
        }

        if (!empty($missing_requirements)) {
            $error_text = "Password must ";
            if (count($missing_requirements) === 1) {
                $error_text .= $missing_requirements[0] . ".";
            } elseif (count($missing_requirements) === 2) {
                $error_text .= $missing_requirements[0] . " and " . $missing_requirements[1] . ".";
            } else {
                $error_text .= implode(", ", array_slice($missing_requirements, 0, -1)) . ", and " . end($missing_requirements) . ".";
            }
            $errors[] = $error_text;
        }
    }

    // Check if passwords match
    if ($parent_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If there are errors, return them
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($parent_password, PASSWORD_BCRYPT);

    // Prepare and execute the insert query
    $stmt = $conn->prepare("INSERT INTO parent (parent_name, parent_email, parent_address, phone_number, parent_gender, parent_relationship, parent_password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $parent_name, $parent_email, $parent_address, $phone_number, $parent_gender, $parent_relationship, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . addslashes($conn->error)]);
    }

    $stmt->close();
}

$conn->close();
?>