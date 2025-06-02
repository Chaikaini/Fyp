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
    $username = trim($_POST['username'] ?? '');

    if (empty($username)) {
        echo json_encode(['exists' => false, 'message' => 'Username is required']);
        exit();
    }

    $stmt = $conn->prepare("SELECT parent_name FROM parent WHERE parent_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true, 'message' => 'Username is already taken']);
    } else {
        echo json_encode(['exists' => false, 'message' => 'Username is available']);
    }

    $stmt->close();
}

$conn->close();
?>