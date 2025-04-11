<?php
header('Content-Type: application/json');

// 数据库连接
$servername = "localhost";
$username = "root";
$password = "";
$order_dbname = "order";  // 订单数据库

$conn = new mysqli($servername, $username, $password, $order_dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['class_id'])) {
    $classId = $_POST['class_id'];

    // 获取该班级的所有学生（根据 class_id）
    $sql = "SELECT child_name, child_gender, child_email FROM orders WHERE class_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $classId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'name' => $row['child_name'],
                'gender' => $row['child_gender'],
                'email' => $row['child_email']
            ];
        }
        echo json_encode(["success" => true, "students" => $students]);
    } else {
        echo json_encode(["success" => false, "message" => "No students found for this class."]);
    }

    $stmt->close();
}

$conn->close();
?>
