<?php
include 'dbadmin_connection.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';


//admin class search
$sql = "SELECT * FROM admin_class WHERE subject_id = '$searchTerm'";
$result = $conn->query($sql);


$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["subject_id"] . "</td>";
        echo "<td>" . $row["class_id"] . "</td>";
        echo "<td>" . $row["year"] . "</td>";
        echo "<td>" . $row["part"] . "</td>";
        echo "<td>" . $row["month"] . "</td>";
        echo "<td>" . $row["time"] . "</td>";
        echo "<td>" . $row["teacher"] . "</td>";
        echo "<td>" . $row["capacity"] . "</td>";
        echo "<td>" . $row["status"] . "</td>";
        echo "<td>
                <i class='pointer-cursor fas fa-edit text-warning edit-btn' 
                onclick='openEditModal(
                \"" . $row["subject_id"] . "\", 
                    \"" . $row["class_id"] . "\", 
                    \"" . addslashes($row["year"]) . "\", 
                    \"" . $row["part"] . "\", 
                    \"" . $row["month"] . "\", 
                    \"" . $row["time"] . "\", 
                    \"" . $row["teacher"] . "\", 
                    \"" . $row["status"] . "\", 
                    \"" . addslashes($row["capacity"]) . "\"
                )'></i>
                <i class='pointer-cursor fas fa-trash-alt text-danger delete-btn' data-subject_id='" . $row['subject_id'] . "'></i>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No matching class found</td></tr>";
}



$conn->close();
?>
