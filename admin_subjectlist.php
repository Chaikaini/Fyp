<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "the seeds";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM subject";
$result = $conn->query($sql);
?>




 <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject ID</th>
                    <th>Subject</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
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
                echo "<tr><td colspan='8'>No classes found</td></tr>";
            }
            
                ?>
            </tbody>
        </table>
    </div>
</div>




<?php $conn->close(); ?>