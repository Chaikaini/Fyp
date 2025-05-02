<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Profile</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre,profile" name="keywords">
    <meta content="The Seeds Learning Centre | Profile" name="description">

    <!-- Favicon -->
    <link href="img/the seeds.jpg" rel="icon" type="image/png">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate.min.css" rel="stylesheet">
    <link href="lib/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
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

        .profile-container {
            max-width: 900px;
            height: auto;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-top: 50px;
            display: flex;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
            width: 100%;
        }
        .profile-options {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-right: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            height: 300px;
        }
        .profile-options a {
            text-decoration: none;
            color: #000;
            font-size: 18px;
            padding: 10px;
            width: 100%;
            transition: color 0.3s ease;
        }
        .profile-options a:hover, .profile-options a.active {
            color: #007bff;
            text-decoration: underline;
        }
        .profile-content {
            flex: 3;
            display: none;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
        }
        .profile-content.active {
            display: block;
        }
        input[type=submit]:hover {
            background-color: rgb(71, 145, 69);
            color: rgb(12, 12, 12); 
        }
        .form-container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-top: 50px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group h3 {
            margin-top: 30px;
        }
        
        .form-group button {
            background-color:#15ca39;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #14a631;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 95%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 30%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
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
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
            
            
        }
        
        .button-container {
        text-align: right;
        margin-top: 10px;
    }

        .form-select {
        width: 150px; 
    }

    .status-circle {
        height: 15px;
        width: 15px;
        background-color: rgb(27, 217, 87);
        border-radius: 50%;
        display: inline-block;
    }
    .avatar-section {
       text-align: center;
       margin-bottom: 20px;
       margin-top: 30px;
   }

    .avatar-container {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #ccc;
        display: inline-block;
    }

    .avatar-container img {
       width: 100%;
       height: 100%;
       object-fit: cover;
    }

    #avatar-upload {
    display: none;
     }

     .required {
    color: red;
    }

    textarea {
    width: 100%;
    height: 100px;
    padding: 8px;
    box-sizing: border-box;
    }

    .pointer-cursor {
    cursor:pointer !important;
           
    }

    button.btn.btn-primaryy {
    width: 100%;
    padding: 10px;
    background-color:#15ca39;
    color: white;
    border: none;
    cursor: pointer;
}

    button.btn.btn-primaryy:hover {
    background-color:#14a631;
}

    .error-message {
        color: red;
        font-size: 0.875em;
        margin-left: 10px;
    }

    .success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    font-weight: bold;
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
    font-weight: bold;
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
.btn-d {
    padding: 10px 15px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}
.btn-secondary {
    background-color: #6c757d;
    color: white;
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
.notification-icon {
    position: relative; 
}


    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
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
                <a href="contsct.html" class="nav-item nav-link">Contact us</a>
                <a href="comment.html" class="nav-item nav-link">Comment</a>
            </div>
            <a href="login.html" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Log out<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->

    <!-- Icon Bar Start -->
    <div class="icon-bar">
        <a href="notification.php" class="notification-icon">
            <i class="fas fa-bell"></i>
            <span class="notification-badge" style="display: none;"></span>
        </a>
        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>
    <!-- Icon Bar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">My Profile</h1>
                    <nav aria-label="breadcrumb">
                        
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <div class="profile-container">
        <div class="profile-options">
            <a href="#" id="my-info-tab" class="active">My Information</a>
            <a href="#" id="children-info-tab">Childrens Information</a>
            <a href="#" id="history-tab">Payment History</a>
        </div>
    
        <div class="profile-content active" id="my-info-content">
            <h3>My Information</h3>

            <div class="avatar-section">
                <label for="avatar-upload">
                    <div class="avatar-container">
                        <img src="img/user.jpg" alt="User Avatar" id="user-avatar">
                    </div>
                </label>
                <input type="file" id="avatar-upload" accept="image/*" style="display: none;">
            </div>
            
            <form>
            <div class="form-group">
        <label for="username"> Name</label>
        <input type="text" id="username" name="parent_name">
    </div>

    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="parent_gender">
            <option value="" disabled selected>Select your gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
    </div>

    <div class="form-group">
        <label for="ic-num">IC Number</label>
        <input type="text" id="ic-num" name="ic_number" placeholder="000000-00-0000">
    </div>

    <div class="form-group">
        <label for="email">Email <span class="required">*</span></label>
        <input type="email" id="email" name="parent_email" readonly>
    </div>

    <div class="form-group">
        <label for="phone-num-1">Phone Number</label>
        <input type="text" id="phone-num-1" name="phone_number">
    </div>

    <div class="form-group">
        <label for="phone-num-2">Emergency Phone Number</label>
        <input type="text" id="phone-num-2" name="phone_number2">
    </div>

    <div class="form-group">
        <label for="relationship">Relationship</label>
        <select id="relationship" name="parent_relationship">
            <option value="" disabled selected>Select Relationship With Children</option>
            <option value="Mother">Mother</option>
            <option value="Father">Father</option>
            <option value="Guardian">Guardian</option>
        </select>
    </div>

    <div class="form-group">
        <label for="address">Address</label>
        <textarea id="address" name="parent_address"></textarea>
    </div>

    <!-- Add Another Contact Button -->
<div class="form-group">
    <button type="button" id="add-contact-btn">Second Contact</button>
</div>

<!-- Additional Contact Section (Initially Hidden) -->
<div id="additional-contact" style="display: none; margin-top: 15px;">
    <div class="form-group">
        <label for="contact-name">Contact Name</label>
        <input type="text" id="contact-name" name="additional_contact_name">
    </div>
    <div class="form-group">
        <label for="contact-relationship">Relationship</label>
        <select id="contact-relationship" name="additional_contact_relationship">
            <option value="" disabled selected>Select Relationship With Children</option>
            <option value="Mother">Mother</option>
            <option value="Father">Father</option>
            <option value="Guardian">Guardian</option>
        </select>
    </div>
    <div class="form-group">
        <label for="contact-phone">Contact Phone Number</label>
        <input type="text" id="contact-phone" name="additional_contact_phone">
    </div>
</div>
                <h3>Reset Password</h3>
                
                <div class="form-group">
                    <label for="current-password">Current Password</label>
                    <input type="password" id="current-password" autocomplete="new-password" placeholder="Enter current password">
                    <span id="current-password-error" class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <input type="password" id="new-password" placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                   <input type="password" id="confirm-password" placeholder="Confirm new password">
                   <span id="new-password-error" class="error-message"></span>
                </div>

                <div class="form-group">
                    <button type="submit">Save Changes</button>
                </div>
            </form>
            <div id="success-message" class="success-message" style="display: none;"></div>
        </div>
    
    <div class="profile-content" id="children-info-content">
    <h3>Childrens Information</h3>
    <table class="table table-striped">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>My kid number</th>
                <th>Birthday</th>
                <th>School</th>
                <th>Year</th>
                <th>Actions</th>
                </tr>
            </thead>   
        <tbody>
            
        </tbody>
    </table>
    </table>

    <div class="button-container">
        <button class="btn btn-primary" id="addChildBtn">Add Child</button>
        
    </div>
    <div id="successToast" class="toast-message" style="display: none;">
    Children Information deleted successfully!
</div>
    <div class="select-child mt-3">
        <label for="childSelect" class="form-label">Select to display child registered classes:</label>
        <select id="childSelect" class="form-select" onchange="displayLearningStatus()">
            <option value="">--Select--</option>
            
        </select>
    </div>

    <div id="learningStatus" class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">Registered Classes</h4>
            <p id="statusContent" class="card-text"></p>
        </div>
    </div>
    </div> 

    
        <div class="profile-content" id="history-content">
            <h3>Payment History</h3>
            <p>Here you can view your payment history for registration class.</p>
            <table class="table table-striped">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                <th>Child Name</th>
                <th>Subject Name</th>
                <th>Part</th>
                <th>Payment Method</th>
                <th>Total Amount</th>
                <th>Payment Date</th>
                
                </tr>
            </thead>   
        <tbody>
            
        </tbody>
    </table>
    </table>
        </div>
    </div>

   <!-- Add Child Modal -->
<div id="addChildModal" class="modal">
    <div class="modal-content pointer-cursor">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Add Child Information</h3>

        <div class="avatar-section">
            <label for="avatar-upload">
                <div class="avatar-container">
                    <img src="img/user.jpg" alt="User Avatar" id="user-avatar">
                </div>
            </label>
            <input type="file" id="avatar-upload" accept="image/*">
        </div>

        <form id="addChildForm" method="post" action="profile_addchild.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="child_name">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="child_gender">
                    <option value="" disabled selected>Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>   
                </select>
            </div>
            <div class="form-group">
            <label for="kidNumber">My Kid Number</label>
            <input type="text" id="kidNumber" name="child_kidNumber" placeholder="000000-00-0000">
        </div>
        <div class="form-group">
            <label for="birthday">Birthday</label>
            <input type="date" id="birthday" name="child_birthday" readonly>
        </div>
            <div class="form-group">
                <label for="school">School</label>
                <input type="text" id="school" name="child_school">
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <select id="year" name="child_year">
                    <option value="" disabled selected>Year</option>
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>   
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
        <div id="successToast" class="toast">Add child information successfully!</div>
    </div>
</div>
   
   <!-- Child Edit Modal -->
<div id="childFormModal" class="modal">
    <div class="modal-content pointer-cursor">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Child Information</h3>

        <div class="avatar-section">
            <label for="avatar-upload">
                <div class="avatar-container">
                    <img src="img/user.jpg" alt="User Avatar" id="user-avatar">
                </div>
            </label>
            <input type="file" id="avatar-upload" accept="image/*">
        </div>

        <form id="childForm" method="post" action="profile_editchild.php">
        <input type="hidden" name="child_id" id="childId">
           

            <div class="form-group">
                <label for="childName">Child Name</label>
                <input type="text" id="childName" name="child_name" required>
            </div>
            <div class="form-group">
                <label for="childGender">Gender</label>
                <select id="childGender" name="child_gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editkidNumber">My kid number</label>
                <input type="text" id="editkidNumber" name="child_kidNumber" required>
            </div>
            <div class="form-group">
                <label for="childBirthday">Birthday</label>
                <input type="date" id="childBirthday" name="child_birthday" required>
            </div>
            <div class="form-group">
                <label for="childSchool">School</label>
                <input type="text" id="childSchool" name="child_school" required>
            </div>
            <div class="form-group">
                <label for="childYear">Year</label>
                <select id="childYear" name="child_year">
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                
            </div>
            <div id="successToast" class="toast">Child information updated successfully!</div>
        </form>
        
        
    </div>
</div>

    <!-- Child Delete Modal -->
<div id="deleteConfirmModal" class="modal-d">
    <div class="modal-dcontent">
        <h4>Confirm Deletion</h4>
        <p>Are you sure you want to delete this child?</p>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
        <button id="cancelDeleteBtn" class="btn btn-secondary">Cancel</button>
    </div>
</div>


   
    

    <!-- JavaScript to handle tab switching -->
    <script>
        document.getElementById('my-info-tab').addEventListener('click', function() {
            showContent('my-info-content', this);
        });
        document.getElementById('children-info-tab').addEventListener('click', function() {
            showContent('children-info-content', this);
        });
        document.getElementById('history-tab').addEventListener('click', function() {
            showContent('history-content', this);
        });

        function showContent(contentId, element) {
            var contents = document.querySelectorAll('.profile-content');
            contents.forEach(function(content) {
                content.classList.remove('active');
            });

        document.getElementById(contentId).classList.add('active');

            var tabs = document.querySelectorAll('.profile-options a');
            tabs.forEach(function(tab) {
                tab.classList.remove('active');
            });
            element.classList.add('active');


        }

        document.getElementById('addChildBtn').onclick = function() {
            document.getElementById('addChildModal').style.display = 'block';
        }
        

        function closeModal() {
            document.getElementById('addChildModal').style.display = 'none';
            document.getElementById('childFormModal').style.display = "none";
        }

        window.onclick = function(event) {
            var modal = document.getElementById('addChildModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }


        document.getElementById('addChildForm').onsubmit = function() {
        
        document.getElementById("addChildModal").style.display = "none";
        
        };


        document.addEventListener("DOMContentLoaded", function () {
    fetchUserEmail();
});

// add another contact
document.getElementById('add-contact-btn').addEventListener('click', function () {
    const extraContact = document.getElementById('additional-contact');
    extraContact.style.display = (extraContact.style.display === 'none') ? 'block' : 'none';
});

document.addEventListener("DOMContentLoaded", function () {
    let select = document.getElementById("childSelect");
    var childId = select.value;

    fetch('profile_select.php') 
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">--Select--</option>'; 

            const children = data.children;

            if (Array.isArray(children) && children.length > 0) {
                children.forEach(child => {
                    let option = document.createElement("option");
                    option.value = child.id; // use child_id as value
                    option.textContent = child.name;
                    select.appendChild(option);
                });
            } else {
                let option = document.createElement("option");
                option.textContent = "No children found";
                option.disabled = true;
                select.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error fetching children:', error);
        });

    // moved inside here so it registers correctly
    select.addEventListener("change", displayLearningStatus);
});



//display learning status
document.addEventListener("DOMContentLoaded", function () {
    let select = document.getElementById("childSelect");

    fetch('profile_select.php') 
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">--Select--</option>'; 
            const children = data.children;

            if (Array.isArray(children) && children.length > 0) {
                children.forEach(child => {
                    let option = document.createElement("option");
                    option.value = child.id;
                    option.textContent = child.name;
                    select.appendChild(option);
                });
            } else {
                let option = document.createElement("option");
                option.textContent = "No children found";
                option.disabled = true;
                select.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error fetching children:', error);
        });

    select.addEventListener("change", displayLearningStatus);
});

function displayLearningStatus() {
    const select = document.getElementById("childSelect");
    const childId = select.value;  

    const statusContent = document.getElementById("statusContent");
    const learningStatus = document.getElementById("learningStatus");

    if (!childId) {
        statusContent.innerHTML = "<p class='text-warning'>Please select a child.</p>";
        learningStatus.style.display = "none";
        return;
    }

    fetch(`http://localhost/FYP/profile_learning.php?child_id=${encodeURIComponent(childId)}`)
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                statusContent.innerHTML = "<p>No learning classes.</p>";
                learningStatus.style.display = "block";
                return;
            }

            const currentMonth = new Date().getMonth(); // 0 = January
            const monthMap = {
                "January": 0, "February": 1, "March": 2,
                "April": 3, "May": 4, "June": 5,
                "July": 6, "August": 7, "September": 8,
                "October": 9, "November": 10, "December": 11
            };

            let table = "<table class='table table-striped'><thead><tr><th>Subject</th><th>Part</th><th>Year</th><th>Teacher</th><th>Time</th><th>Status</th></tr></thead><tbody>";
            data.forEach(course => {
                let status = "Ongoing"; // default
                const duration = course.part_duration;
                const timeRange = duration.split(" - ");

                if (timeRange.length === 2) {
                    const startMonthStr = timeRange[0].trim();
                    const endMonthStr = timeRange[1].trim();
                    const startMonth = monthMap[startMonthStr];
                    const endMonth = monthMap[endMonthStr];

                    if (startMonth !== undefined && endMonth !== undefined) {
                        if (currentMonth < startMonth) {
                            status = "Not Started";
                        } else if (currentMonth > endMonth) {
                            status = "Complete";
                        } else {
                            status = "Ongoing";
                        }
                    }
                }

                let statusClass = "";
                if (status === "Ongoing") statusClass = "text-success";
                else if (status === "Complete") statusClass = "text-danger";
                else if (status === "Not Started") statusClass = "text-secondary";

                table += `<tr>
                    <td>${course.subject_name}</td>
                    <td>${course.part_name} (${course.part_duration})</td>
                    <td>${course.year}</td>
                    <td>${course.teacher_name}</td>
                    <td>${course.class_time}</td>
                    <td class="${statusClass}">${status}</td>
                </tr>`;
            });
            table += "</tbody></table>";

            statusContent.innerHTML = table;
            learningStatus.style.display = "block";
        })
        .catch(error => {
            console.error("Error fetching learning status:", error);
            statusContent.innerHTML = "<p class='text-danger'>No register class yet.</p>";
            learningStatus.style.display = "block";
        });
}

document.querySelector('.close').addEventListener('click', function() {
  document.getElementById('childFormModal').style.display = "none";
});

window.addEventListener('click', function(event) {
  if (event.target == document.getElementById('childFormModal')) {
    document.getElementById('childFormModal').style.display = "none";
  }
});

document.getElementById("avatar-upload").addEventListener("change", function(event) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById("user-avatar").src = e.target.result;
    };
    reader.readAsDataURL(event.target.files[0]);
});


// delete modal
document.addEventListener("DOMContentLoaded", function () {
    let selectedChildId = null; // save the select child_id

    // when click delete button，get child_id and display delete modal
    document.querySelector("#children-info-content").addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-btn")) {
            selectedChildId = event.target.getAttribute("data-child-id");
            document.getElementById("deleteConfirmModal").style.display = "flex";
        }
    });

    // cancel delete
    document.getElementById("cancelDeleteBtn").addEventListener("click", function () {
        document.getElementById("deleteConfirmModal").style.display = "none";
        selectedChildId = null; 
    });

    // make sure delete
    document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
        if (selectedChildId) {
            fetch("profile_deletechild.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "child_id=" + encodeURIComponent(selectedChildId)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("deleteConfirmModal").style.display = "none";
                selectedChildId = null;

                if (data.success) {
                    showToast("Children information deleted successfully!");
                    setTimeout(() => { location.reload(); }, 2000);
                } else {
                    showToast("Error: " + data.error, true);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showToast("Unexpected error occurred.", true);
            });
        }
    });
});


// Toast Notification Function
function showToast(message, isError = false) {
    let toast = document.getElementById("successToast");
    toast.innerText = message;
    toast.style.backgroundColor = isError ? "#dc3545" : "#28a745";
    toast.style.display = "block";
    toast.style.opacity = "1";

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => { toast.style.display = "none"; }, 500);
    }, 3000);
}

// edit modal
function openEditModal(name, gender, kidNumber, birthday, school, year, childId) {
    console.log("Opening edit modal with childId:", childId); 

    document.getElementById("childName").value = name;
    document.getElementById("childGender").value = gender;
    document.getElementById("editkidNumber").value = kidNumber;
    document.getElementById("childBirthday").value = birthday;
    document.getElementById("childSchool").value = school;
    document.getElementById("childId").value = childId; 

    let yearSelect = document.getElementById("childYear");
    for (let i = 0; i < yearSelect.options.length; i++) {
        if (yearSelect.options[i].value == year) {
            yearSelect.options[i].selected = true;
            break;
        }
    }

    document.getElementById("childFormModal").style.display = "block";
}

document.getElementById("childForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    fetch("profile_editchild.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            showToast("Child information updated successfully!");
            setTimeout(() => { location.reload(); }, 2000);
        } else {
            showToast("Error: " + data.error, true);
        }
    })
    .catch(error => console.error("Error:", error));
});





// add child
document.getElementById("addChildForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("profile_addchild.php", {
        method: "POST",
        body: formData,
        credentials: "include"  
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            showToast("Child information added successfully!");
            setTimeout(() => { location.reload(); }, 2000);
        } else {
            showToast("Error: " + data.error, true);
        }
    })
    .catch(error => console.error("Error:", error));
});




document.addEventListener("DOMContentLoaded", function () {
    fetch("profile_myinfo.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                document.getElementById("username").value = data.data.parent_name;
                document.getElementById("gender").value = data.data.parent_gender;
                document.getElementById("ic-num").value = data.data.ic_number;
                document.getElementById("email").value = data.data.parent_email;
                document.getElementById("address").value = data.data.parent_address;
                document.getElementById("phone-num-1").value = data.data.phone_number;
                document.getElementById("phone-num-2").value = data.data.phone_number2;
                document.getElementById("relationship").value = data.data.parent_relationship;
                document.getElementById("contact-name").value = data.data.parent_name2;
                document.getElementById("contact-relationship").value = data.data.parent_relationship2;
                document.getElementById("contact-phone").value = data.data.parent_num2;
            } else {
                alert("Failed to load profile: " + data.message);
            }
        })
        .catch(error => console.error("Error fetching profile:", error));
});

document.querySelector("form").addEventListener("submit", async function (event) {
    event.preventDefault();

    const successMessage = document.getElementById("success-message");
    successMessage.style.display = "none"; 

    // get data
    const formData = {
        parent_name: document.getElementById("username").value,
        parent_gender: document.getElementById("gender").value,
        ic_number: document.getElementById("ic-num").value,
        phone_number: document.getElementById("phone-num-1").value,
        phone_number2: document.getElementById("phone-num-2").value,
        parent_relationship: document.getElementById("relationship").value,
        parent_address: document.getElementById("address").value,
        parent_name2: document.getElementById("contact-name").value,
        parent_relationship2: document.getElementById("contact-relationship").value,
        parent_num2: document.getElementById("contact-phone").value,
        current_password: document.getElementById("current-password").value.trim(),
        new_password: document.getElementById("new-password").value.trim(),
        confirm_password: document.getElementById("confirm-password").value.trim(),
    };

    document.getElementById("current-password-error").textContent = "";
    document.getElementById("new-password-error").textContent = "";

    // make sure new and confirm password match
    if (formData.new_password || formData.current_password) {
        if (formData.new_password !== formData.confirm_password) {
            document.getElementById("new-password-error").textContent = "* New and Confirm password do not match";
            return;
        }
    }

    try {
        const response = await fetch("update_profile.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formData),
        });

        const result = await response.json();

        if (result.status === "error") {
            if (result.message === "Current password is incorrect") {
                document.getElementById("current-password-error").textContent = "* Current password is incorrect ";
            } else {
                console.error(result.message);
            }
            return;
        }

        // if update successful clear the line and alert successful message
        if (result.status === "success") {
            document.getElementById("current-password").value = "";
            document.getElementById("new-password").value = "";
            document.getElementById("confirm-password").value = "";

            successMessage.textContent = "Profile updated successfully!";
            successMessage.style.display = "block";
            successMessage.style.color = "green"; // green color successful message

            // after 6 second hide the successful message
            setTimeout(() => {
                successMessage.style.display = "none";
            }, 6000);
        }
    } catch (error) {
        console.error("Error:", error);
    }
});


document.addEventListener("DOMContentLoaded", function () {
    fetchChildrenInfo();
});

function fetchChildrenInfo() {
    fetch("profile_childlist.php")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#children-info-content tbody");
            tbody.innerHTML = ""; 

            if (data.status === "success") {
                data.data.forEach(child => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${child.child_name}</td>
                        <td>${child.child_gender}</td>
                        <td>${child.child_kidNumber}</td>
                        <td>${child.child_birthday}</td>
                        <td>${child.child_school}</td>
                        <td>${child.child_year}</td>
                        <td>
                            <i class="pointer-cursor fas fa-edit text-warning edit-btn" 
                               onclick="openEditModal('${child.child_name}', '${child.child_gender}', '${child.child_kidNumber}','${child.child_birthday}', '${child.child_school}', '${child.child_year}', '${child.child_id}')"></i>
                           <i class="pointer-cursor fas fa-trash-alt text-danger delete-btn"  data-child-id="${child.child_id}"></i>

                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan='7'>${data.message}</td></tr>`;
            }
        })
        .catch(error => console.error("Error fetching child information:", error));
}


document.addEventListener("DOMContentLoaded", function () {
    fetch("profile_history.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "success" && Array.isArray(data.data)) {
                const tableBody = document.querySelector("#history-content tbody");
                tableBody.innerHTML = ""; 

                data.data.forEach(payment => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${payment.child_name}</td>
                        <td>${payment.subject_name}</td>
                        <td>${payment.part_name} </td>
                        <td>${payment.payment_method}</td>
                        <td>RM${parseFloat(payment.payment_total_amount).toFixed(2)}</td>
                        <td>${payment.payment_time}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                console.error("Failed to load payment history:", data.message);
            }
        })
        .catch(error => console.error("Error fetching payment history:", error.message));
});

document.getElementById("kidNumber").addEventListener("input", function () {
            let kidNumber = this.value.trim();
            let birthdayInput = document.getElementById("birthday");

            if (kidNumber.length >= 6) { // atleast 6 num
                let yearPrefix = kidNumber.substring(0, 2) >= "50" ? "19" : "20"; 
                let year = yearPrefix + kidNumber.substring(0, 2);
                let month = kidNumber.substring(2, 4);
                let day = kidNumber.substring(4, 6);

                
                if (month >= "01" && month <= "12" && day >= "01" && day <= "31") {
                    birthdayInput.value = `${year}-${month}-${day}`;
                } else {
                    birthdayInput.value = ""; 
                }
            } else {
                birthdayInput.value = ""; 
            }
        });

    document.addEventListener('DOMContentLoaded', function() {
    fetch('get_notification.php')
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log('Notifications data:', data); 

        // Check if there are any unread notifications
        if (data.notifications && data.notifications.some(notif => notif.read_status === 'unread')) {
            console.log('Unread notifications found!');
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                badge.style.display = 'block'; // Show the red dot
            }
        } else {
            console.log('No unread notifications');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
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
