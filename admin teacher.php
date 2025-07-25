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
           <li class="nav-item">
              <a class="nav-link" href="teacher_notification.php" id="notificationBell">
                  <i class="fas fa-bell"></i>
                  <span class="notification-badge" id="notificationBadge"></span>
              </a>
          </li>
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
      <div class="row w-100 align-items-center">
      
        <div class="col-md-9 d-flex gap-2 flex-wrap align-items-center">
          <select id="search-category" class="form-select search-category">
            <option value="class_term">Term</option>
            <option value="subject_name">Subject Name</option>
          </select>
          <input class="form-control" type="search" placeholder="Search with Term" id="search" />
          <button class="btn btn-outline-success" type="button" id="search-btn">Search</button>
        </div>

        
        <div class="col-md-3 text-md-end mt-2 mt-md-0">
          <button type="button" class="btn btn-primary" id="view-timetable-btn">View Ongoing Timetable</button>
        </div>
      </div>
    </div>
     

      <div class="card-body">
        <table class="table table-striped">
            <thead>
              <tr>
                <th>Subject ID</th>
                <th>Subject Name</th>
                <th>Term</th>
                <th>Class ID</th>
                <th>Year</th>
                <th>Class Venue</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="schedule-table-body">
              <!-- rows will be injected here -->
            </tbody>
          </table>
      </div>
    </div>
  </div>


  <!-- Timetable Modal -->
<div class="modal fade" id="timetableModal" tabindex="-1" aria-labelledby="timetableModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> 
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="timetableModalLabel">Ongoing Timetable</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="timetable-modal-body">
        <!-- Timetable will be injected here -->
      </div>
    </div>
  </div>
</div>

</body>


 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });

window.addEventListener("DOMContentLoaded", function () {
  const tbody = document.getElementById("schedule-table-body");
  tbody.innerHTML = "<tr><td colspan='7' class='text-center text-muted'>Loading your schedule...</td></tr>";
  loadTeacherSchedule(); // auto load related data of teacher_id
});

document.getElementById("search-category").addEventListener("change", function () {
  const category = this.value;
  const searchInput = document.getElementById("search");

  if (category === "class_term") {
    searchInput.placeholder = "Search with Term";
  } else if (category === "subject_name") {
    searchInput.placeholder = "Search with Subject Name";
  }
});

document.getElementById("search-btn").addEventListener("click", function () {
  const keyword = document.getElementById("search").value.trim();
  const category = document.getElementById("search-category").value;

  if (!keyword) {
    
    return;
  }

  // search request include teacher_id
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
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  
    body: "" 
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

//Calculates class status based on current date and classes duration
function renderSchedule(data) {
  const tbody = document.getElementById("schedule-table-body");
  tbody.innerHTML = "";

  if (data.error) {
    tbody.innerHTML = `<tr><td colspan='7'>${data.error}</td></tr>`;
    return;
  }

  if (data.length === 0) {
    tbody.innerHTML = "<tr><td colspan='7'>No classes found.</td></tr>";
    return;
  }

  const monthMap = {
    "January": 0, "February": 1, "March": 2, "April": 3,
    "May": 4, "June": 5, "July": 6, "August": 7,
    "September": 8, "October": 9, "November": 10, "December": 11
  };

  const currentDate = new Date();

  data.forEach((row) => {
    let status = "Ongoing";
    let statusClass = "text-success";

    if (row.part_duration && row.class_term) {
      const [start, end] = row.part_duration.split(" - ").map(s =>
        s.trim().charAt(0).toUpperCase() + s.trim().slice(1).toLowerCase()
      );
      const startMonth = monthMap[start];
      const endMonth = monthMap[end];
      const classYear = parseInt(row.class_term);

      if (startMonth !== undefined && endMonth !== undefined && !isNaN(classYear)) {
        const startDate = new Date(classYear, startMonth, 1);
        const endDate = new Date(classYear, endMonth + 1, 0);

        if (currentDate < startDate) {
          status = "Not Started";
          statusClass = "text-secondary";
        } else if (currentDate > endDate) {
          status = "Complete";
          statusClass = "text-danger";
        }
      }
    }

    tbody.innerHTML += `
      <tr>
        <td>${row.subject_id}</td>
        <td>${row.subject_name}</td>
        <td>${row.class_term}</td>
        <td>${row.class_id}</td>
        <td>${row.year}</td>
        <td>${row.class_venue}</td>
        <td>${row.time}</td>
        <td class="${statusClass}">${status}</td>
      </tr>
    `;
  });
}


document.getElementById("view-timetable-btn").addEventListener("click", function () {
  const allRows = document.querySelectorAll("#schedule-table-body tr");
  const timetableData = [];

  allRows.forEach(row => {
    const status = row.cells[7].textContent.trim();  
    if (status === "Ongoing") {
      const subject_id = row.cells[0].textContent.trim();
      const subject_name = row.cells[1].textContent.trim();
      const class_id = row.cells[3].textContent.trim();
      const class_venue = row.cells[5].textContent.trim();
      const time = row.cells[6].textContent.trim();

      const subject = `${subject_id} - ${subject_name} (${class_id})<br><small class="text-muted">Venue: ${class_venue}</small>`;
      timetableData.push({ subject, time });
    }
  });

  renderTimetableModal(timetableData);

  const timetableModal = new bootstrap.Modal(document.getElementById('timetableModal'));
  timetableModal.show();
});

function renderTimetableModal(data) {
  const container = document.getElementById("timetable-modal-body");
  container.innerHTML = ""; // clear previous

  if (data.length === 0) {
    container.innerHTML = "<div class='text-muted'>No Ongoing schedule found.</div>";
    return;
  }

  const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
  const timeSlots = [];
  const scheduleMap = {};

  data.forEach(({ subject, time }) => {
    const [dayPart, ...timeParts] = time.split(" ");
    const timePart = timeParts.join(" ");
    if (!scheduleMap[dayPart]) scheduleMap[dayPart] = {};
    scheduleMap[dayPart][timePart] = subject;
    if (!timeSlots.includes(timePart)) timeSlots.push(timePart);
  });

  timeSlots.sort();

  let html = `
    <table class='table table-bordered'>
      <thead>
        <tr>
          <th>Time</th>
          ${days.map(day => `<th>${day}</th>`).join('')}
        </tr>
      </thead>
      <tbody>`;

  timeSlots.forEach(slot => {
    html += `<tr><td>${slot}</td>`;
    days.forEach(day => {
      const subject = scheduleMap[day]?.[slot] || "";
      html += `<td class="align-middle">${subject}</td>`;
    });
    html += "</tr>";
  });

  html += "</tbody></table>";
  container.innerHTML = html;
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
            checkUnreadNotifications();
            
            // every 30 seconds check for unread notifications
            setInterval(checkUnreadNotifications, 30000);
        });


</script>


   
      
</body>
</html>