<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Class</title>
  
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
    .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            position: absolute; 
            top: 10px; 
            right: 10px; 
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-content form {
            display: flex;
            flex-direction: column;
        }
        .modal-content label {
            margin-top: 10px;
        }
        .modal-content input[type="text"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .modal-content input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            background-color: #2962ff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .modal-content input[type="submit"]:hover {
            background-color: #0039cb;
        }

        .modal-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .modal-footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 20px;
            text-align: right;
        }
        .list-group {
            list-style: none;
            padding: 0;
        }
        .list-group-item {
            padding: 10px 15px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .list-group-item:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .pointer-cursor {
          cursor:pointer !important;
           
        }
        .success-message {
        background-color: #d4edda;
        color:rgb(41, 172, 220)；
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        font-weight: normal;
        
        }

        .toast-message {
        position: fixed;
        top: 10%; 
        left: 50%;
        transform: translateX(-50%);
        background-color:rgb(171, 241, 187); 
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        font-size: 16px;
        font-weight: normal;
        z-index: 1000;
        text-align: center;
        transition: opacity 0.5s ease-in-out;
    }

        .modal-d {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
       }
       .modal-dcontent {
       background-color: white;
       padding: 20px;
       border-radius: 8px;
       text-align: center;
       width: 300px;
       }
   
  </style>
</head>
<body>
  <!-- Sidebar -->
  <nav id="sidebar" class="d-flex flex-column p-3">
    <div class="sidebar-header">
      <h3>Admin Panel</h3>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item"><a href="dashboard.html" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li class="nav-item"><a href="admin staff.html" class="nav-link"><i class="fas fa-user"></i> <span>Staff List</span></a></li>
      <li class="nav-item"><a href="children.list.html" class="nav-link"><i class="fas fa-graduation-cap"></i> <span>Children List</span></a></li>
      <li class="nav-item"><a href="parent list.html" class="nav-link"><i class="fas fa-users"></i> <span>Parent List</span></a></li>
      <li class="nav-item"><a href="view order.html" class="nav-link"><i class="fas fa-shopping-cart"></i> <span>Order List</span></a></li>
      <li class="nav-item"><a href="admin subject.php" class="nav-link"><i class="fas fa-book"></i> <span>Subject List</span></a></li>
      <li class="nav-item"><a href="admin class.php" class="nav-link"><i class="fas fa-school"></i> <span>Class List</span></a></li>
      <li class="nav-item"><a href="admin notification.html" class="nav-link"><i class="fas fa-envelope"></i> <span>Notification</span></a></li>
      <li class="nav-item">
        <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#reportMenu" aria-expanded="false">
          <i class="fas fa-chart-line"></i> <span>Report</span>
          <i class="fas fa-chevron-down ms-2" id="reportIcon"></i> <!-- Add this icon for expand/collapse -->
        </a>
        <div class="collapse" id="reportMenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item"><a href="payment report.html" class="nav-link"><i class="fas fa-file-invoice"></i> <span>Payment report</span></a></li>
            <li class="nav-item"><a href="subject enroll report.html" class="nav-link"><i class="fas fa-file-alt"></i> <span>Subject Enrollment report
            </span></a></li>
            <li class="nav-item"><a href="new student report.html" class="nav-link"><i class="fas fa-file-alt"></i> <span>New Children report</span></a></li>
          </ul>
        </div>
      </li>      
    </ul>
  </nav>

  <!-- Main Content -->
  <div id="main-content">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
      <div class="container-fluid">
        <button class="btn btn-outline-secondary me-2" id="toggleSidebar"><i class="fas fa-bars"></i></button>
        <form class="d-flex ms-auto">
           <input class="form-control me-2" type="search" placeholder="Search" id="search" />
           <button class="btn btn-outline-success" type="button" id="search-btn">Search</button>
        </form>
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
              <li><a class="dropdown-item" href="admin profile.html">Profile</a></li>
              <li><a class="dropdown-item" href="admin login.html">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Class List -->
    <h1 class="mb-4">Class List</h1>
    <div class="card">
    <div class="card-header">
        <h5>Class List</h5>
        <button class="btn btn-primary float-end" id="addClassBtn">Add Class</button>
    </div>

  
  <tbody>
      <?php include 'admin_classlist.php'; ?>
  </tbody>
</table>
      </div>
    </div>
  </div>
  <!-- The Modal -->
   <div id="addClassModal" class="modal">
    <div class="modal-content pointer-cursor">
        <span class="close ">&times;</span>
        <form id="addClassForm" method="POST" action="admin_addclass.php">
        <label for="subject_id">Subject ID:</label>
        <input type="text" id="subject_id" name="subject_id" required>
        <label for="class_id">Class ID:</label>
        <input type="text" id="class_id" name="class_id" required>
        <label for="year">Year:</label>
        <input type="text" id="year" name="year" required>
        <label for="part">Part:</label>
        <input type="text" id="part" name="part_id" required>
        <label for="class_time">Time:</label>
        <input type="text" id="class_time" name="class_time" required>
        <label for="teacher">Teacher:</label>
        <input type="text" id="teacher" name="teacher_id" required>
        <label for="class_capacity">Maximum Capacity:</label>
        <input type="number" id="class_capacity" name="class_capacity" required>
        <label for="class_status">Status:</label>
           <select id="class_status" name="class_status" required>
             <option value="available">Available</option>
             <option value="unavailable">Unavailable</option>
           </select>
        <input type="submit" value="Add Class">
        </form>
        </div>
    </div>
 <div id="successToast" class="toast-message" style="display: none;"></div>


 <!-- Delete Class Confirm Modal -->
<div id="deleteClassConfirmModal" class="modal-d">
    <div class="modal-dcontent">
        <h4>Confirm Deletion</h4>
        <p>Are you sure you want to delete this class?</p>
        <button id="confirmDeleteClassBtn" class="btn btn-danger">Delete</button>
        <button id="cancelDeleteClassBtn" class="btn btn-secondary">Cancel</button>
    </div>
</div>


  <!-- Modal to view students -->
<div id="studentModal" class="modal">
    <div class="modal-content pointer-cursor">
        <span class="close" onclick="closeStudentModal()">&times;</span>
        <div class="modal-header">
            <h5>Students List - <span id="class-id-title"></span></h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody id="student-list">
                    <!-- Student list will be dynamically added here -->
                </tbody>
            </table>
        </div>
        <div class="modal-footer pointer-cursor">
            <button type="button" class="btn btn-secondary" onclick="closeStudentModal()">Close</button>
        </div>
    </div>
</div>
   

<script>

  document.querySelector('[data-bs-toggle="collapse"]').addEventListener('click', function() {
   const icon = document.getElementById('reportIcon');
   if (icon.classList.contains('fa-chevron-down')) {
    icon.classList.remove('fa-chevron-down');
    icon.classList.add('fa-chevron-up');
   } else {
    icon.classList.remove('fa-chevron-up');
    icon.classList.add('fa-chevron-down');
   }
  });

  var modals = document.querySelectorAll(".modal");
  var addClassBtn = document.getElementById("addClassBtn");
  var closeButtons = document.querySelectorAll(".close");

  addClassBtn.onclick = function() {
    document.getElementById("addClassModal").style.display = "block";
  }

  closeButtons.forEach(function(btn) {
    btn.onclick = function() {
      btn.closest(".modal").style.display = "none";
    }
  });

  window.onclick = function(event) {
    modals.forEach(function(modal) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    });
  }

 

  document.getElementById("addClassForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("admin_addclass.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            document.getElementById("addClassModal").style.display = "none";
            document.getElementById("addClassForm").reset();
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast(data.message, true);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showToast("An error occurred. Please try again.", true);
    });
});




  function openModal() {
    document.getElementById('editModal').style.display = 'block';
  }

  function closeModal() {
    document.getElementById('editModal').style.display = 'none';
  }



  document.addEventListener("DOMContentLoaded", function () {
    let selectedClassId = null;

    // delete function
    document.querySelector("tbody").addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-btn")) {
            selectedClassId = event.target.getAttribute("data-classid");
            document.getElementById("deleteClassConfirmModal").style.display = "flex";
        }
    });

    document.getElementById("cancelDeleteClassBtn").addEventListener("click", function () {
        document.getElementById("deleteClassConfirmModal").style.display = "none";
    });

    document.getElementById("confirmDeleteClassBtn").addEventListener("click", function () {
        if (selectedClassId) {
            fetch("admin_deleteclass.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "class_id=" + encodeURIComponent(selectedClassId)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("deleteClassConfirmModal").style.display = "none";
                if (data.success) {
                    showToast("Class deleted successfully!");
                    setTimeout(() => { location.reload(); }, 2000);
                } else {
                    showToast("Error: " + data.error, true);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showToast("An error occurred while deleting the class.", true);
            });
        }
    });
});



document.getElementById("search-btn").addEventListener("click", function() {
    let searchQuery = document.getElementById("search").value.trim();

    fetch("admclass_search.php?query=" + encodeURIComponent(searchQuery))
        .then(response => response.text())
        .then(data => {
            document.querySelector("tbody").innerHTML = data;
        })
        .catch(error => console.error("Error:", error));
});

// Toast Notification Function
function showToast(message, isError = false) {
    let toast = document.getElementById("successToast");
    toast.innerText = message;
    toast.style.backgroundColor = isError ? "#dc3545" : "#007bff";
    toast.style.display = "block";
    toast.style.opacity = "1";

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => { toast.style.display = "none"; }, 500);
    }, 3000);
}


document.addEventListener("DOMContentLoaded", function () {
    // find all class_id element，and use updateEnrollment
    const enrolledSpans = document.querySelectorAll("[id^='enrolled-']");
    enrolledSpans.forEach(span => {
        const classId = span.id.replace("enrolled-", "");
        updateEnrollment(classId); 
    });
});

function updateEnrollment(classId) {
    if (!classId) return;

    fetch('adminclass_update.php?timestamp=' + new Date().getTime(), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'class_id=' + encodeURIComponent(classId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const enrolledElement = document.getElementById('enrolled-' + classId);
            if (enrolledElement) {
                const capacity = enrolledElement.getAttribute('data-capacity');
                enrolledElement.textContent = `${data.class_enrolled}/${capacity}`;
            }
        }
    })
    .catch(error => console.error('Error updating enrollment:', error));
}



function viewStudents(classId) {
        // Display class id in modal header
        document.getElementById("class-id-title").textContent = classId;

        // Fetch the student information via AJAX
        fetch('adminclass_students.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'class_id=' + classId
        })
        .then(response => response.json())
        .then(data => {
            const studentList = document.getElementById("student-list");
            studentList.innerHTML = ''; // Clear previous list

            if (data.success) {
                // Dynamically populate the student list
                data.students.forEach(student => {
                    const row = `<tr>
                        <td>${student.name}</td>
                        <td>${student.gender}</td>
                        <td>${student.email}</td>
                    </tr>`;
                    studentList.innerHTML += row;
                });

                // Open the modal to view students
                document.getElementById("studentModal").style.display = "block";
            } else {
                alert(data.message || 'Failed to load students.');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function closeStudentModal() {
        document.getElementById("studentModal").style.display = "none";
    }
</script>


 

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });
  </script>
</body>
</html>