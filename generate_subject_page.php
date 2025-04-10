<?php
include('db_connect.php'); // 您的数据库连接文件

// 1. 获取传入的科目ID
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
if (!$subject_id) die("Error: Missing subject_id");

// 2. 从数据库读取科目数据
$query = "SELECT * FROM admin_subject WHERE subject_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();

if (!$subject) die("Error: Subject not found");

// 3. 动态生成 HTML（保留PHP功能）
ob_start(); // 开启输出缓冲
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($subject['subject']) ?> Class</title>
    <!-- 其他 head 内容保持不变 -->
</head>
<body>
    <!-- 导航栏等静态部分保持不变 -->

    <!-- 动态替换的部分 -->
    <div class="breadcrumb-container">
        <h2><?= htmlspecialchars($subject['subject']) ?></h2>
        <!-- 面包屑导航 -->
    </div>

    <div class="container">
        <!-- 科目图片和基本信息 -->
        <div class="subject-image">
            <img src="<?= htmlspecialchars($subject['image']) ?>" alt="Subject Image">
        </div>

        <!-- 从数据库读取的 Subject Overview -->
        <div class="subject-overview">
            <h2>Subject Overview</h2>
            <p><?= nl2br(htmlspecialchars($subject['description'])) ?></p>
        </div>

        <!-- 保留原有的 PHP 动态功能（如评论） -->
        <?php include('fetch_reviews.php'); ?>
    </div>

    <!-- 其他部分保持不变 -->
</body>
</html>
<?php
$html = ob_get_clean(); // 获取生成的HTML

// 4. 保存为静态文件
$filename = str_replace(' ', '_', $subject['subject']) . '_class.html';
file_put_contents($filename, $html);

// 5. 更新数据库标记
$update = "UPDATE admin_subject SET page_generated = 1, page_path = ? WHERE subject_ID = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("si", $filename, $subject_id);
$stmt->execute();

echo "Generated: " . $filename;
?>