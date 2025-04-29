<?php
// generate_subject_page.php

session_start();
if (!isset($_SESSION['parent_id'])) {
    $_SESSION['message'] = 'Please log in to add items to cart';
    header('Location: login.html');
    exit;
}

// 启动会话（支持消息提示，类似 product_detail.php）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 数据库连接（使用 PDO）
try {
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// 获取 subject_id
$subject_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($subject_id <= 0) {
    die("Can not find the subject ID");
}

// 查询主题详情和平均评分
try {
    $stmt = $pdo->prepare("
        SELECT s.*, 
               (SELECT AVG(comment_rating) FROM comments WHERE subject_id = s.subject_id) as avg_rating
        FROM subject s 
        WHERE s.subject_id = ?
    ");
    $stmt->execute([$subject_id]);
    $subject = $stmt->fetch();

    if (!$subject) {
        die("未找到主题");
    }
} catch (PDOException $e) {
    die("数据库查询失败: " . $e->getMessage());
}

// 设置默认值
$subject = array_merge([
    'subject_name' => 'Unknown Subject',
    'teacher_id' => 0,
    'subject_price' => '0.00',
    'avg_rating' => 0,
    'subject_description' => 'No description available',
    'subject_image' => 'img/default-subject.jpg',
    'year' => 'Year 1'
], $subject);

// 获取教师姓名
$teacher_name = "Unknown Teacher";
if ($subject['teacher_id']) {
    try {
        $stmt = $pdo->prepare("SELECT teacher_name FROM teacher WHERE teacher_id = ?");
        $stmt->execute([$subject['teacher_id']]);
        $teacher = $stmt->fetch();
        if ($teacher) {
            $teacher_name = $teacher['teacher_name'];
        }
    } catch (PDOException $e) {
        error_log("查询教师失败: " . $e->getMessage());
    }
}

// 显示会话消息
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($subject['subject_name']) ?> Class</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre" name="keywords">
    <meta content="The Seeds Learning Centre" name="description">
    <link href="img/the seeds.jpg" rel="icon" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        .icon-bar {
            display: flex;
            justify-content: flex-end;
            padding: 10px 0;
            background-color: #f8f9fa;
            border-bottom: 1px solid #caccce;
        }

        .icon-bar a {
            margin: 0 15px;
            color: #000000;
            font-size: 24px;
            transition: color 0.3s ease;
        }

        .icon-bar a:hover {
            color: #73cf67;
        }

        .breadcrumb-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .breadcrumb-container h2 {
            margin-bottom: 10px;
        }

        .breadcrumb-container .breadcrumb {
            display: flex;
            gap: 5px;
            font-size: 14px;
        }

        .breadcrumb-container .breadcrumb li {
            color: #555;
        }

        .breadcrumb-container .breadcrumb li a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb-container .breadcrumb li a:hover {
            text-decoration: underline;
        }

        .time-slot-container {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .time-slot {
            display: inline-block;
            width: 250px;
            height: 80px;
            line-height: 80px;
            text-align: center;
            border: 2px solid #ccc;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
        }

        .green {
            background-color: #73cf67;
            color: white;
        }

        .gray {
            background-color: #d3d3d3;
            color: #888;
            pointer-events: none;
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            height: auto;
            position: relative;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            text-align: left;
            font-size: 18px;
            overflow: auto;
        }

        .popup-content h4 {
            margin-bottom: 15px;
            text-align: center;
        }

        .popup-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .popup-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 20px;
        }

        .popup-label {
            font-weight: bold;
            width: 120px;
            text-align: left;
        }

        .popup-value {
            font-size: 20px;
            text-align: left;
            flex: 1;
        }

        .popup-content button {
            display: block;
            width: 100%;
            background-color: #73cf67;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            margin-top: 50px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            color: #888;
            cursor: pointer;
        }

        .subject-image {
            width: 350px;
            height: auto;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
        }

        .subject-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .class-details-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .class-details-container h2 {
            font-size: 64px;
            font-weight: bold;
        }

        .time-slot-container {
            margin-top: 20px;
        }

        .rating-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stars-container {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 30px;
        }

        .stars-container .star {
            width: 25px;
            height: 25px;
            background: gray;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            display: inline-block;
        }

        .stars-container .yellow {
            background: gold;
        }

        .stars-container .half {
            background: linear-gradient(to right, gold 50%, gray 50%);
        }

        .rating-text {
            font-size: 18px;
            color: #333;
        }

        .rating-number {
            font-weight: bold;
            font-size: 20px;
        }

        .container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            padding: 20px;
            width: 100%;
        }

        .subject-overview {
            width: 60%;
            font-size: 18px;
        }

        .subject-overview h2 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .subject-overview ul {
            padding-left: 20px;
            margin-top: 10px;
        }

        .subject-overview li {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .subject-overview p {
            margin-top: 20px;
            font-size: 18px;
        }

        .review-section {
            width: 35%;
            margin-top: 0;
        }

        .review-section h2 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .review-filter {
            margin-bottom: 20px;
        }

        .review-item {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            position: relative;
        }

        .review-text {
            max-width: 100%;
        }

        .subject-overview, .review-section {
            width: 100%;
            box-sizing: border-box;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            word-wrap: break-word;
        }

        .stars-container {
            display: flex;
            gap: 5px;
            font-size: 20px;
        }

        .star {
            width: 20px;
            height: 20px;
            background: gray;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            display: inline-block;
        }

        .star.filled {
            background: gold;
        }

        .review-text p {
            margin-top: 10px;
            font-size: 16px;
        }

        .review-filter select {
            font-size: 16px;
            padding: 5px;
            width: 150px;
        }

        .toast-message {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgb(171, 241, 187);
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            font-weight: bold;
            z-index: 1000;
            text-align: center;
            transition: opacity 0.5s ease-in-out;
        }

        .error {
            color: #dc3545;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="#" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="navbar-color"><i class="fa fa-book me-3"></i>The Seeds</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="member.html" class="nav-item nav-link">Home</a>
                <a href="subject.html" class="nav-item nav-link">Subject</a>
                <a href="about.html" class="nav-item nav-link">About us</a>
                <a href="contact.html" class="nav-item nav-link">Contact us</a>
                <a href="comment.html" class="nav-item nav-link">Comment</a>
            </div>
            <a href="login.html" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Log out<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>

    <!-- 图标栏 -->
    <div class="icon-bar">
        <a href="notification.html"><i class="fas fa-bell"></i></a>
        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>

    <!-- 面包屑导航 -->
    <div class="breadcrumb-container">
        <h2><?= htmlspecialchars($subject['subject_name']) ?></h2>
        <ul class="breadcrumb">
            <li><a href="member.html">Home</a></li>
            <li>></li>
            <li><a href="subject.html">Subject</a></li>
            <li>></li>
            <li><?= htmlspecialchars($subject['subject_name']) ?></li>
        </ul>
    </div>

    <!-- 主要内容 -->
    <div class="container my-5">
        <?php if ($message): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="class-details-container">
            <div class="subject-image">
                <img src="<?= htmlspecialchars($subject['subject_image']) ?>" alt="Subject Image" id="subjectImage">
            </div>
            <div class="class-details">
                <h2 id="subjectName"><?= htmlspecialchars($subject['subject_name']) ?></h2>
                <div class="rating-container">
                    <div class="stars-container">
                        <?php
                        $rating = $subject['avg_rating'] ?? 0;
                        $fullStars = floor($rating);
                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                        for ($i = 0; $i < $fullStars; $i++) {
                            echo '<span class="star yellow"></span>';
                        }
                        if ($hasHalfStar) {
                            echo '<span class="star half"></span>';
                        }
                        for ($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++) {
                            echo '<span class="star"></span>';
                        }
                        ?>
                    </div>
                    <div class="rating-text">
                        <span class="rating-number"><?= number_format($rating, 1) ?></span>
                    </div>
                </div>
                <div class="class-info">
                    <p id="teacher"><strong>Teacher: </strong> <?= htmlspecialchars($teacher_name) ?></p>
                    <p id="subjectPrice"><strong>Price: RM</strong> <?= htmlspecialchars($subject['subject_price']) ?></p>
                </div>
                <div class="time-slot-container">
                    <div class="time-slot green" id="partA">
                        <strong>Part A</strong> (January - June)
                    </div>
                    <div class="time-slot gray" id="partB">
                        <strong>Part B</strong> (July - December) (No open)
                    </div>
                </div>
            </div>
        </div>

        <!-- 弹窗 -->
        <div class="popup" id="popup">
            <div class="popup-content">
                <span class="close-btn" id="closePopup">×</span>
                <h4>Class Time Details</h4>
                <div class="popup-info">
                    <div class="popup-row">
                        <span class="popup-label">Month:</span>
                        <span class="popup-value" id="popupMonth"></span>
                    </div>
                    <div class="popup-row">
                        <span class="popup-label">Time:</span>
                        <span class="popup-value" id="popupTime"></span>
                    </div>
                    <div class="popup-row">
                        <span class="popup-label">Capacity:</span>
                        <span class="popup-value" id="popupchildren"></span>
                    </div>
                    <div class="popup-row">
                        <span class="popup-label">Children:</span>
                        <span class="popup-value">
                            <select id="childrenSelect">
                                <option value="">Choose</option>
                            </select>
                        </span>
                    </div>
                </div>
                <button id="addToCart">Add to Cart</button>
                <div id="successToast" class="toast-message" style="display: none;"></div>
            </div>
        </div>

        <!-- 课程概述 -->
        <div class="subject-overview">
            <h2>Subject Overview</h2>
            <p><?= nl2br(htmlspecialchars($subject['subject_description'])) ?></p>
        </div>

        <!-- 评论部分 -->
        <div class="review-section">
            <h2>Parent Reviews</h2>
            <div id="review-list">
                <!-- 评论将通过 JavaScript 动态加载 -->
            </div>
        </div>
    </div>

    <!-- 页脚 -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Quick Link</h4>
                    <a class="btn btn-link" href="about.html">About Us</a><br>
                    <a class="btn btn-link" href="contact.html">Contact Us</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Contact</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>5262A, Jalan Matahari, Bandar Indahpura, 81000 Kulai, Johor Darul Ta'zim</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+011 775 8990</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>TheSeeds@gmail.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Gallery</h4>
                    <div class="row g-2 pt-2">
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="img/course-1.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="img/course-2.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="img/course-3.jpg" alt="">
                        </div>
                        <div class="col-4">
                            <img class="img-fluid bg-light p-1" src="img/course-1.jpg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        © <a class="border-bottom" href="#">The Seeds Learning Centre</a>, All Right Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow.min.js"></script>
    <script src="lib/easing.min.js"></script>
    <script src="lib/waypoints.min.js"></script>
    <script src="lib/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script>
        let selectedClassInfo = null;

        document.addEventListener("DOMContentLoaded", function () {
            fetch('get_classes.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        console.error('Server error:', data.error);
                        return;
                    }

                    const courses = data.data;

                    courses.forEach(course => {
                        if (course.part_id === 1) course.part = "Part A";
                        else if (course.part_id === 2) course.part = "Part B";
                    });

                    const partA = courses.find(c => c.part === 'Part A');
                    const partB = courses.find(c => c.part === 'Part B');

                    if (partA) {
                        const partAElement = document.getElementById('partA');
                        if (partA.class_status.toLowerCase() === 'available') {
                            partAElement.classList.remove('gray', 'disabled');
                            partAElement.classList.add('green');
                        } else {
                            partAElement.classList.add('disabled');
                        }
                    }

                    if (partB) {
                        const partBElement = document.getElementById('partB');
                        if (partB.class_status.toLowerCase() === 'available') {
                            partBElement.classList.remove('gray', 'disabled');
                            partBElement.classList.add('green');
                        } else {
                            partBElement.classList.add('disabled');
                        }
                    }

                    document.getElementById('partA').addEventListener('click', function () {
                        if (!partA || partA.class_status.toLowerCase() !== 'available') return;
                        selectedClassInfo = {
                            class_id: partA.class_id || 'N/A',
                            capacity: partA.class_capacity,
                            enrolled: partA.class_enrolled
                        };
                        updatePopup(partA);
                    });

                    document.getElementById('partB').addEventListener('click', function () {
                        if (!partB || partB.class_status.toLowerCase() !== 'available') return;
                        selectedClassInfo = {
                            class_id: partB.class_id || 'N/A',
                            capacity: partB.class_capacity,
                            enrolled: partB.class_enrolled
                        };
                        updatePopup(partB);
                    });

                    document.getElementById('closePopup').addEventListener('click', function () {
                        document.getElementById('popup').style.display = 'none';
                    });

                    function updatePopup(course) {
                        const monthText = course.part_id === 1 ? "January - June" : "July - December";
                        document.getElementById('popupMonth').textContent = monthText;
                        document.getElementById('popupTime').textContent = course.class_time || 'Not set';
                        const enrolled = course.class_enrolled ?? 0;
                        const capacity = course.class_capacity ?? 0;
                        document.getElementById('popupchildren').textContent = `${enrolled} / ${capacity}`;
                        document.getElementById('popup').style.display = 'flex';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('加载课程数据失败，请刷新页面重试');
                });

            let addToCartButton = document.getElementById("addToCart");
            addToCartButton.disabled = true;

            let select = document.getElementById("childrenSelect");
            select.addEventListener("change", function() {
                if (select.value) {
                    addToCartButton.disabled = false;
                } else {
                    addToCartButton.disabled = true;
                }
            });

            
        // add to cart function 
        document.getElementById("addToCart").addEventListener("click", async function() {
    try {
        // 收集数据
        const cartItem = {
            subject_id: <?= $subject_id ?>,
            subject_name: document.getElementById("subjectName").innerText,
            price: parseFloat(
                document.getElementById("subjectPrice")
                    .innerText.replace('Price: RM', '')
                    .trim()
            ),
            child_name: document.getElementById("childrenSelect").value,
            image: document.getElementById("subjectImage").src,
            teacher: document.getElementById("teacher").innerText.replace('Teacher: ', ''),
            child_year: "<?= $subject['year'] ?>",
            class_id: selectedClassInfo?.class_id || null,
            capacity: selectedClassInfo?.capacity || 0,
            time: selectedClassInfo?.time || null
        };

        // 验证
        if (isNaN(cartItem.price)) {
            throw new Error("Invalid price value");
        }
        if (!cartItem.child_name || cartItem.child_name === "Choose") {
            throw new Error("Please select a child first");
        }

        // 发送请求
        const response = await fetch('save_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(cartItem)
        });

        // 处理非JSON响应
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            errorLog("Non-JSON response: " + text);
            throw new Error("Server returned an invalid response");
        }

        const data = await response.json();
        
        if (!response.ok || data.status !== "success") {
            throw new Error(data.message || "Failed to add to cart");
        }

        // 更新本地购物车
        updateLocalCart(data.id, cartItem);
        
        showToast(data.message || "Item added to cart successfully!");
        
    } catch (error) {
        console.error('Cart Error:', error);
        showToast(
            error.message.includes('JSON') || error.message.includes('Server')
                ? "Server error. Please try again later."
                : error.message,
            true
        );
    }
});

function updateLocalCart(itemId, item) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    
    // 添加服务器生成的ID
    if (itemId) {
        item.id = itemId;
    }
    
    // 检查是否已存在
    const exists = cart.some(i => 
        i.subject_id === item.subject_id && 
        i.child_name === item.child_name && 
        i.class_id === item.class_id
    );
    
    if (!exists) {
        cart.push(item);
        localStorage.setItem("cart", JSON.stringify(cart));
    }
}

function errorLog(message) {
    // 可以替换为实际的错误日志记录方式
    console.error("Error:", message);
}

            // Toast 通知
            function showToast(message, isError = false) {
                let toast = document.getElementById("successToast");
                toast.innerText = message;
                toast.style.backgroundColor = isError ? "#dc3545" : "#28a745";
                toast.style.display = "block";
                toast.style.opacity = "1";
                setTimeout(() => {
                    toast.style.opacity = "0";
                    setTimeout(() => { toast.style.display = "none"; }, 500);
                }, 3000);
            }

            // 设置评分星星
            const avgRating = <?= $subject['avg_rating'] ?? 0 ?>;
            setRating(avgRating);

            // 获取孩子数据
            fetch('get_child.php')
                .then(response => response.json())
                .then(data => {
                    let select = document.getElementById("childrenSelect");
                    select.innerHTML = '<option value="">Choose</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        const subjectYear = <?= intval(str_replace('Year ', '', $subject['year'])) ?>;
                        data.forEach(child => {
                            if (child.child_year === subjectYear) {
                                let option = document.createElement("option");
                                option.value = child.child_name;
                                option.textContent = child.child_name;
                                select.appendChild(option);
                            }
                        });
                        if (select.options.length === 1) {
                            let option = document.createElement("option");
                            option.textContent = "No matching children found";
                            option.disabled = true;
                            select.appendChild(option);
                        }
                    } else {
                        let option = document.createElement("option");
                        option.textContent = "No children found";
                        option.disabled = true;
                        select.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error fetching children:', error);
                });
        });

        function setRating(rating) {
            const stars = document.querySelectorAll('.stars-container .star');
            const fullStars = Math.floor(rating);
            const halfStar = (rating - fullStars) >= 0.5 ? 1 : 0;
            stars.forEach(star => {
                star.classList.remove('yellow', 'half');
            });
            for (let i = 0; i < fullStars; i++) {
                stars[i].classList.add('yellow');
            }
            if (halfStar) {
                stars[fullStars].classList.add('half');
            }
            const ratingNumber = document.querySelector('.rating-number');
            if (ratingNumber) {
                ratingNumber.textContent = rating.toFixed(1);
            }
        }

        // 获取评论
        function fetchReviews() {
            const subject_id = <?= $subject_id ?>;
            const url = `get_comments.php?subject_id=${subject_id}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    displayReviews(data.data || []);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showError('Error loading reviews: ' + error.message);
                });
        }

        function displayReviews(comments) {
            const reviewList = document.getElementById('review-list');
            reviewList.innerHTML = '<div class="review-items-container"></div>';
            const container = reviewList.querySelector('.review-items-container');

            if (comments.length > 0) {
                comments.forEach(comment => {
                    const reviewItem = document.createElement('div');
                    reviewItem.className = 'review-item';
                    const commentDate = new Date(comment.comment_created_at);
                    reviewItem.setAttribute('data-year', commentDate.getFullYear());

                    const reviewText = document.createElement('div');
                    reviewText.className = 'review-text';

                    const nameDate = document.createElement('p');
                    nameDate.innerHTML = `<strong>${comment.parent_name}</strong> - ${commentDate.toLocaleDateString()}`;

                    const starsContainer = document.createElement('div');
                    starsContainer.className = 'stars-container';
                    const rating = parseFloat(comment.rating) || 0;
                    starsContainer.innerHTML = `
                        ${'<span class="star yellow"></span>'.repeat(Math.floor(rating))}
                        ${rating % 1 >= 0.5 ? '<span class="star half"></span>' : ''}
                        ${'<span class="star"></span>'.repeat(5 - Math.ceil(rating))}
                        <span class="rating-number">${rating.toFixed(1)}</span>`;

                    const commentText = document.createElement('p');
                    commentText.textContent = comment.comment;

                    reviewText.appendChild(nameDate);
                    reviewText.appendChild(starsContainer);
                    reviewText.appendChild(commentText);
                    reviewItem.appendChild(reviewText);
                    container.appendChild(reviewItem);
                });
            } else {
                reviewList.innerHTML += '<p>No reviews yet</p>';
            }
        }

        function showError(message) {
            const reviewList = document.getElementById('review-list');
            reviewList.innerHTML = `<p class="error">${message}</p>`;
        }

        document.addEventListener("DOMContentLoaded", fetchReviews);
    </script>
</body>
</html>