<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//admin subject search
$searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';

$sql = "SELECT * FROM subject";
if (!empty($searchTerm)) {
    
    $searchTerm = $conn->real_escape_string($searchTerm);
    $sql = "SELECT * FROM subject 
            WHERE subject_id LIKE '%$searchTerm%' 
               OR subject LIKE '%$searchTerm%'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["subject_id"] . "</td>";
        echo "<td>" . $row["subject_name"] . "</td>";
        echo "<td>" . $row["subject_price"] . "</td>";
        echo "<td>
                <i class='pointer-cursor fas fa-edit text-warning edit-btn' 
                onclick='openEditModal(
                    \"" . $row["subject_id"] . "\", 
                    \"" . addslashes($row["subject_name"]) . "\", 
                    \"" . $row["subject_price"] . "\", 
                    \"" . $row["subject_image"] . "\",
                    \"" . addslashes($row["subject_description"]) . "\"
                )'></i>
                <i class='pointer-cursor fas fa-trash-alt text-danger delete-btn' data-subject_id='" . $row['subject_id'] . "'></i>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No matching subjects found</td></tr>";
}


$conn->close();
?>
