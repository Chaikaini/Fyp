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
</style>

<body>
  <!-- Sidebar -->
  <nav id="sidebar" class="d-flex flex-column p-3">
    <div class="sidebar-header">
      <h3>Teacher Panel</h3>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item"><a href="" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li class="nav-item"><a href="admin teacher.php" class="nav-link"><i class="fas fa-user"></i> <span>Teacher</span></a></li> 
      <li class="nav-item"><a href="admin attendance.html" class="nav-link"><i class="fas fa-user"></i> <span>Attendance</span></a></li>    
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
              <li><a class="dropdown-item" href="admin profile.html">Profile</a></li>
              <li><a class="dropdown-item" href="admin login.html">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Attendance -->
    <h1 class="mb-4">Attendance</h1>
    <div class="card">
      <div class="card-header">
        <form class="d-flex ms-auto">
          <input class="form-control me-2" type="search" placeholder="Search with Subject ID" id="search" />
          <button class="btn btn-outline-success" type="button" id="search-btn">Search</button>
          </form>
      </div>
      <div class="card-body">
        <table class="table table-striped">
            <thead>
              <tr>
                <th>Subject ID</th>
                <th>Class ID</th>
                <th>Subject Name</th>
                <th>Year</th>
                <th>Part</th>
                <th>Time</th>
                <th>Class capasity</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="schedule-table-body">
              <!-- JS will insert rows here -->
            </tbody>
          </table>
      </div>
    </div>
  </div>
</body>

 
<!-- Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">Student List For  <span id="modal-class-id"></span> </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>Gender</th>
              <th>Kid Number</th>
              <th>Parent Name</th>
              <th>Phone Number</th>
            </tr>
          </thead>
          <tbody id="student-modal-body">
            <!-- Data will be inserted here -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
<script>

    document.getElementById("search-btn").addEventListener("click", function () {
        const subjectId = document.getElementById("search").value.trim();
    
        if (!subjectId) {
            alert("Please enter a valid Subject ID.");
            return;
        }
    
        fetch("teacher_attendance_info.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "subject_id=" + encodeURIComponent(subjectId),
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("schedule-table-body");
            tbody.innerHTML = "";
    
            if (data.error) {
                tbody.innerHTML = `<tr><td colspan='8'>${data.error}</td></tr>`;
            } else if (data.length === 0) {
                tbody.innerHTML = "<tr><td colspan='8'>No data found.</td></tr>";
            } else {
                data.forEach(row => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${row.subject_id}</td>
                            <td>${row.class_id}</td>
                            <td>${row.subject_name}</td>
                            <td>${row.year}</td>
                            <td>${row.part}</td>
                            <td>${row.time}</td>
                            <td>${row.capacity}</td>
                            <td>
                            <i class='pointer-cursor fas fa-eye text-info view-icon' onclick='viewStudents("${row.class_id}")'></i>

                            <button class="btn btn-primary">Take Attendance</button>
                            </td>
                        </tr>
                    `;
                });
            }
        })
        .catch(error => {
            console.error("Error loading attendance:", error);
            document.getElementById("schedule-table-body").innerHTML = "<tr><td colspan='8'>Error loading data.</td></tr>";
        });
    });



    function viewStudents(classId) {

      document.getElementById("modal-class-id").textContent = classId;


      document.getElementById("student-modal-body").innerHTML = "<tr><td colspan='5'>Loading...</td></tr>";



      // view student list for the selected class

    fetch("teacher_studentinfo.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "class_id=" + encodeURIComponent(classId),
    })
    .then(res => res.json())
    .then(data => {
    const tbody = document.getElementById("student-modal-body");
    tbody.innerHTML = "";

    
    const modal = new bootstrap.Modal(document.getElementById("studentModal"));
    modal.show();

    if (data.error) {
        tbody.innerHTML = `<tr><td colspan='5'>${data.error}</td></tr>`;
    } else if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan='5' class="text-center">No student enrolled yet</td></tr>`;
    } else {
      data.forEach((row, index) => {
    tbody.innerHTML += `
        <tr>
            <td>${index + 1}. ${row.child_name}</td>
            <td>${row.child_gender}</td>
            <td>${row.child_kidnumber}</td>
            <td>${row.parent_name}</td>
            <td>${row.phone_number}</td>
        </tr>
            `;
        });
    }
})

    .catch(err => {
        console.error("Error fetching students:", err);
        document.getElementById("student-modal-body").innerHTML = "<tr><td colspan='5'>Error loading students.</td></tr>";
    });
}

    </script>
    

   
      
</body>
</html>