<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tuition_centre";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$sql = "SELECT name, image, teacher, price, rating, page, year FROM subjects ORDER BY year";
$result = $conn->query($sql);

$subjectsData = ["year1" => [], "year2" => [],"year3" => []];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subject = [
            "name" => $row["name"],
            "image" => $row["image"],
            "teacher" => $row["teacher"],
            "price" => $row["price"],
            "rating" => floatval($row["rating"]),
            "page" => $row["page"]
        ];
        
        if ($row["year"] == 1) {
            $subjectsData["year1"][] = $subject;
        } elseif ($row["year"] == 2) {
            $subjectsData["year2"][] = $subject;
        }
        elseif ($row["year"] == 3) {
            $subjectsData["year3"][] = $subject;
        }
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($subjectsData);
?>
