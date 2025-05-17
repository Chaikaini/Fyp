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

    .notification-dot {
    position: absolute;
    top: 0;
    right: 0;
    width: 10px;
    height: 10px;
    background-color: red;
    border-radius: 50%;
    display: none; 
}


    </style>
</head>

<body>
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
        <a href="notification.php">
            <i class="fas fa-bell"></i>
            <span id="notificationDot" class="notification-dot"></span>  <!-- red point -->
        </a>
        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>

    <!-- Breadcrumb Section -->
    <div class="breadcrumb-container">
        <h1>Notifications</h1>
        <ul class="breadcrumb">
            <li><a href="member.html">Home</a></li>
            <li>&gt;</li>
            <li>Notifications</li>
        </ul>
    </div>

    <!-- Notification Page -->
    <div class="notification-page">
        <div id="welcomeMessage" >
            <!-- welcome message will display here -->
        </div>
    </div>




<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('get_notification.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('welcomeMessage');

            if (data.error) {
                container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }

            container.innerHTML = '';

            // Sort notifications by date (newest first)
            data.notifications.sort((a, b) => new Date(b.notification_created_at) - new Date(a.notification_created_at));

            // show notifications
            data.notifications.forEach(notif => {
                const notificationItem = document.createElement('div');
                notificationItem.className = 'notification-item';
                notificationItem.dataset.notificationId = notif.notification_id;

                let statusLabel = '';
                if (notif.read_status === 'unread') {
                    statusLabel = '<span class="new-label">(New)</span>';
                }

                const createdAt = new Date(notif.notification_created_at);

                const datePart = createdAt.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });

                const timePart = createdAt.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

                const formattedDate = `${datePart} | ${timePart}`;

                notificationItem.innerHTML = `
                    <div class="notification-header">
                        <div class="sender">${notif.sender_name} - ${notif.subject_name === 'General Announcement' ? 
                            notif.subject_name : // if subject name is General Announcement,not show year
                            `${notif.year} ${notif.subject_name}`// else show year and subject name
                        }</div>   
                        <div class="date">${formattedDate}</div>
                    </div>
                    <div class="title">${notif.notification_title} ${statusLabel}</div>
                    <div class="content">${notif.notification_content}</div>
                    ${notif.notification_document ? `<div><a href="${notif.notification_document}" target="_blank">View Document</a></div>` : ''}
                `;

                // when click, mark as read
                notificationItem.addEventListener('click', function () {
                    const notificationId = this.dataset.notificationId;
                    fetch('mark_as_read.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `notification_id=${notificationId}`
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            const titleDiv = this.querySelector('.title');
                            const newLabel = titleDiv.querySelector('.new-label');
                            if (newLabel) newLabel.remove();

                            titleDiv.style.fontWeight = 'normal';
                            titleDiv.style.color = 'black';
                        }
                    })
                    .catch(error => console.error('Error marking as read:', error));
                });

                container.appendChild(notificationItem); 
            });

            // Welcome message down here
            if (data.first_login) {
                const welcomeMsg = `
                    <div class="notification-item">
                        <div class="title">Welcome to The Seeds</div>
                        <div class="content">Hello ${data.user_name}! Thank you for joining us.</div>
                        <div class="date">This is your first login!</div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', welcomeMsg); 
            }

            // unread notification red point
            const notificationDot = document.getElementById('notificationDot');
            const hasUnread = data.notifications && data.notifications.some(notif => notif.read_status === 'unread');
            notificationDot.style.display = hasUnread ? 'block' : 'none';

            // when click the bell icon, hide the red point
            document.querySelector('.fa-bell').addEventListener('click', function () {
                notificationDot.style.display = 'none';
            });
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('welcomeMessage').innerHTML = `
                <div class="alert alert-danger">
                    Failed to load notifications. Please try again later.
                </div>
            `;
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
</body>

</html>
