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
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre, Year 1 class " name="keywords">
    <meta content="The Seeds Learning Centre | Year 1 Class " name="description">

    <!-- Favicon -->
    <link href="img/the seeds.jpg" rel="icon" type="image/png">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
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
    <script>
document.addEventListener("DOMContentLoaded", function () {
    fetch('get_classes.php')
        .then(response => response.json())
        .then(data => {
            const partA = data.find(c => c.part === 'Part A');
            const partB = data.find(c => c.part === 'Part B');

            if (partA && partA.status === 'available') {
                document.getElementById('partA').classList.remove('gray');
                document.getElementById('partA').classList.add('green');
            }

            if (partB && partB.status === 'unavailable') {
                document.getElementById('partB').classList.add('disabled');
            } else if (partB) {
                document.getElementById('partB').classList.remove('disabled');
            }

            document.getElementById('partA').addEventListener('click', function () {
                if (!partA) return;
                document.getElementById('popupMonth').innerText = partA.month;
                document.getElementById('popupTime').innerText = partA.time;
                const [enrolled, total] = partA.capacity.split('/');
                document.getElementById('popupchildren').innerText = `${enrolled} / ${total}`;
                document.getElementById('popup').style.display = 'flex';
            });

            document.getElementById('partB').addEventListener('click', function () {
                if (!partB || partB.status !== 'available') return;
                document.getElementById('popupMonth').innerText = partB.month;
                document.getElementById('popupTime').innerText = partB.time;
                const [enrolled, total] = partB.capacity.split('/');
                document.getElementById('popupchildren').innerText = `${enrolled} / ${total}`;
                document.getElementById('popup').style.display = 'flex';
            });

            document.getElementById('closePopup').addEventListener('click', function () {
                document.getElementById('popup').style.display = 'none';
            });
        })
        .catch(error => console.error('Error:', error));
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
        time: time
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
            const url = 'y1_eng_comments.php';  // 获取固定条件下的评论
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
                            name.innerHTML = `<strong>${comment.subject} - ${comment.year_created}</strong>`;
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