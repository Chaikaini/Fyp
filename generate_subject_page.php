<?php
// 开启错误报告（调试完成后可移除）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. 引入数据库连接文件
include('db1.php');  // 确保数据库连接成功

// 2. 获取科目ID（支持 CLI 和 HTTP 请求）
$subject_id = 0;
if (php_sapi_name() === 'cli') {
    // 从命令行参数中获取科目ID
    $subject_id = isset($argv[1]) ? (int)$argv[1] : 0;
} else {
    // 从 URL 参数中获取科目ID
    $subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
}

if (!$subject_id) {
    die("Error: 必须提供科目ID");
}

// 3. 从数据库读取科目信息
$query = "SELECT * FROM admin_subject WHERE subject_ID = ?";
$stmt = $conn->prepare($query);  // 使用 $conn 执行查询
$stmt->bind_param("i", $subject_id);  // 绑定参数
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();  // 获取查询结果

if (!$subject) {
    die("Error: 找不到ID为 $subject_id 的科目");
}

// 4. 动态生成 HTML 内容
ob_start();  // 启动输出缓冲区

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($subject['subject']) ?> Class</title>
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
    flex-direction: column; /* 让内容垂直排列 */
    align-items: flex-start; /* 左对齐 */
}

.breadcrumb-container h2 {
    margin-bottom: 10px; /* 增加与面包屑导航的间距 */
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
        z-index: 9999; /* Ensure the popup is on top of other content */
    }
    
    .popup-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 500px;
        height: auto; /* Allow dynamic height */
        position: relative;
        z-index: 10000; /* Ensure content is visible inside popup */
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        text-align: left; /* Align content to the left */
        font-size: 18px; /* Adjust font size to fit better */
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
        align-items: baseline; /* Align content to baseline for better alignment */
        font-size: 20px;
    }
    
    .popup-label {
        font-weight: bold;
        width: 120px;
        text-align: left; /* Left-align labels */
    }
    
    .popup-value {
        font-size: 20px;
        text-align: left; /* Ensure values are aligned left */
        flex: 1; /* Make sure the value stretches if necessary */
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
            font-size: 64px; /* Increased font size for h2 */
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

 </style>
</head>
<body>
    <!-- 导航栏等静态部分保持不变 -->
         <!-- Navbar Start -->
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
    <!-- Navbar End -->

    <div class="icon-bar">
        <a href="notification.html"><i class="fas fa-bell"></i></a>
        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>

    <!-- 动态替换的部分 -->

    <!-- 面包屑导航 -->
    <div class="breadcrumb-container">
        <h2><?= htmlspecialchars($subject['subject']) ?></h2>
        <ul class="breadcrumb">
            <li><a href="member.html">Home</a></li>
            <li>&gt;</li>
            <li><a href="subject.html">Subject</a></li>
            <li>&gt;</li>
            <li><?= htmlspecialchars($subject['subject']) ?></li>
        </ul>
    </div>

 <!-- 主要内容 -->
 <div class="container my-5">
        <div class="class-details-container">
            <div class="subject-image">
                <img src="<?= htmlspecialchars($subject['image']) ?>" alt="Subject Image" id="subjectImage">
            </div>
            <div class="class-details">
                <h2 id="subjectName"><?= htmlspecialchars($subject['subject']) ?></h2>
                
                <div class="rating-container">
                    <div class="stars-container">
                        <!-- 动态生成评分星星 -->
                        <?php
                        $rating = $subject['rating'] ?? 0;
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
                    <p id="teacher"><strong>Teacher: </strong> <?= htmlspecialchars($subject['teacher']) ?></p>
                    <p id="subjectPrice"><strong>Price: RM</strong> <?= htmlspecialchars($subject['price']) ?></p>
                </div>

                <!-- 时间选择部分 -->
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

        <div class="popup" id="popup">
        <div class="popup-content">
            <span class="close-btn" id="closePopup">&times;</span>
            <h4>Class Time Details</h4>
            
            <!-- Information section with flex layout -->
            <div class="popup-info">
                <div class="popup-row">
                    <span class="popup-label">Month:</span>
                    <span class="popup-value" id="popupMonth"></span>
                </div>
                <div class="popup-row">
                    <span class="popup-label" id="classTime">Time:</span>
                    <span class="popup-value" id="popupTime"></span>
                </div>
                <div class="popup-row">
                    <span class="popup-label" id="currentCapacity">Capacity:</span>
                    <span class="popup-value" id="popupchildren"></span>
                </div>
                <div class="popup-row">
                    <span class="popup-label" id="selectedChildren">Children:</span>
                    <span class="popup-value">
                        <select id="childrenSelect">
                            <option value="">Choose </option>
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
            <p><?= nl2br(htmlspecialchars($subject['description'])) ?></p>
        </div>

        <!-- 评论部分（直接嵌入，不使用fetch_reviews.php） -->
        <div class="review-section">
            <h2>Parent Reviews</h2>
            <div id="review-list">
                <!-- 评论将通过JavaScript动态加载 -->
                <div class="review-filter">
                <label for="yearFilter">Filter by Year: </label>
                <select id="yearFilter">
                    <option value="All">All</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </select>
            </div>
            </div>
        </div>
    </div>

    <!-- 页脚 -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn">
        <!-- 页脚内容保持不变 -->
    </div>

    <!-- 其他部分保持不变 -->
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

// 🔧 修复点：转换 part_id 为 part 字符串
courses.forEach(course => {
    if (course.part_id === 1) course.part = "Part A";
    else if (course.part_id === 2) course.part = "Part B";
});

const partA = courses.find(c => c.part === 'Part A');
const partB = courses.find(c => c.part === 'Part B');

            // 处理 Part A
            if (partA) {
                const partAElement = document.getElementById('partA');
                if (partA.status === 'available') {
                    partAElement.classList.remove('gray');
                    partAElement.classList.add('green');
                } else {
                    partAElement.classList.add('disabled');
                }
            }

            // 处理 Part B
            if (partB) {
                const partBElement = document.getElementById('partB');
                if (partB.status === 'available') {
                    partBElement.classList.remove('gray', 'disabled');
                    partBElement.classList.add('green');
                } else {
                    partBElement.classList.add('disabled');
                }
            }

            // 点击 Part A
            document.getElementById('partA').addEventListener('click', function () {
                if (!partA || partA.status !== 'available') return;
                
                selectedClassInfo = {
                    class_id: partA.class_id || 'N/A',  // 处理空 class_id
                    capacity: partA.capacity
                };
                
                updatePopup(partA);
            });

            // 点击 Part B
            document.getElementById('partB').addEventListener('click', function () {
                if (!partB || partB.status !== 'available') return;
                
                selectedClassInfo = {
                    class_id: partB.class_id || 'N/A',  // 处理空 class_id
                    capacity: partB.capacity
                };
                
                updatePopup(partB);
            });

            // 关闭弹窗
            document.getElementById('closePopup').addEventListener('click', function () {
                document.getElementById('popup').style.display = 'none';
            });

            // 更新弹窗内容的公共函数
            function updatePopup(course) {
                document.getElementById('popupMonth').textContent = course.month;
                document.getElementById('popupTime').textContent = course.time;
                
                // 处理 capacity 显示
                if (course.capacity && course.capacity.includes('/')) {
                    const [enrolled, total] = course.capacity.split('/');
                    document.getElementById('popupchildren').textContent = `${enrolled} / ${total}`;
                } else {
                    document.getElementById('popupchildren').textContent = '0 / 0';
                }
                
                document.getElementById('popup').style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('加载课程数据失败，请刷新页面重试');
        });
});



        document.addEventListener("DOMContentLoaded", function() {
    let addToCartButton = document.getElementById("addToCart");
    addToCartButton.disabled = true; // 默认禁用

    let select = document.getElementById("childrenSelect");

    select.addEventListener("change", function() {
        if (select.value) {
            addToCartButton.disabled = false; // 选择了孩子，启用按钮
        } else {
            addToCartButton.disabled = true; // 没选孩子，禁用按钮
        }
    });
});


       // 购物车功能

       document.getElementById("addToCart").addEventListener("click", function() {
    let selectedChild = document.getElementById("childrenSelect").innerText.replace('Choose', '').trim();
    let subjectName = document.getElementById("subjectName").innerText;
    let subjectPrice = document.getElementById("subjectPrice").innerText.replace('Price: RM', '').trim();
    let teacher = document.getElementById("teacher").innerText.replace('Teacher: ', '').trim();
    let time = document.getElementById("time").innerText.replace('Time: ', '').trim();
    let price = parseFloat(subjectPrice);

    let subjectImage = document.getElementById("subjectImage").src; // 获取img标签的src属性

    if (isNaN(price)) {
        alert("Invalid price value");
        return;
    }

    // 创建一个购物车商品对象
    const cartItem = {
    subject: subjectName,
    price: price,
    child: selectedChild,
    image: subjectImage,
    teacher: teacher,
    time: time,
    class_id: selectedClassInfo?.class_id || null,
    capacity: selectedClassInfo?.capacity || null
};


    // 获取购物车中的现有商品
    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    // 检查当前商品是否已在购物车中
    let itemExists = cart.some(item => item.subject === cartItem.subject && item.child === cartItem.child && item.teacher === cartItem.teacher && item.time === cartItem.time);

    if (itemExists) {
        showToast("This item is already in your cart!", true); // 如果已存在，显示提示消息
    } else {
        // 如果不存在，发送 POST 请求到 save_cart.php
        fetch('save_cart.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json'  // 确保使用正确的 Content-Type
            },
            body: JSON.stringify({ cart: [cartItem] })  // 发送 JSON 格式数据
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                // 👉 如果服务器返回了数据库中的 id，就添加到 cartItem 上
                if (data.id) {
                    cartItem.id = data.id;
                }

                // ✅ 将商品添加到购物车并更新 localStorage
                cart.push(cartItem);
                localStorage.setItem("cart", JSON.stringify(cart)); // 更新购物车

                showToast("Item added to cart!");
            } else {
                showToast(data.message, true); // 显示错误消息
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast("An error occurred. Please try again.", true);  // 请求错误提示
        });
    }
});

// Toast Notification Function
function showToast(message, isError = false) {
    let toast = document.getElementById("successToast");
    toast.innerText = message;
    toast.style.backgroundColor = isError ? "#dc3545" : "#28a745"; // 错误时为红色，成功时为绿色
    toast.style.display = "block";
    toast.style.opacity = "1";

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => { toast.style.display = "none"; }, 500);
    }, 3000);
}








        
document.addEventListener("DOMContentLoaded", function() {
            // 通过 Fetch 请求获取孩子的数据
            fetch('get_child.php')  // 获取孩子数据
                .then(response => response.json())
                .then(data => {
                    let select = document.getElementById("childrenSelect");
                    select.innerHTML = '<option value="">Choose </option>'; // 清空现有选项

                    // 如果返回的数组为空，显示一个选项表示没有孩子
                    if (Array.isArray(data) && data.length > 0) {
                        // 遍历返回的孩子数据，筛选出 Year 1 的孩子
                        data.forEach(child => {
                            if (child.year === "Year 1") { // 只处理 Year 1 的孩子
                                let option = document.createElement("option");
                                option.value = child.name;  // 孩子的名字作为值
                                option.textContent = child.name;  // 只显示名字
                                select.appendChild(option);
                            }
                        });
                    } else {
                        // 如果没有孩子数据，显示一个"无孩子"选项
                        let option = document.createElement("option");
                        option.textContent = "No children found";
                        option.disabled = true; // 禁用无子项的选项
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
    const emptyStars = stars.length - fullStars - halfStar;

    for (let i = 0; i < fullStars; i++) {
        stars[i].classList.add('yellow');
    }

    if (halfStar) {
        stars[fullStars].classList.add('half');
    }

    for (let i = fullStars + halfStar; i < stars.length; i++) {
        stars[i].classList.remove('yellow', 'half');
    }
}

setRating(4.6);

document.getElementById("yearFilter").addEventListener("change", function() {
    var selectedYear = this.value;
    var reviews = document.querySelectorAll(".review-item");
    reviews.forEach(function(review) {
        var reviewYear = review.getAttribute("data-year");
        if (selectedYear === "All" || selectedYear === reviewYear) {
            review.style.display = "flex";
        } else {
            review.style.display = "none";
        }
    });
});


    </script>
    <!-- Footer Start -->
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
            &copy; <a class="border-bottom" href="#">The Seeds Learning Centre</a>, All Right Reserved.
          </div>
          <div class="col-md-6 text-center text-md-end">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer End -->


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
        // 获取并显示评论的函数
        function fetchReviews() {
            const url = 'get_comments.php';  // 获取固定条件下的评论
            fetch(url)
                .then(response => response.json())
                .then(comments => {
                    const reviewList = document.getElementById('review-list');
                    reviewList.innerHTML = ''; // 清空现有的评论
    
                    if (comments.length > 0) {
                        comments.forEach(comment => {
                            const reviewItem = document.createElement('div');
                            reviewItem.classList.add('review-item');
                            reviewItem.setAttribute('data-year', comment.year_created);
    
                            const reviewText = document.createElement('div');
                            reviewText.classList.add('review-text');
    
                            const name = document.createElement('p');
                            name.innerHTML = `<strong>${comment.comment_created_at.split(' ')[0]}</strong>`;
                            reviewText.appendChild(name);
    
                            // 生成评分星星
                            const starsContainer = document.createElement('div');
                            starsContainer.classList.add('stars-container');
                            const rating = comment.rating;
                            let fullStars = Math.floor(rating);
                            let halfStar = (rating - fullStars) >= 0.5 ? 1 : 0;
                            let emptyStars = 5 - fullStars - halfStar;
    
                            for (let i = 0; i < fullStars; i++) {
                                starsContainer.innerHTML += '<span class="star filled"></span>';
                            }
    
                            if (halfStar) {
                                starsContainer.innerHTML += '<span class="star half"></span>';
                            }
    
                            for (let i = 0; i < emptyStars; i++) {
                                starsContainer.innerHTML += '<span class="star"></span>';
                            }
    
                            reviewText.appendChild(starsContainer);
    
                            const commentText = document.createElement('p');
                            commentText.textContent = comment.comment;
                            reviewText.appendChild(commentText);
    
                            reviewItem.appendChild(reviewText);
                            reviewList.appendChild(reviewItem);
                        });
                    } else {
                        reviewList.innerHTML = 'No reviews found.';
                    }
                })
                .catch(error => {
                    console.error('Error fetching reviews:', error);
                });
        }
    
        // 页面加载时显示评论
        window.onload = function() {
            fetchReviews(); // 默认加载所有评论（即英文一年级的评论）
        };
    </script>
</body>
</html>
<?php

// 5. 保存为静态 HTML 文件
$filename = str_replace(' ', '_', $subject['subject']) . '_class.html';  // 文件名
$html_content = ob_get_clean();  // 获取输出缓冲区中的内容

if (file_put_contents($filename, $html_content)) {
    // 页面生成成功
    echo "Page can run : <a href='$filename'>$filename</a>";

    // 6. 更新数据库标记，记录页面生成路径
    $update = "UPDATE admin_subject SET page_generated = 1, page_path = ? WHERE subject_ID = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("si", $filename, $subject_id);
    $stmt->execute();  // 执行更新
} else {
    // 页面生成失败
    die("Error: Can not write page");
}
?>