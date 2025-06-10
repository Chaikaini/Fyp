<?php
$servername = "127.0.0.1";  
$username = "root";         
$password = "";             
$dbname = "the seeds";      


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    
    echo "Connection successful!";
}

// search parent database parent_id
function getParentIdByEmail($conn, $email) {
    $sql = "SELECT parent_id FROM parent WHERE parent_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['parent_id'];
    } else {
        return null;
    }
}

?>
