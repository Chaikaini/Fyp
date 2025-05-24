<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Notifications</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre | Notifications" name="keywords">
    <meta content="The Seeds Learning Centre | Notifications" name="description">

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

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <style>
        /* Notification page specific styles */
    .notification-page {
        padding: 20px;
    }

    .search-container {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }   

    .search-container input {
        padding: 10px;
        font-size: 16px;
        width: 250px;
        border-radius: 5px 0 0 5px;
        border: 1px solid #ccc;
    }

    .search-container button {
        padding: 10px;
        font-size: 16px;
        background-color: #2962ff;
        border: none;
        color: white;
        cursor: pointer;
        border-radius:  0 5px 5px 0;

    }

    .search-container button:hover {
        background-color: #1d5bbb;
    }

   .notification-item {
        background-color: #f8f9fa;
        border: 1px solid #d1d1d1;
        border-radius: 8px;
        margin-bottom: 15px;
        padding: 15px;
        cursor: pointer;
    }



    .notification-item .title {
        font-weight: bold;
        font-size: 16px;
    }

    .notification-item .content {
        font-size: 14px;
        color: #555;
    }

    .notification-item .date {
        text-align: right;
        font-size: 12px;
        color: #aaa;
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


    
    .icon-bar {
        display: flex;
        justify-content: flex-end;
        padding: 10px 0;
        background-color: #f8f9fa;
        border-bottom: 1px solid #caccce;
        position: relative; 
    }

    .icon-bar a {
        position: relative;
        margin: 0 15px;
        color: #000000;
        font-size: 24px;
        transition: color 0.3s ease;
        display: inline-block;
}

    .icon-bar a:hover {
        color: #73cf67;
    }

    
    .icon-bar a.fa-bell {
        display: none;
    }

    .notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    /* Inbox Modal Style */
    .inbox-modal {
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        border-radius: 10px;
    }

    .inbox-link {
        display: inline-block;
        margin-top: 10px;
        color: #007bff;
        text-decoration: none;
    }

    .inbox-link:hover {
        text-decoration: underline;
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
    .new-badge {
    background-color: rgb(0, 255, 132);
    color: black;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 12px;
    margin-left: 8px;
}

    </style>
</head>

<body>
    <!-- Navbar Start -->
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
            </div>
            <a href="logout.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Log out<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->

   
    <div class="icon-bar">
        <a href="notification.php" onclick="event.preventDefault(); return false;">
            <i class="fas fa-bell"></i>
            <span id="notificationDot" class="notification-badge"></span>
        </a>
        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>

    <!-- Breadcrumb Section -->
    <div class="breadcrumb-container">
        <h1>Announcement</h1>
        <ul class="breadcrumb">
            <li><a href="member.html">Home</a></li>
            <li>&gt;</li>
            <li>Announcement</li>
        </ul>
    </div>

    <!-- Notification Content Section -->
    <div class="container">

        <div class="card">
            <div class="card-body">
                <div id="notification-list" class="notification-container">
                    <div class="text-muted text-center">Loading announcement...</div>
                </div>
            </div>
        </div>
    </div>




<!-- JavaScript -->
<script>
    
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
                    loadYears();
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
    loadNotifications();
    checkUnreadNotifications();
    setInterval(checkUnreadNotifications, 30000);
});

 function loadNotifications() {
    fetch('get_notification.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('notification-list');
            
            if (data.error) {
                container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }

            container.innerHTML = '';

            // Sort and display notifications
            data.notifications.sort((a, b) => new Date(b.notification_created_at) - new Date(a.notification_created_at));

            // Display regular notifications
            data.notifications.forEach(notif => {
                const block = document.createElement('div');
                block.className = 'card mb-3 shadow-sm';
                
                const createdAt = new Date(notif.notification_created_at);
                const day = String(createdAt.getDate()).padStart(2, '0');
                const month = String(createdAt.getMonth() + 1).padStart(2, '0');
                const year = createdAt.getFullYear();
                let hours = createdAt.getHours();
                const minutes = String(createdAt.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                hours = String(hours).padStart(2, '0');
                const formattedDate = `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;


                block.onclick = function() {
                    if (notif.read_status === 'unread') {
                        markNotificationAsRead(notif.notification_id, block);
                    }
                };

                block.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>${notif.sender_name} - ${notif.subject_name === 'General Announcement' ? 
                                    notif.subject_name : 
                                    `${notif.year} ${notif.subject_name}`}
                                </strong>
                                ${notif.read_status === 'unread' ? '<span class="new-badge">New</span>' : ''}
                            </div>
                            <small class="text-muted">${formattedDate}</small>
                        </div>
                        <p class="card-text mb-1"><strong>${notif.notification_title}</strong></p>
                        <p class="card-text">${notif.notification_content}</p>
                        ${notif.notification_document ? `
                            <a href="${notif.notification_document}" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                <i class="fas fa-paperclip me-1"></i>View Attachment
                            </a>
                        ` : ''}
                    </div>
                `;

                container.appendChild(block);
            });

            // A welcome message 
           if (data.first_login) {
            const welcomeBlock = document.createElement('div');
            welcomeBlock.className = 'card mb-3 shadow-sm';

            welcomeBlock.innerHTML = `
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Welcome to The Seeds</strong>
                    </div>
                    <p class="card-text">Hello ${data.user_name}! Thank you for joining us.</p>
                </div>
            `;
            container.appendChild(welcomeBlock);
        }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('notification-list').innerHTML = `
                <div class="alert alert-danger">
                    Failed to load notifications. Please try again later.
                </div>
            `;
        });
}

    function markNotificationAsRead(notificationId, element) {
        fetch('get_notification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `mark_single_read=true&notification_id=${notificationId}`
        })
        .then(res => res.json())
        .then(data => {
            const newBadge = element.querySelector('.new-badge');
            if (newBadge) {
                newBadge.remove();
            }
            checkUnreadNotifications();
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

  function checkUnreadNotifications() {
    fetch('get_notification.php?check_unread=true')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('notificationDot');
            if (data.unread_count && data.unread_count > 0) {
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error checking notifications:', error);
        });
}
</script>






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
</body>

</html>
