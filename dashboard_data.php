<?php
// connect database profile/childreninfo.sql
$conn_children = new mysqli("localhost", "root", "", "profile");
if ($conn_children->connect_error) {
    die("Connection failed: " . $conn_children->connect_error);
}

// check children data
$sql_children = "SELECT COUNT(*) AS total_children FROM childreninfo";
$result_children = $conn_children->query($sql_children);
$total_children = ($result_children->num_rows > 0) ? $result_children->fetch_assoc()['total_children'] : 0;

// close connect
$conn_children->close();

// connect second database userinformation/user.sql
$conn_parents = new mysqli("localhost", "root", "", "user_information");
if ($conn_parents->connect_error) {
    die("Connection failed: " . $conn_parents->connect_error);
}

// check parents data
$sql_parents = "SELECT COUNT(*) AS total_parents FROM users";
$result_parents = $conn_parents->query($sql_parents);
$total_parents = ($result_parents->num_rows > 0) ? $result_parents->fetch_assoc()['total_parents'] : 0;

// close connect second database
$conn_parents->close();

// connect admin database
$conn_admin = new mysqli("localhost", "root", "", "admin");

if ($conn_admin->connect_error) {
    die("Connection failed: " . $conn_admin->connect_error);
}

$sql_subject = "SELECT COUNT(*) AS total_subjects FROM admin_subject";
$result_subject = $conn_admin->query($sql_subject);

if ($result_subject->num_rows > 0) {
    $row = $result_subject->fetch_assoc();
    $total_subjects = $row['total_subjects'];
} else {
    $total_subjects = 0;
}

$conn_admin_panel = new mysqli("localhost", "root", "", "admin_panel");
if ($conn_admin_panel->connect_error) {
    die("Connection failed: " . $conn_admin_panel->connect_error);
}

$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn_admin_panel->query($sql_users);
$total_users = ($result_users->num_rows > 0) ? $result_users->fetch_assoc()['total_users'] : 0;

$conn_admin_panel->close();


echo json_encode([
    'total_children' => $total_children,
    'total_parents' => $total_parents,
    'total_subjects'=> $total_subjects,
    'total_users'=> $total_users,
]);
?>
