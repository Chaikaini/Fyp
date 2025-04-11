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

$sql = "SELECT * FROM admin_class";
$result = $conn->query($sql);
?>

<div class="card-body">
    <style>
        .status-badge {
            display: inline-block;
            width: 100px;
            text-align: center;
            font-size: 14px;
            font-weight: normal; 
            padding: 5px 0;
        }
        .bg-success {
            background-color: #28a745 !important; 
            color: white;
        }
        .bg-secondary {
            background-color: #6c757d !important;
            color: white;
        }
    </style>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Subject ID</th>
                <th>Class ID</th>
                <th>Year</th>
                <th>Part</th>
                <th>Month</th>
                <th>Time</th>
                <th>Teacher</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
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
                    echo "<td><span id='capacity-" . $row["class_id"] . "'>" . $row["enrolled"] . "/" . $row["capacity"] . "</span></td>";
                    echo "<td>";
                    if ($row["status"] === "available") {
                        echo "<span class='badge status-badge bg-success'>Available</span>";
                    } else {
                        echo "<span class='badge status-badge bg-secondary'>Unavailable</span>";
                    }
                    echo "</td>";
                    echo "<td>
                        <i class='pointer-cursor fas fa-edit text-warning edit-btn' onclick='updateCapacityAndShowStudents(\"" . $row["class_id"] . "\")'></i>
                        <i class='pointer-cursor fas fa-trash-alt text-danger delete-btn' data-classid='" . $row['class_id'] . "'></i>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No classes found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php $conn->close(); ?>