<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Teacher</title>

    <!-- Favicon -->
    <link href="img/the seeds.jpg" rel="icon" type="image/png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      height: 100vh;
      overflow-x: hidden;
    }
    #sidebar {
      width: 250px;
      background-color: #343a40;
      color: white;
      transition: width 0.3s;
      white-space: nowrap;
      overflow: hidden;
    }
    #sidebar a {
      color: white;
      text-decoration: none;
      padding: 15px 15px; 
      display: block;
    }
    #sidebar a:hover {
      background-color: #495057;
    }
    #sidebar.collapsed {
      width: 80px;
    }
    #sidebar.collapsed .sidebar-header h4,
    #sidebar.collapsed a span {
      display: none;
    }
    #main-content {
      flex-grow: 1;
      padding: 20px;
      transition: margin-left 0.3s;
    }
    .sidebar-header {
      display: flex;
      align-items: center;
      padding: 10px;
      border-bottom: 1px solid #495057;
    }
    .sidebar-header img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }
    .sidebar-header h4 {
      margin: 0;
      font-size: 16px;
    }
    .table-container .fa-edit, .table-container .fa-trash-alt {
      color: #007BFF;
    }

    .pointer-cursor{
      cursor:pointer;
    }

   
    .card-header form {
        display: flex;
        justify-content: flex-start;; 
        width: 100%;
    }
    .card-header .form-control {
        width: 250px; 
        margin-right: 10px; 
    }
    .card-header button {
        padding: 8px 15px; 
    }

    .form-label {
      font-weight: bold;
    }

   .notification-badge {
    position: absolute;
    top: -5px;  
    right: -5px; 
    width: 10px; 
    height: 10px; 
    background-color: #ff0000;
    border-radius: 50%;
    display: none;
    border: 2px solid #fff;
}

    .nav-item {
        position: relative;
    }

     .new-badge {
        background-color:rgb(0, 255, 132);
        color: black;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
        margin-left: 8px;
    }
    


    
</style>

<body>

  <!-- Sidebar -->
  <nav id="sidebar" class="d-flex flex-column p-3">
    <div class="sidebar-header">
      <h3>Teacher Panel</h3>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item"><a href="admin teacher.php" class="nav-link"><i class="fas fa-calendar"></i> <span>My schedule</span></a></li> 
      <li class="nav-item"><a href="teacher_classes.php" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> <span>Classes</span></a></li>
      <li class="nav-item"><a href="teacher_announcement.php" class="nav-link"><i class="fas fa-envelope"></i> <span>Announcement</span></a></li>    
    </ul>
  </nav>

  <!-- Main Content -->
  <div id="main-content">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
      <div class="container-fluid">
        <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto">
        
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="notificationBell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge"></span>
                </a>

            </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
              <li><a class="dropdown-item" href="admin teacher_profile.html">Profile</a></li>
              <li><a class="dropdown-item" href="admin login.html">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

  
  <div class="container">

    <!-- Notification -->
    <h1 class="mb-4">Notification</h1>
    <div class="card">
    <div class="card-header">
    
    </div>
    <div class="card-body">
        <div id="notification-list" class="notification-container">
        <div class="text-muted text-center">Loading notifications...</div>
        </div>
    </div>
    </div>

 
</body>



  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });
    
    // loading notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadNotifications();
    });

    // Sidebar toggle functionality
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });
    
    function loadNotifications() {
    fetch("teacher_get_notification.php")
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("notification-list");
            container.innerHTML = "";

            if (data.error) {
                container.innerHTML = `<div class='text-muted text-center'>${data.error}</div>`;
                return;
            }

            if (data.length === 0) {
                container.innerHTML = "<div class='text-muted text-center'>No notification yet.</div>";
            } else {
                data.sort((a, b) => new Date(b.notification_created_at) - new Date(a.notification_created_at));

                data.forEach(n => {
                    const time = n.notification_created_at ? new Date(n.notification_created_at).toLocaleString() : '';
                    const block = document.createElement("div");
                    block.className = "card mb-3 shadow-sm";
                    
                    
                    block.onclick = function() {
                        if (n.is_unread) {
                            markNotificationAsRead(n.notification_id, block);
                        }
                    };

                    block.innerHTML = `
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>From: ${n.sender_name}</strong>
                                    ${n.is_unread ? '<span class="new-badge">New</span>' : ''}
                                </div> 
                                <small class="text-muted">${time}</small>
                            </div>
                            <p class="card-text mb-1"><strong>Title:</strong> ${n.notification_title}</p>
                            <p class="card-text">${n.notification_content}</p>
                            ${n.notification_document ? `
                                <a href="${n.notification_document}" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
                                    <i class="fas fa-paperclip me-1"></i>View Attachment
                                </a>
                            ` : ''}
                        </div>
                    `;

                    container.appendChild(block);
                });
            }
        })
        .catch(error => {
            const container = document.getElementById("notification-list");
            container.innerHTML = "<div class='text-danger text-center'>Error loading notifications. Please try again later.</div>";
            console.error('Error:', error);
        });
}

// singele notification mark as read
function markNotificationAsRead(notificationId, element) {
    fetch(`teacher_get_notification.php?mark_single_read=true&notification_id=${notificationId}`)
        .then(res => res.json())
        .then(data => {
            // remove the "New"
            const newBadge = element.querySelector('.new-badge');
            if (newBadge) {
                newBadge.remove();
            }
            
            // check if the notification bell should be marked as read
            checkUnreadNotifications();
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
}


 function checkUnreadNotifications() {
            fetch("teacher_get_notification.php?check_unread=true")
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    console.log('Unread count:', data.unread_count); 
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

        // check for unread notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
            checkUnreadNotifications();
            
            // every 30 seconds check for unread notifications
            setInterval(checkUnreadNotifications, 30000);
        });
  </script>
</body>
</html>