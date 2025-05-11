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
      padding: 15px 15px; /* Adjusted padding for better spacing */
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

    .submit-btn-container {
      text-align: center;
    }

   
    .notification-item {
    border-left: 5px solid #0d6efd;
    background-color: #f8f9fa;
  }

    .send-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 6px 14px;
    font-size: 14px;
    border-radius: 4px;
    transition: background-color 0.3s ease;
    }

    .send-btn:hover {
    background-color: #0056b3;
    }

    .toast-message {
    position: fixed;
    top: 10%; 
    left: 50%;
    transform: translateX(-50%);
    background-color: #155724;
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
            <a class="nav-link dropdown-toggle" href="#" id="notifications" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-bell"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifications">
              <li><a class="dropdown-item" href="teacher_notification">Notification</a></li>
            </ul>
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


     <!-- Breadcrumb Section -->
    <div class="breadcrumb-container">
        <h1>Notifications</h1>
        <ul class="breadcrumb">
            <li>Notifications</li>
        </ul>
    </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });
    
   

  </script>
</body>
</html>