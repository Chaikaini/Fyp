<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Teacher</title>

    <!-- Favicon -->
    <link href="img/the seeds.jpg" rel="icon" type="image/png">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

    .toast-message {
    position: fixed;  
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 5px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    font-size: 16px;
    font-weight: bold;
    z-index: 9999; 
    text-align: center;
    transition: all 0.3s ease-in-out;
    display: none;
    min-width: 250px;
}

    .toast-message.error {
        background-color: #dc3545; 
    }
    .comment-btn {
        background: transparent;
        border: none;
        padding: 0;
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
.avatar-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.avatar-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    background-color: #f8f9fa;
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

  <!-- Attendance -->
<h1 class="mb-4">Classes</h1>
<div class="card">
  <div class="card-header">
    <form class="d-flex ms-auto">
      <select id="search-category" class="form-select me-2" style="max-width: 180px;">
        <option value="subject_id">Subject ID</option>
        <option value="subject_name">Subject Name</option>
      </select>
      <input class="form-control me-2" type="search" placeholder="Search with Subject ID" id="search" />
      <button class="btn btn-outline-success" type="button" id="search-btn">Search</button>
    </form>
  </div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Subject ID</th>
          <th>Subject Name</th>
          <th>Class ID</th>
          <th>Year</th>
          <th>Part</th>
          <th>Time</th>
          <th>Class Capacity</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="schedule-table-body">
            <tr><td colspan="7" class="text-center text-muted">Please enter a Subject ID or Subject Name to display classes.</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">Student List For <span id="modal-class-id"></span></h5>
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Student Name</th>
              <th>Gender</th>
              <th>School</th>
              <th>Parent Name</th>
              <th>Relationship</th>
              <th>Phone Number</th>
              <th>Teacher Comment</th>

            </tr>
          </thead>
          <tbody id="student-modal-body">
          </tbody>
        </table>
        <div class="modal-footer d-flex justify-content-end">
        <button class="btn btn-success" onclick="exportStudentTableToExcel()">Export to Excel</button>
       </div>
      </div>
    </div>
  </div>
</div>

<!-- Parent Info Modal -->
<div class="modal fade" id="parentInfoModal" tabindex="-1" aria-labelledby="parentInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="parentInfoModalLabel">Parent Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
                <div class="avatar-container">
                    <div class="avatar-circle">
                        <img id="parentImage" src="" alt="Parent Image" class="avatar-img">
                    </div>
                </div>
       </div>
      <div class="modal-body" id="parent-info-body">
        <!-- filled by JS -->
      </div>
    </div>
  </div>
</div>

<!-- Child Image Modal -->
<div class="modal fade" id="childImageModal" tabindex="-1" aria-labelledby="childImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="childImageModalLabel">Child Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-container">
                    <div class="avatar-circle">
                        <img id="childImage" src="" alt="Child Image" class="avatar-img">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!--- toast notification-->
    <div id="toastContainer" style="position: relative;"></div>


      <div class="modal-header">
        <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="commentText">Comment</label>
          <textarea id="commentText" class="form-control" rows="4"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="submitComment()">Save Comment</button>
      </div>
    </div>
  </div>
</div>



<!-- Modal Structure -->
<div class="modal fade" id="examResultModal" tabindex="-1" aria-labelledby="examResultModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">

    <!-- Toast Message Container -->
   <div id="successToast" class="toast-message" style="display: none;"></div>

      <div class="modal-header">
        <h5 class="modal-title" id="examResultModalLabel">Exam Results</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="studentResultsTable" class="table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Student Name</th>
              <th>Midterm result</th>
              <th>Final result</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        
        <button class="btn btn-primary" onclick="saveExamResults()">Save</button>
        
        <button class="btn btn-success" onclick="exportExamResultsToExcel()">Export to Excel</button>
      </div>
    </div>
  </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('collapsed');
    });



window.addEventListener("DOMContentLoaded", function () {
  const tbody = document.getElementById("schedule-table-body");
  tbody.innerHTML = "<tr><td colspan='8' class='text-center text-muted'>Loading your subjects...</td></tr>";

  // Load teacher's data automatically on page load
  loadTeacherData();
});

document.getElementById("search-btn").addEventListener("click", function () {
  const category = document.getElementById("search-category").value;
  const keyword = document.getElementById("search").value.trim();

  if (!keyword) {
    alert("Please enter a valid search value.");
    return;
  }

  const bodyData = new URLSearchParams();
  bodyData.append(category, keyword);

  fetch("teacher_classes_info.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: bodyData.toString(),
  })
  .then(res => res.json())
  .then(data => {
    populateTable(data);
  })
  .catch(error => {
    console.error("Error loading attendance:", error);
    document.getElementById("schedule-table-body").innerHTML = "<tr><td colspan='8'>Error loading data.</td></tr>";
  });
});

// function to load teacher's data
function loadTeacherData() {
  fetch("teacher_classes_info.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" }
  })
  .then(res => res.json())
  .then(data => {
    populateTable(data);
  })
  .catch(error => {
    console.error("Error loading attendance:", error);
    document.getElementById("schedule-table-body").innerHTML = "<tr><td colspan='8'>Error loading data.</td></tr>";
  });
}


function populateTable(data) {
  const tbody = document.getElementById("schedule-table-body");
  tbody.innerHTML = "";

  if (data.error) {
    tbody.innerHTML = `<tr><td colspan='8'>${data.error}</td></tr>`;
  } else if (data.length === 0) {
    tbody.innerHTML = "<tr><td colspan='8'>No subject found.</td></tr>";
  } else {
    data.forEach(row => {
      tbody.innerHTML += `
        <tr>
          <td>${row.subject_id}</td>
          <td>${row.subject_name}</td>
          <td>${row.class_id}</td>
          <td>${row.year}</td>
          <td>${row.part}</td>
          <td>${row.time}</td>
          <td>${row.capacity}</td>
          <td>
            <button class="btn btn-primary" onclick='viewStudents("${row.class_id}")'>View List</button>
            <button class="btn btn-primary" onclick='openExamResultModal("${row.class_id}")'>Exam Result</button>
          </td>
        </tr>
      `;
    });
  }
}

   
document.getElementById("search-btn").addEventListener("click", function () {
  const category = document.getElementById("search-category").value;
  const keyword = document.getElementById("search").value.trim();

  if (!keyword) {
    alert("Please enter a valid search value.");
    return;
  }

  const bodyData = new URLSearchParams();
  bodyData.append(category, keyword);

  fetch("teacher_classes_info.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: bodyData.toString(),
  })
  .then(res => res.json())
  .then(data => {
    populateTable(data);
  })
  .catch(error => {
    console.error("Error loading attendance:", error);
    document.getElementById("schedule-table-body").innerHTML = "<tr><td colspan='8'>Error loading data.</td></tr>";
  });
});







function viewStudents(classId) {
    const viewButton = document.querySelector(`button[onclick='viewStudents("${classId}")']`);
    viewButton.disabled = true;

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

        document.getElementById("studentModal").setAttribute("data-class-id", classId);
        document.getElementById("studentModalLabel").innerHTML = `Student List For ${classId} (${data.subject_name || "Unknown Subject"})`;

        if (data.error) {
            tbody.innerHTML = `<tr><td colspan='7'>${data.error}</td></tr>`;
        } else if (data.students.length === 0) {
            tbody.innerHTML = `<tr><td colspan='7' class="text-center">No student enrolled yet</td></tr>`;
        } else {
            data.students.forEach((row, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <a href="#" onclick="showChildImageModal('${row.child_id}', 
                                '${row.child_name}', 
                                '${row.child_image ? encodeURIComponent(row.child_image) : ''}')">
                                ${row.child_name}
                            </a>
                        </td>
                        <td>${row.child_gender}</td>
                        <td>${row.child_school}</td>
                        <td><a href="#" onclick="viewParentInfo('${row.parent_id}', '${row.child_name}')">${row.parent_name}</a></td>
                        <td>${row.parent_relationship}</td>
                        <td>${row.phone_number}</td>
                        <td>
                            <button class="btn btn-sm comment-btn" data-child-id="${row.child_id}" data-class-id="${classId}" data-child-name="${row.child_name}">
                                <i class="fas fa-comment"></i>
                            </button>

                        </td>
                    </tr>
                `;
            });

        
            const commentButtons = document.querySelectorAll(".comment-btn");
            commentButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const childId = this.getAttribute("data-child-id");
                    const classId = this.getAttribute("data-class-id");
                    const childName = this.getAttribute("data-child-name");
                    openCommentModal(childId, classId, childName);
                });
            });
        }
    })
    .catch(err => {
        console.error("Error fetching students:", err);
        document.getElementById("student-modal-body").innerHTML = "<tr><td colspan='7'>Error loading students.</td></tr>";
    })
    .finally(() => {
        viewButton.disabled = false;
    });
}

// Function to show child image in a modal
function showChildImageModal(childId, childName, childImage) {
    const modal = new bootstrap.Modal(document.getElementById("childImageModal"));
    const modalTitle = document.getElementById("childImageModalLabel");
    const modalImage = document.getElementById("childImage");
    const avatarContainer = modalImage.parentElement;

    modalTitle.innerHTML = `Image for ${childName}`;

    if (childImage && childImage !== "null" && childImage !== "") {
        // if image exists, show it
        avatarContainer.style.display = 'block';
        modalImage.style.display = 'block';
        modalImage.src = decodeURIComponent(childImage);
        modalImage.alt = `${childName}'s Image`;
        
        modalImage.onerror = function() {
            console.error('Failed to load image:', childImage);
            // load default avatar if image fails
            modalImage.src = 'img/user.jpg';
            modalImage.alt = `Default avatar for ${childName}`;
        };
    } else {
        // if no image, show default avatar
        avatarContainer.style.display = 'block';
        modalImage.style.display = 'block';
        modalImage.src = 'img/user.jpg';
        modalImage.alt = `Default avatar for ${childName}`;
    }

    modal.show();
}

// CommentModal
function openCommentModal(childId, classId, childName) {
    console.log("Opening comment modal for:", childId, classId, childName);

    currentClassId = classId;

    fetchChildId(classId, childName, function(fetchedChildId) {
        currentChildId = fetchedChildId;

        if (!currentChildId) {
            showToast("Failed to load child ID. Please try again.");
            return;
        }

        document.getElementById("commentModalLabel").innerText = `Add Comment for ${childName}`;

        //after get childId, go to fetch the existing comment
        loadExistingComment(currentChildId, currentClassId);

        // then open modal
        const commentModal = new bootstrap.Modal(document.getElementById("commentModal"));
        commentModal.show();
    });
}

function fetchChildId(classId, childName, callback) {
    if (!classId || !childName) {
        console.error("classId or childName is missing.");
        callback(null);
        return;
    }

    const bodyData = new URLSearchParams();
    bodyData.append("class_id", classId);
    bodyData.append("child_name", childName);

    fetch("teacher_getchild_id.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: bodyData.toString(),
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error(data.error);
            callback(null);
        } else {
            console.log("Child ID:", data.child_id);
            callback(data.child_id);
        }
    })
    .catch(error => {
        console.error("Error fetching child ID:", error);
        callback(null);
    });
}

// use fetch the existing comment
function loadExistingComment(childId, classId) {
    const bodyData = new URLSearchParams();
    bodyData.append("child_id", childId);
    bodyData.append("class_id", classId);

    fetch("teacher_save_comment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: bodyData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.comment_text !== undefined) {
            document.getElementById("commentText").value = data.comment_text;
        } else {
            document.getElementById("commentText").value = "";
        }
    })
    .catch(error => {
        console.error("Error fetching existing comment:", error);
        document.getElementById("commentText").value = "";
    });
}

function submitComment() {
    const commentText = document.getElementById("commentText").value.trim();

    if (!commentText) {
        showToast("Please enter a comment.", true); 
        return;
    }

    if (!currentChildId) {
        showToast("Child ID not loaded yet. Please try again.", true); 
        return;
    }

    const bodyData = new URLSearchParams();
    bodyData.append("child_id", currentChildId);
    bodyData.append("class_id", currentClassId);
    bodyData.append("comment_text", commentText);

    fetch("teacher_save_comment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: bodyData.toString()
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast(result.success, false);
            
            // close modal
            setTimeout(() => {
                const commentModal = bootstrap.Modal.getInstance(document.getElementById("commentModal"));
                if (commentModal) {
                    commentModal.hide();
                }
            }, 1000); 
        } else if (result.error) {
            showToast(result.error, true);
        }
    })
    .catch(error => {
        console.error("Error saving comment:", error);
        showToast("Error saving comment.", true);
    });
}







function viewParentInfo(parentId, childName) {
    fetch("teacher_parentinfo.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "parent_id=" + encodeURIComponent(parentId),
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            const modalTitle = document.getElementById("parentInfoModalLabel");
            const parentImage = document.getElementById("parentImage");
            
            modalTitle.innerText = `Parent Information for ${childName}`;

            // process parent image
            if (data.parent_image && data.parent_image !== "null" && data.parent_image !== "") {
                parentImage.src = decodeURIComponent(data.parent_image);
                parentImage.alt = `${data.parent_name}'s Image`;
                
                parentImage.onerror = function() {
                    console.error('Failed to load parent image:', data.parent_image);
                    parentImage.src = 'img/user.jpg';
                    parentImage.alt = `Default avatar for ${data.parent_name}`;
                };
            } else {
                parentImage.src = 'img/user.jpg';
                parentImage.alt = `Default avatar for ${data.parent_name}`;
            }

            // parent info table
            let content = `
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <td>${data.parent_name}</td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td>${data.parent_gender}</td>
                        </tr>
                        <tr>
                            <th>Relationship</th>
                            <td>${data.parent_relationship}</td>
                        </tr>
                        <tr>
                            <th>IC Number</th>
                            <td>${data.ic_number}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>${data.parent_email}</td>
                        </tr>
                        <tr>
                            <th>Phone 1</th>
                            <td>${data.phone_number}</td>
                        </tr>
                        <tr>
                            <th>Phone 2</th>
                            <td>${data.phone_number2}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>${data.parent_address}</td>
                        </tr>
                        <tr>
                            <th>Parent 2</th>
                            <td>${data.parent_name2} (${data.parent_relationship2}) - ${data.parent_num2}</td>
                        </tr>
                    </tbody>
                </table>
            `;

            document.getElementById("parent-info-body").innerHTML = content;
            const modal = new bootstrap.Modal(document.getElementById("parentInfoModal"));
            modal.show();
        }
    })
    .catch(err => {
        console.error("Error fetching parent info:", err);
        alert("Error loading parent information.");
    });
}

function exportStudentTableToExcel() {
  const table = document.querySelector("#studentModal .table");
  const classId = document.getElementById("studentModal").getAttribute("data-class-id") || "Unknown";

  const workbook = XLSX.utils.book_new();
  const worksheet = XLSX.utils.table_to_sheet(table);
  XLSX.utils.book_append_sheet(workbook, worksheet, "Students");

  const filename = `Student_List_${classId}.xlsx`;
  XLSX.writeFile(workbook, filename);
}



// Toast Notification Function
function showToast(message, isError = false) {
  
    const toast = document.createElement("div");
    toast.className = "toast-message";
    if (isError) {
        toast.classList.add("error");
    }
    toast.textContent = message;

    
    document.body.appendChild(toast);

    
    setTimeout(() => {
        toast.style.display = "block";
        toast.style.opacity = "1";
    }, 100);

  
    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}






// Function to save exam results
function saveExamResults() {
    const rows = document.querySelectorAll("#studentResultsTable tbody tr");
    const results = [];
    const classId = window.currentClassId;

    rows.forEach(row => {
        const childId = row.querySelector("input[data-child-id]").dataset.childId;
        const midterm = row.querySelector("input[data-exam='midterm']").value;
        const final = row.querySelector("input[data-exam='final']").value;

        results.push({
            child_id: childId,
            exam_result_midterm: parseFloat(midterm),
            exam_result_final: parseFloat(final)
        });
    });

    fetch("save_exam_results.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ class_id: classId, results: results })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast("Exam results saved successfully!");
        } else {
            showToast("Error: " + (data.error || "Failed to save results."), true);
        }
    })
    .catch(err => {
        console.error("Save error:", err);
        showToast("Failed to save results.", true);
    });
}

// Function to open the modal and load student data
function openExamResultModal(classId) {
  fetch(`teacher_exam_students.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "class_id=" + encodeURIComponent(classId)
  })
    .then(response => response.json())
    .then(data => {
      const tbody = document.querySelector("#studentResultsTable tbody");
      tbody.innerHTML = "";
      let index = 1;
    data.forEach(student => {
      tbody.innerHTML += `
        <tr>
          <td>${index++}</td>
          <td>${student.child_name}</td>
          <td><input type="number" value="${student.exam_result_midterm ?? ''}" data-exam="midterm" data-child-id="${student.child_id}"></td>
          <td><input type="number" value="${student.exam_result_final ?? ''}" data-exam="final" data-child-id="${student.child_id}"></td>
        </tr>
      `;
      });

      // Update modal title to include the class_id
      const modalTitle = document.getElementById("examResultModalLabel");
      modalTitle.textContent = `Exam Results for ${classId}`;

      // Store classId in a global variable or in the modal
      window.currentClassId = classId;

      const modal = new bootstrap.Modal(document.getElementById("examResultModal"));
      modal.show();
    })
    .catch(error => console.error("Error loading student data:", error));
}





// Function to export the student results table to Excel
function exportExamResultsToExcel() {
  const table = document.querySelector("#studentResultsTable");
  const classId = window.currentClassId || "Unknown";  // Get the current class ID from the global variable

  // Get all rows in the table
  const rows = table.querySelectorAll("tbody tr");

  // Loop through the rows and update the input fields with their values
  rows.forEach(row => {
    const midtermInput = row.querySelector("input[data-exam='midterm']");
    const finalInput = row.querySelector("input[data-exam='final']");

    // Replace input fields with their values
    midtermInput.parentNode.textContent = midtermInput.value;
    finalInput.parentNode.textContent = finalInput.value;
  });

 
  const workbook = XLSX.utils.book_new();
  const worksheet = XLSX.utils.table_to_sheet(table);
  XLSX.utils.book_append_sheet(workbook, worksheet, "Exam Results");

  // filename with the class ID
  const filename = `Exam_Results_Class_${classId}.xlsx`;
  XLSX.writeFile(workbook, filename);

  
  rows.forEach(row => {
    const midtermInput = row.querySelector("input[data-exam='midterm']");
    const finalInput = row.querySelector("input[data-exam='final']");

    
    midtermInput.parentNode.innerHTML = `<input type="number" value="${midtermInput.value}" data-exam="midterm" data-child-id="${midtermInput.dataset.childId}">`;
    finalInput.parentNode.innerHTML = `<input type="number" value="${finalInput.value}" data-exam="final" data-child-id="${finalInput.dataset.childId}">`;
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
            checkUnreadNotifications();
            
            // every 30 seconds check for unread notifications
            setInterval(checkUnreadNotifications, 30000);
        });


</script>

    

   
      
</body>
</html>