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
      <li class="nav-item"><a href="teacher_notification.php" class="nav-link"><i class="fas fa-envelope"></i> <span>Announcement</span></a></li>    
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
              <li><a class="dropdown-item" href="#">No new notifications</a></li>
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
    <h1 class="mb-4">Announcement</h1>
    <div class="card">
    <div class="card-header">
    <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
     Add Announcement
    </button>
    </div>
    <div class="card-body">
        <div id="notification-list" class="notification-container">
        <div class="text-muted text-center">Loading notifications...</div>
        </div>
    </div>
    </div>

 
</body>


    <!-- Send Notification Modal -->
    <div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="sendNotificationModalLabel">Announcement</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="notificationForm" enctype="multipart/form-data">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="subject_id" class="form-label">Subject</label>
                  <select class="form-select form-select-sm" name="subject_id" id="subject_id" required>
                    <option value="">-- Select Subject --</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="class_id" class="form-label">Class</label>
                  <select class="form-select form-select-sm" name="class_id" id="class_id" required>
                    <option value="">-- Select Class --</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-8">
                  <label for="notification_title" class="form-label">Title</label>
                  <input type="text" class="form-control form-control-sm" name="notification_title" id="notification_title" placeholder="Enter the title" required>
                </div>
                <div class="col-md-4">
                  <label for="notification_document" class="form-label">Attachment</label>
                  <input type="file" class="form-control form-control-sm" name="notification_document" id="notification_document" accept="image/*">
                </div>
              </div>

              <div class="mb-3">
                <label for="notification_content" class="form-label">Content</label>
                <textarea class="form-control form-control-sm" name="notification_content" id="notification_content" rows="4" placeholder="Write your content..." required></textarea>
              </div>

              <input type="hidden" name="sender_id" value="Teacher_001">

              <div class="text-end">
                <button type="submit" class="btn btn-sm btn-success">Send Announcement</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      loadSubjects();
      loadNotifications();
    });

    function loadSubjects() {
  fetch("teacher_notification_data.php?action=subject")
    .then(res => res.json())
    .then(subjects => {
      const subjectSelect = document.getElementById("subject_id");
      subjectSelect.innerHTML = "<option value=''>-- Select Subject --</option>";
      subjects.forEach(subject => {
        const option = document.createElement("option");
        option.value = subject.subject_id;
        option.textContent = `${subject.subject_id} - ${subject.subject_name}`;
        subjectSelect.appendChild(option);
      });
    })
    .catch(err => console.error('Error loading subjects:', err));
}

document.getElementById("subject_id").addEventListener("change", function () {
  const subjectId = this.value;
  const classSelect = document.getElementById("class_id");
  classSelect.innerHTML = "<option value=''>-- Select Class --</option>";
  if (subjectId) {
    fetch(`teacher_notification_data.php?action=class&subject_id=${subjectId}`)
      .then(res => res.json())
      .then(classes => {
        classes.forEach(cls => {
          const option = document.createElement("option");
          option.value = cls.class_id;
          option.textContent = cls.class_id;
          classSelect.appendChild(option);
        });
      })
      .catch(err => console.error('Error loading classes:', err));
  }
});


document.getElementById("notificationForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("teacher_notificationlist.php", {
        method: "POST",
        body: formData
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => Promise.reject(err));
        }
        return res.json();
    })
    .then(msg => {
        if (msg.success) {
            showToast(msg.message);  
        } else {
            showToast(msg.error || 'An error occurred', true);  
        }

        this.reset();
        const modal = bootstrap.Modal.getInstance(document.getElementById("sendNotificationModal"));
        modal.hide();
        loadNotifications();
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'An error occurred while sending the notification. Please try again.', true); 
    });
});

// Toast Notification Function
function showToast(message, isError = false) {
    let toast = document.getElementById("successToast");
    if (!toast) {
       
        toast = document.createElement("div");
        toast.id = "successToast";
        toast.className = "toast-message";
        document.body.appendChild(toast);
    }

    toast.innerText = message;
    toast.style.backgroundColor = isError ? "#dc3545" : "#28a745";
    toast.style.display = "block";
    toast.style.opacity = "1";

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => { toast.style.display = "none"; }, 500);
    }, 3000);
}



    function loadNotifications() {
  fetch("teacher_get_notification.php")
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById("notification-list");
      container.innerHTML = "";

      if (data.length === 0) {
        container.innerHTML = "<div class='text-muted text-center'>No release any announcements yet.</div>";
      } else {
        data.reverse().forEach(n => {
          const time = n.notification_created_at ? new Date(n.notification_created_at).toLocaleString() : '';
          const block = document.createElement("div");
          block.className = "card mb-3 shadow-sm";

          block.innerHTML = `
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div><strong>${n.sender_name}</strong></div> 
                <small class="text-muted">${time}</small>
              </div>
              <p class="card-text mb-1"><strong>Title:</strong> ${n.notification_title}</p>
              <p class="card-text mb-1"><strong>To Class:</strong> ${n.class_id}</p>
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
    });
}


  </script>
</body>
</html>