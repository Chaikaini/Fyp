<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

echo "<h2>Welcome, " . $_SESSION["admin_name"] . " (".$_SESSION["admin_role"].")</h2>";
echo '<p><a href="logout.php">Log out</a></p>';
