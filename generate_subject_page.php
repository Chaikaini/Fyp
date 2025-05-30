<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    $_SESSION['message'] = 'Please log in to add items to cart';
    header('Location: login.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=the seeds", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

$subject_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($subject_id)) {
    die("Cannot find the subject ID");
}

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
        die("Subject not found");
    }
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

$subject = array_merge([
    'subject_name' => 'Unknown Subject',
    'subject_price' => '0.00',
    'avg_rating' => 0,
    'subject_description' => 'No description available',
    'subject_image' => 'img/default-subject.jpg',
    'year' => 'Year 1'
], $subject);

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($subject['subject_name']) ?> Class</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre, Class" name="keywords">
    <meta content="The Seeds Learning Centre | Class" name="description">
    <!-- 禁用缓存 -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link href="img/the seeds.jpg" rel="icon" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
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
            cursor: pointer;
        }
        .gray {
            background-color: #d3d3d3;
            color: #888;
            pointer-events: none;
            cursor: not-allowed;
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
            position: relative;
            display: flex;
            flex-direction: column;
            font-size: 18px;
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
        .review-item {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
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
        .toast-message {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #28a745 !important;
            color: white !important;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            font-weight: bold;
            z-index: 1000 !important;
            text-align: center;
            transition: opacity 0.5s ease-in-out;
            opacity: 1 !important;
        }
        .toast-message.error {
            background-color: #dc3545 !important;
        }
        .error {
            color: #dc3545;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
            position: fixed;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            font-weight: bold;
            display: none;
        }
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 10px;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            display: none;
            z-index: 1000;
        }
        .notification-icon {
            position: relative;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="member.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="navbar-color"><i class="fa fa-book me-3"></i>The Seeds</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <!-- Navigation links will be dynamically inserted here -->
                 <a href="member.html" class="nav-item nav-link">Home</a>
                <a href="subject.html" class="nav-item nav-link">Subject</a>
                <a href="about.html" class="nav-item nav-link">About us</a>
                <a href="contact.html" class="nav-item nav-link">Contact us</a>
                <a href="comment.html" class="nav-item nav-link">Comment</a>
            </div>
            <a href="logout.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Log out<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <div class="icon-bar">
        <a href="notification.php" class="notification-icon">
            <i class="fas fa-bell"></i>
            <span class="notification-badge" style="display: none;"></span>
        </a>
        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>
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
    <div class="container my-5">
        <?php if ($message): ?>
        <div class="error" id="errorBox"><?= htmlspecialchars($message) ?></div>
        <?php else: ?>
        <div class="error" id="errorBox" style="display: none;"></div>
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
                    <p id="subjectPrice"><strong>Price: RM</strong> <?= htmlspecialchars($subject['subject_price']) ?></p>
                </div>
                <div class="time-slot-container">
                    <div class="time-slot" id="partA"><strong>Part A</strong> (January - June)</div>
                    <div class="time-slot" id="partB"><strong>Part B</strong> (July - December)</div>
                </div>
            </div>
        </div>
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
                        <span class="popup-label">Teacher:</span>
                        <span class="popup-value" id="popupTeacher"></span>
                    </div>
                    <div class="popup-row">
                        <span class="popup-label">Venue:</span>
                        <span class="popup-value" id="popupVenue"></span>
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
                        <span class="popup-value" id="childrenContainer">
                            <select id="childrenSelect">
                                <option value="">Choose</option>
                            </select>
                        </span>
                    </div>
                </div>
                <button id="addToCart">Add to Cart</button>
            </div>
        </div>
        <div class="subject-overview">
            <h2>Subject Overview</h2>
            <p><?= nl2br(htmlspecialchars($subject['subject_description'])) ?></p>
        </div>
        <div class="review-section">
            <h2>Parent Reviews</h2>
            <div id="review-list"></div>
        </div>
    </div>
     <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <!-- 品牌标语 -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">The Seeds Learning Tuition Centre</h4>
                    <p>Every child is a different kind of flower. We nurture their growth.</p>
                </div>
                <!-- Quick Link -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">Quick Link</h4>
                    <a class="btn btn-link" href="about.html">About Us</a><br>
                </div>
                <!-- Contact -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">Contact</h4>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>TheSeeds@gmail.com</p>
                    <!-- 社交媒体（可替换为真实链接） -->
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="https://www.facebook.com/people/The-Seeds-Learning-Centre/100063525220441/#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="https://www.instagram.com/theseeds_kulai?igsh=dGt4YWpiOWJiem44"><i class="fab fa-instagram"></i></a>
                        <a class="btn btn-outline-light btn-social" href="https://wa.me/60117758990"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        © <a class="border-bottom" href="#">The Seeds Learning Tuition Centre</a>, All Right Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->
     
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow.min.js"></script>
    <script src="lib/easing.min.js"></script>
    <script src="lib/waypoints.min.js"></script>
    <script src="lib/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    
    <script>
let selectedClassInfo = null;
let hasChildren = false; // Flag to track if children are available

function showToast(message, isError = false) {
    console.log('showToast called with message:', message, 'at', new Date().toLocaleString('en-US', { timeZone: 'Asia/Kuala_Lumpur' }));
    document.querySelectorAll('.toast-message').forEach(toast => toast.remove());
    let toast = document.createElement('div');
    toast.className = `toast-message ${isError ? 'error' : ''}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    console.log('Toast element created and appended:', toast);
    toast.style.opacity = '1';
    setTimeout(() => {
        console.log('Fading out toast:', message);
        toast.style.opacity = '0';
        setTimeout(() => {
            console.log('Removing toast from DOM:', message);
            toast.remove();
        }, 500);
    }, 3000);
}

function showErrorBox(message) {
    const errorBox = document.getElementById('errorBox');
    if (errorBox) {
        console.log('showErrorBox called with message:', message, 'at', new Date().toLocaleString('en-US', { timeZone: 'Asia/Kuala_Lumpur' }));
        errorBox.textContent = message;
        errorBox.style.display = message ? 'block' : 'none';
        if (message) {
            setTimeout(() => {
                errorBox.style.display = 'none';
            }, 3000);
        }
    }
}

// Check login status and load content
async function checkLoginStatus() {
    try {
        const response = await fetch('check_login.php', {
            credentials: 'include',
            cache: 'no-store'
        });
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        console.log('Login check response:', data);

        if (!data.isLoggedIn) {
            window.location.href = 'login.html';
        } else {
            loadNavigation();
        }
    } catch (error) {
        console.error('Error fetching login status:', error);
        showToast('Failed to check login status. Please try again.', true);
        window.location.href = 'login.html';
    }
}

// Load navigation dynamically
async function loadNavigation() {
    try {
        const response = await fetch('check_login.php', { credentials: 'include', cache: 'no-store' });
        if (!response.ok) throw new Error('Failed to fetch login status');
        const data = await response.json();

        const navLinks = document.getElementById('navLinks');
        const authButton = document.getElementById('authButton');

        if (data.isLoggedIn) {
            navLinks.innerHTML = `
                <a href="member.html" class="nav-item nav-link">Home</a>
                <a href="subject.html" class="nav-item nav-link active">Subject</a>
                <a href="about.html" class="nav-item nav-link">About us</a>
                <a href="contact.html" class="nav-item nav-link">Contact us</a>
                <a href="comment.html" class="nav-item nav-link">Comment</a>
            `;
            authButton.textContent = 'Log out';
            authButton.href = 'logout.php';
        } else {
            navLinks.innerHTML = `
                <a href="888.html" class="nav-item nav-link">Home</a>
                <a href="about.html" class="nav-item nav-link">About us</a>
                <a href="contact.html" class="nav-item nav-link">Contact us</a>
            `;
            authButton.textContent = 'Log in';
            authButton.href = 'login.html';
        }
    } catch (error) {
        console.error('Error fetching login status:', error);
        const navLinks = document.getElementById('navLinks');
        const authButton = document.getElementById('authButton');
        navLinks.innerHTML = `
            <a href="888.html" class="nav-item nav-link">Home</a>
            <a href="about.html" class="nav-item nav-link">About us</a>
            <a href="contact.html" class="nav-item nav-link">Contact us</a>
        `;
        authButton.textContent = 'Log in';
        authButton.href = 'login.html';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Check login status
    checkLoginStatus();

    // Existing fetch for notifications
    fetch('get_notification.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.notifications && data.notifications.some(notif => notif.read_status === 'unread')) {
                document.querySelector('.notification-badge').style.display = 'block';
            }
        })
        .catch(error => console.error('Fetch error:', error));

    // Existing fetch for classes
    fetch('get_classes.php?subject_id=<?= htmlspecialchars($subject_id) ?>')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Received class data:', data);
            if (!data.success) {
                showToast('Failed to load class data: ' + data.error, true);
                return;
            }

            const currentSubjectId = "<?= htmlspecialchars($subject_id) ?>";
            const partA = (data.data.partA || []).find(c => c.subject_id == currentSubjectId);
            const partB = (data.data.partB || []).find(c => c.subject_id == currentSubjectId);

            function setupTimeSlot(element, course, partName) {
                if (!element) return;
                if (!course) {
                    element.classList.add('gray');
                    element.style.pointerEvents = 'none';
                    showToast(`No class data for ${partName}`, true);
                    return;
                }

                const classId = course.class_id?.toString().trim();
                const teacherId = parseInt(course.teacher_id);
                if (!classId || isNaN(teacherId) || teacherId <= 0) {
                    element.classList.add('gray');
                    element.style.pointerEvents = 'none';
                    showToast(`Invalid class data for ${partName}`, true);
                    return;
                }

                element.classList.remove('green', 'gray');
                const isAvailable = course.class_status.toLowerCase() === 'available';
                if (isAvailable) {
                    element.classList.add('green');
                    element.addEventListener('click', function() {
                        selectedClassInfo = {
                            class_id: classId,
                            capacity: parseInt(course.class_capacity) || 0,
                            enrolled: parseInt(course.class_enrolled) || 0,
                            teacher_id: teacherId
                        };
                        updatePopup(course);
                    });
                } else {
                    element.classList.add('gray');
                    element.style.pointerEvents = 'none';
                }
            }

            setupTimeSlot(document.getElementById('partA'), partA, 'Part A');
            setupTimeSlot(document.getElementById('partB'), partB, 'Part B');

            function updatePopup(course) {
                if (!course) return;
                const monthText = course.part_id == 1 ? "January - June" : "July - December";
                document.getElementById('popupMonth').textContent = monthText;
                document.getElementById('popupTeacher').textContent = course.teacher_name || 'Unknown';
                document.getElementById('popupVenue').textContent = course.class_venue || 'Unknown';
                document.getElementById('popupTime').textContent = course.class_time || 'Not set';
                document.getElementById('popupchildren').textContent = `${course.class_enrolled || 0} / ${course.class_capacity || 0}`;
                document.getElementById('popup').style.display = 'flex';

                const select = document.getElementById('childrenSelect');
                const childrenContainer = document.getElementById('childrenContainer');
                const addToCartButton = document.getElementById('addToCart');
                addToCartButton.disabled = false; // Ensure button is enabled on popup open

                if (!hasChildren) {
                    childrenContainer.innerHTML = `
                        <span style="color: #dc3545; font-size: 16px;">
                            No children registered. Please go to 
                            <a href="profile.php" style="color: #007bff; text-decoration: underline;">Profile's Children Information</a> 
                            to add a child.
                        </span>
                    `;
                } else {
                    select.value = '';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
            showToast('Failed to load class data', true);
        });

    // Existing fetch for children
    fetch('get_child.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log("Fetched children data:", data);
            let select = document.getElementById('childrenSelect');
            select.innerHTML = '<option value="">Choose</option>';

            if (!Array.isArray(data) || data.length === 0) {
                hasChildren = false;
                select.innerHTML += '<option disabled>No children found</option>';
                showToast('No children found. Please go to Profile\'s Children Information to add a child.', true);
                return;
            }

            const subjectYear = <?= json_encode(str_replace('Year ', '', $subject['year'])) ?>;
            let matchingChildren = data.filter(child => child.child_year == subjectYear);

            if (matchingChildren.length === 0) {
                hasChildren = false;
                select.innerHTML += '<option disabled>No matching children found</option>';
                showToast('No children match the subject year. Please go to Profile\'s Children Information to add a child.', true);
                return;
            }

            hasChildren = true;
            matchingChildren.forEach(child => {
                let option = document.createElement('option');
                option.value = child.child_name;
                option.textContent = child.child_name;
                select.appendChild(option);
            });
            const addToCartButton = document.getElementById('addToCart');
            addToCartButton.disabled = false; // Ensure button is enabled after children load
        })
        .catch(error => {
            console.error('Error fetching children:', error);
            hasChildren = false;
            showToast('Failed to load children data. Please go to Profile\'s Children Information to add a child.', true);
            const addToCartButton = document.getElementById('addToCart');
            addToCartButton.disabled = false; // Ensure button is enabled on error
        });

    // Existing event listeners
    const addToCartButton = document.getElementById('addToCart');
    const select = document.getElementById('childrenSelect');
    select.addEventListener('change', function() {
        addToCartButton.disabled = false; // Ensure button is enabled on change
        console.log('Child selected:', this.value);
    });

    document.getElementById('closePopup').addEventListener('click', function() {
        document.getElementById('popup').style.display = 'none';
        selectedClassInfo = null;
        const addToCartButton = document.getElementById('addToCart');
        addToCartButton.disabled = false; // Ensure button is enabled on close
        select.value = '';
        if (hasChildren) {
            document.getElementById('childrenContainer').innerHTML = `
                <select id="childrenSelect">
                    <option value="">Choose</option>
                </select>
            `;
        }
    });

    document.getElementById("addToCart").addEventListener("click", async function() {
        console.log('Add to Cart clicked, disabled:', this.disabled);
        if (!hasChildren) {
            showToast('No children registered. Please go to Profile\'s Children Information to add a child.', true);
            return;
        }
        if (!selectedClassInfo || !selectedClassInfo.class_id || isNaN(selectedClassInfo.teacher_id) || selectedClassInfo.teacher_id <= 0) {
            showToast('No valid class selected. Please select a time slot.', true);
            return;
        }

        try {
            showErrorBox('');
            const cartItem = {
                subject_id: <?= json_encode($subject_id) ?>,
                subject_name: document.getElementById("subjectName").innerText.trim(),
                price: parseFloat(document.getElementById("subjectPrice").innerText.replace('Price: RM', '').trim()),
                child_name: document.getElementById("childrenSelect").value.trim(),
                class_id: selectedClassInfo?.class_id || null,
                teacher_id: selectedClassInfo?.teacher_id || null
            };

            console.log("cartItem:", cartItem);

            if (!cartItem.child_name || cartItem.child_name === "") {
                showToast("Please select a child", true);
                return; // Stop execution if no child is selected
            }
            if (isNaN(cartItem.price)) {
                showToast("Invalid price", true);
                return;
            }

            const response = await fetch('save_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(cartItem)
            });

            const data = await response.json().catch(() => null);
            console.log("Response data:", data);

            if (!response.ok || !data || data.status !== 'success') {
                const errorMsg = data?.message || `HTTP error ${response.status}`;
                showErrorBox(errorMsg);
                showToast(errorMsg, true);
                return;
            }

            showToast(data.message || "Added to cart successfully!");
            document.getElementById('popup').style.display = 'none';
        } catch (error) {
            console.error("Add to cart error:", error);
            showErrorBox(error.message);
            showToast(error.message || "An error occurred", true);
        }
    });

    const avgRating = <?= $subject['avg_rating'] ?? 0 ?>;
    setRating(avgRating);

    function setRating(rating) {
        const stars = document.querySelectorAll('.stars-container .star');
        const fullStars = Math.floor(rating);
        const halfStar = (rating - fullStars) >= 0.5 ? 1 : 0;
        stars.forEach(star => star.classList.remove('yellow', 'half'));
        for (let i = 0; i < fullStars; i++) {
            stars[i].classList.add('yellow');
        }
        if (halfStar) {
            stars[fullStars].classList.add('half');
        }
        document.querySelector('.rating-number').textContent = rating.toFixed(1);
    }

    function fetchReviews() {
        const subject_id = "<?= htmlspecialchars($subject_id) ?>";
        fetch(`get_comments.php?subject_id=${subject_id}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                displayReviews(data.data || []);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('review-list').innerHTML = `<p class="error">Error loading reviews: ${error.message}</p>`;
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

    fetchReviews();
});
</script>
</body>
</html>