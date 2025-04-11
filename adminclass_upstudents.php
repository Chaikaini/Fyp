<?php
// 数据库连接配置
$servername = "localhost";
$username = "root";
$password = "";
$order_dbname = "order";  // 订单数据库
$children_dbname = "childreninfo";  // 孩子信息数据库

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $order_dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 查询所有班级
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $studentName = $row["student_name"];
        $studentEmail = $row["email"];
        $classId = $row["class_id"];

        // 先查询 childreninfo 数据库获取孩子信息
        $conn->select_db($children_dbname);  // 切换到 childreninfo 数据库
        $sqlChild = "SELECT name, gender, email FROM children WHERE name = ? AND email = ?";
        $stmtChild = $conn->prepare($sqlChild);
        $stmtChild->bind_param("ss", $studentName, $studentEmail);
        $stmtChild->execute();
        $resultChild = $stmtChild->get_result();

        if ($resultChild->num_rows > 0) {
            // 孩子信息存在，获取信息
            $childInfo = $resultChild->fetch_assoc();
            $childName = $childInfo['name'];
            $childGender = $childInfo['gender'];
            $childEmail = $childInfo['email'];

            // 更新订单数据库中的对应 class_id 的学生信息
            $conn->select_db($order_dbname);  // 切换回 order 数据库
            $updateSql = "UPDATE orders SET child_name = ?, child_gender = ?, child_email = ? WHERE class_id = ? AND student_name = ? AND email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ssssss", $childName, $childGender, $childEmail, $classId, $studentName, $studentEmail);

            if ($updateStmt->execute()) {
                echo "Updated student information for class_id: " . $classId . "<br>";
            } else {
                echo "Failed to update student information for class_id: " . $classId . "<br>";
            }

            $updateStmt->close();
        } else {
            echo "No matching student found for student_name: $studentName and email: $studentEmail.<br>";
        }

        $stmtChild->close();
    }
} else {
    echo "No orders found.<br>";
}

$conn->close();
?>
