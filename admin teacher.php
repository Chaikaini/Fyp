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

    .search-category {
    width: 150px;
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

    <!-- Schedule -->
    <h1 class="mb-4">View My Schedule</h1>
    <div class="card">
      <div class="card-header">
      <form class="d-flex ms-auto align-items-center">
      <select id="search-category" class="form-select search-category">
          <option value="teacher_id">Teacher ID</option>
          <option value="subject_name">Subject Name</option>
        </select>
          <input class="form-control me-2" type="search" placeholder="Search with Teacher ID" id="search" />
          <button class="btn btn-outline-success" type="button" id="search-btn">Search</button>
      </div>
      <div class="card-body">
        <table class="table table-striped">
            <thead>
              <tr>
                <th>Subject ID</th>
                <th>Subject Name</th>
                <th>Teacher ID</th>
                <th>Class ID</th>
                <th>Year</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="schedule-table-body">
            <tr><td colspan="7" class="text-center text-muted">Please enter a Teacher ID or Subject Name to view classes.</td></tr>
            </tbody>
          </table>
      </div>
    </div>
  </div>
</body>

 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener("DOMContentLoaded", function () {
  const tbody = document.getElementById("schedule-table-body");
  tbody.innerHTML = "<tr><td colspan='7' class='text-center text-muted'>Loading your schedule...</td></tr>";
  loadTeacherSchedule(); // auto load related data of teacher_id
});

document.getElementById("search-category").addEventListener("change", function () {
  const category = this.value;
  const searchInput = document.getElementById("search");

  if (category === "teacher_id") {
    searchInput.placeholder = "Search with Teacher ID";
  } else if (category === "subject_name") {
    searchInput.placeholder = "Search with Subject Name";
  }
});

document.getElementById("search-btn").addEventListener("click", function () {
  const keyword = document.getElementById("search").value.trim();
  const category = document.getElementById("search-category").value;

  if (!keyword) {
    alert("Please enter a valid search keyword.");
    return;
  }

  fetch("teacher_schedule.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `category=${encodeURIComponent(category)}&keyword=${encodeURIComponent(keyword)}`
  })
    .then((res) => {
      if (!res.ok) throw new Error("Network response was not ok");
      return res.json();
    })
    .then(renderSchedule)
    .catch((err) => {
      console.error("Error loading schedule:", err);
      const tbody = document.getElementById("schedule-table-body");
      tbody.innerHTML = "<tr><td colspan='7'>Error loading schedule.</td></tr>";
    });
});

function loadTeacherSchedule() {
  fetch("teacher_schedule.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" }
    // use session teacher_id return all classes
  })
    .then((res) => {
      if (!res.ok) throw new Error("Network response was not ok");
      return res.json();
    })
    .then(renderSchedule)
    .catch((err) => {
      console.error("Error loading schedule:", err);
      const tbody = document.getElementById("schedule-table-body");
      tbody.innerHTML = "<tr><td colspan='7'>Error loading schedule.</td></tr>";
    });
}

function renderSchedule(data) {
  const tbody = document.getElementById("schedule-table-body");
  tbody.innerHTML = "";

  if (data.error) {
    tbody.innerHTML = `<tr><td colspan='7'>${data.error}</td></tr>`;
  } else if (data.length === 0) {
    tbody.innerHTML = "<tr><td colspan='7'>No classes found.</td></tr>";
  } else {
    const currentMonth = new Date().getMonth();
    const monthMap = {
      "January": 0, "February": 1, "March": 2, "April": 3,
      "May": 4, "June": 5, "July": 6, "August": 7,
      "September": 8, "October": 9, "November": 10, "December": 11
    };

    data.forEach((row) => {
      let status = "Ongoing";
      let statusClass = "text-success";

      if (row.part_duration) {
        const [start, end] = row.part_duration.split(" - ").map(s => s.trim());
        const startMonth = monthMap[start];
        const endMonth = monthMap[end];

        if (startMonth !== undefined && endMonth !== undefined) {
          if (currentMonth < startMonth) {
            status = "Not Started";
            statusClass = "text-secondary";
          } else if (currentMonth > endMonth) {
            status = "Complete";
            statusClass = "text-danger";
          }
        }
      }

      tbody.innerHTML += `
        <tr>
          <td>${row.subject_id}</td>
          <td>${row.subject_name}</td>
          <td>${row.teacher_id}</td>
          <td>${row.class_id}</td>
          <td>${row.year}</td>
          <td>${row.time}</td>
          <td class="${statusClass}">${status}</td>
        </tr>
      `;
    });
  }
}
</script>


   
      
</body>
</html>