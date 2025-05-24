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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">

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

    .profile-image-wrapper {
        width: 120px;
        height: 120px;
        display: inline-block; 
        position: relative;    
    }


    .profile-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #dee2e6; 
    }

    .overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 32px;
        height: 32px;
        background-color: #f8f9fa; 
        border-radius: 50%;
        border: 1px solid #6c757d; 
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .camera-icon {
        font-size: 1.2rem;
        color: #212529; 
    }


     .readonly-email {
    background-color: #e9ecef;
    border-color: #ced4da;
    cursor: not-allowed;
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
    .btn-secondary-custom {  /* button second contact*/
        background-color: #6c757d !important;  
        color: white !important;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-secondary-custom:hover {
        background-color: #5a6268 !important;
    }

   
    .form-group button.btn-secondary-custom {
        background-color: #6c757d !important;
    }

    .form-group button.btn-secondary-custom:hover {
        background-color: #5a6268 !important;
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

.result-modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5);
}

.result-modal-content {
  background-color: #fff;
  margin: 10% auto;
  padding: 20px;
  width: 400px;
  border-radius: 8px;
  position: relative;
}

.close-btn {
  position: absolute;
  right: 15px;
  top: 10px;
  cursor: pointer;
  font-size: 20px;
}
.icon-button {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
}



.modal {
    z-index: 1050 !important;
}


.teacher-image-container img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    margin-bottom: 20px;
}


.teacher-modal-dialog {
    max-width: 1000px;
    width: 90%; 
}
.teacher-info-table td, .teacher-info-table th {
    word-break: break-word;
    vertical-align: middle;
}

.even-row {
    background-color: #f2f2f2;
}

.odd-row {
    background-color: #ffffff;
}
.info-reminder {
    background: linear-gradient(to right, #f8f9fa, #e9ecef);
    color: #495057;
    padding: 12px 20px;
    border-radius: 8px;
    margin-top: 20px;
    text-align: center;
    border: 1px solid #dee2e6;
    font-size: 14px;
    width: 85%;
    max-width: 350px;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.info-reminder:before {
    content: "\f05a";  /* Font Awesome info icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 10px;
    color: #6c757d;
}

.info-reminder:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}


  .progress-bar.weak { background-color: #dc3545; }
  .progress-bar.medium { background-color: #ffc107; }
  .progress-bar.strong { background-color: #28a745; }
  .progress-bar.very-strong { background-color: #007bff; }

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
        <a href="../Fyp/member.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
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

            <!-- Replace the existing avatar section with this -->
            <div class="avatar-section">
                <div class="profile-image-wrapper" style="position: relative;">
                    <div class="image-container">
                        <img src="img/user.jpg" alt="User Avatar" id="parent-avatar-preview" class="profile-img">
                        <div class="overlay" title="Click to change image" onclick="document.getElementById('parent-avatar-upload').click()">
                            <i class="fas fa-camera camera-icon"></i>
                        </div>
                    </div>
                    <input type="file" id="parent-avatar-upload" name="parent_image" accept="image/*" style="display: none;">
                </div>
                <div class="info-reminder" style="display: none;">
                    Please complete your personal information.
                </div>
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
        <label for="email">Email </label>
        <input type="email" class="form-control readonly-email" id="email" name="parent_email" readonly title="Email Unchangeable">
    </div>

    <div class="form-group">
        <label for="phone-num-1">Phone Number</label>
        <input type="text" id="phone-num-1" name="phone_number">
    </div>

    <div class="form-group">
        <label for="phone-num-2">Phone Number 2</label>
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
    <button type="button" id="add-contact-btn" class="btn-secondary-custom">Second Contact</button>
</div>

<!-- Additional Contact Section -->
<div id="additional-contact" style="display: none;">
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

                <!-- password strength check -->
            <div class="password-strength mt-2">
            <div class="progress">
                <div id="password-strength-bar" class="progress-bar" role="progressbar" 
                    style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div id="password-strength-text" class="password-strength-text small mt-1"></div>
            </div>
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

<!-- Teacher Info Modal -->
 
<div class="modal fade" id="teacherInfoModal" tabindex="-1" aria-labelledby="teacherInfoModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered teacher-modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="teacherInfoModalLabel">Teacher Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="teacher-info-body">
      </div>
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
                <th>Subject Part</th>
                <th>Subject Name</th>
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
        <div class="profile-image-wrapper">
            <div class="image-container">
                <img src="img/user.jpg" alt="User Avatar" id="child-avatar-preview" class="profile-img">
                <div class="overlay" title="Click to change image" onclick="document.getElementById('child-avatar-upload').click()">
                    <i class="fas fa-camera camera-icon"></i>
                </div>
            </div>
            <input type="file" id="child-avatar-upload" name="child_image" accept="image/*" style="display: none;">
        </div>
    </div>

        <form id="addChildForm" method="post" action="profile_addchild.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="child_name">
                <small class="text-muted" style="font-size: 0.8em; color: #6c757d; display: block; margin-top: 5px;">
                * Please fill out the name as shown in MyKid
                </small>
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
            <div class="profile-image-wrapper" style="position: relative;">
                <div class="image-container">
                    <img src="img/user.jpg" alt="User Avatar" id="edit-child-avatar-preview" class="profile-img">
                    <div class="overlay" title="Click to change image" onclick="document.getElementById('edit-child-avatar-upload').click()">
                        <i class="fas fa-camera camera-icon"></i>
                    </div>
                </div>
                <input type="file" id="edit-child-avatar-upload" name="edit_child_image" accept="image/*" style="display: none;">
            </div>
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

 

   
<!-- Custom Modal -->
<div id="ResultModal" class="result-modal">
  <div class="result-modal-content">
    <span class="close-btn" onclick="closeResultModal()">&times;</span>
    <h5>Exam Result & Teacher Comment</h5>
    <form>
      <div class="mb-3">
        <label for="midterm" class="form-label">Midterm Result</label>
        <input type="text" class="form-control" id="midterm" readonly>
      </div>
      <div class="mb-3">
        <label for="final" class="form-label">Final Result</label>
        <input type="text" class="form-control" id="final" readonly>
      </div>
      <div class="mb-3">
        <label for="teacherComment" class="form-label">Teacher Comment</label>
        <textarea class="form-control" id="teacherComment" rows="3" readonly></textarea>
      </div>
    </form>
  </div>
</div>




</body>   


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


// myinformation reminder
function checkRequiredFields() {
    const requiredFields = {
        'username': {value: document.getElementById('username').value, label: 'Name'},
        'gender': {value: document.getElementById('gender').value, label: 'Gender'},
        'ic-num': {value: document.getElementById('ic-num').value, label: 'IC Number'},
        'phone-num-1': {value: document.getElementById('phone-num-1').value, label: 'Phone Number'},
        'relationship': {value: document.getElementById('relationship').value, label: 'Relationship'},
        'address': {value: document.getElementById('address').value, label: 'Address'}
    };
    
    const infoReminder = document.querySelector('.info-reminder');
    const missingFields = [];
    
    // Check each field and collect missing ones
    Object.entries(requiredFields).forEach(([id, field]) => {
        const element = document.getElementById(id);
        if (element && (!field.value || field.value.trim() === '' || 
            field.value === 'Select your gender' || 
            field.value === 'Select Relationship With Children')) {
            missingFields.push(field.label);
        }
    });
    
    // Show specific message if fields are missing
    if (missingFields.length > 0) {
        const message = `Please fill out your ${missingFields.join(', ')}`;
        infoReminder.textContent = message;
        infoReminder.style.display = 'flex';
    } else {
        infoReminder.style.display = 'none';
    }
}

// Update event listeners
document.addEventListener('DOMContentLoaded', function() {
    const fields = ['username', 'gender', 'ic-num', 'phone-num-1', 'relationship', 'address'];
    
    fields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.addEventListener('input', checkRequiredFields);
            element.addEventListener('change', checkRequiredFields);
        }
    });

    // check when page loads
    setTimeout(checkRequiredFields, 1000);
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

            let table = `
                <table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Part</th>
                            <th>Year</th>
                            <th>Teacher</th>
                            <th>Venue</th>
                            <th>Time</th>
                            <th>Result</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach(course => {
            let status = "Ongoing";
            const duration = course.part_duration;
            const classYear = parseInt(course.class_term); 
            const now = new Date();

            if (duration && !isNaN(classYear)) {
                const timeRange = duration.split(" - ");

                if (timeRange.length === 2) {
                    const startMonthStr = timeRange[0].trim();
                    const endMonthStr = timeRange[1].trim();
                    const startMonth = monthMap[startMonthStr];
                    const endMonth = monthMap[endMonthStr];

                    if (startMonth !== undefined && endMonth !== undefined) {
                        
                        const startDate = new Date(classYear, startMonth, 1);
                        const endDate = new Date(classYear, endMonth + 1, 0); 

                        if (now < startDate) {
                            status = "Not Started";
                        } else if (now > endDate) {
                            status = "Complete";
                        } else {
                            status = "Ongoing";
                        }
                    }
                }
            }

            let statusClass = "";
            if (status === "Ongoing") statusClass = "text-success";
            else if (status === "Complete") statusClass = "text-danger";
            else if (status === "Not Started") statusClass = "text-secondary";

            table += `
                <tr>
                    <td>${course.subject_name}</td>
                    <td>${course.part_name} (${course.part_duration})</td>
                    <td>${course.year}</td>
                    <td>
                        <a href="#" onclick="viewTeacherInfo('${course.teacher_id}', '${course.teacher_name}')">
                            ${course.teacher_name}
                        </a>
                    </td>
                    <td>${course.class_venue}</td>
                    <td>${course.class_time}</td>
                    <td>
                        <button class="icon-button result-btn" 
                                data-child-id="${childId}" 
                                data-class-id="${course.class_id}" 
                                title="View Result">
                            <i class="fas fa-clipboard"></i>
                        </button>
                    </td>
                    <td class="${statusClass}">${status}</td>
                </tr>
            `;
        });


            table += "</tbody></table>";
            statusContent.innerHTML = table;
            learningStatus.style.display = "block";

            
            document.querySelectorAll(".result-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const childId = this.getAttribute("data-child-id");
                    const classId = this.getAttribute("data-class-id");
                    openResultModal(childId, classId);
                });
            });
        })
        .catch(error => {
            console.error("Error fetching learning status:", error);
            statusContent.innerHTML = "<p class='text-danger'>No registered class yet.</p>";
            learningStatus.style.display = "block";
        });
}

function viewTeacherInfo(teacherId, teacherName) {
  fetch("profile_learning.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "teacher_id=" + encodeURIComponent(teacherId)
  })
  .then(res => res.json())
  .then(data => {
    if (data.error) {
      alert(data.error);
    } else {
      // image path
      let imagePath;
      if (data.teacher_image) {
        // Check if the image path starts with 'uploads/'
        imagePath = data.teacher_image.startsWith('uploads/') 
          ? data.teacher_image 
          : `uploads/teacher_images/${data.teacher_image}`;
      } else {
        imagePath = 'img/user.jpg';
      }

      
      console.log("Original teacher image:", data.teacher_image);
      console.log("Processed image path:", imagePath);

      const content = `
        <div class="teacher-image-container text-center mb-3">
          <img src="${imagePath}" 
               alt="Teacher ${data.teacher_name}" 
               class="rounded-circle"
               onerror="this.onerror=null; this.src='img/user.jpg';">
        </div>
        <table class="table table-bordered teacher-info-table">
          <tbody>
            <tr><th>Name</th><td>${data.teacher_name}</td></tr>
            <tr><th>Gender</th><td>${data.teacher_gender}</td></tr>
            <tr><th>Email</th><td>${data.teacher_email}</td></tr>
            <tr><th>Phone</th><td>${data.teacher_phone_number}</td></tr>
          </tbody>
        </table>
      `;
      
      document.getElementById("teacherInfoModalLabel").innerText = `Teacher Info: ${teacherName}`;
      document.getElementById("teacher-info-body").innerHTML = content;
      
      const modal = new bootstrap.Modal(document.getElementById("teacherInfoModal"));
      modal.show();
    }
  })
  .catch(err => {
    console.error("Error fetching teacher info:", err);
    alert("Failed to load teacher information.");
  });
}

function openResultModal(child_id, class_id) {
    fetch(`learning_result_comment.php?child_id=${child_id}&class_id=${class_id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("midterm").value = data.exam_result_midterm ?? '-';
            document.getElementById("final").value = data.exam_result_final ?? '-';
            document.getElementById("teacherComment").value = data.teacher_comment_text ?? '-';
            document.getElementById("ResultModal").style.display = 'block';
        })
        .catch(error => {
            console.error("Error loading result/comment:", error);
        });
}

function closeResultModal() {
    document.getElementById("ResultModal").style.display = 'none';
}





document.querySelector('.close').addEventListener('click', function() {
  document.getElementById('childFormModal').style.display = "none";
});

window.addEventListener('click', function(event) {
  if (event.target == document.getElementById('childFormModal')) {
    document.getElementById('childFormModal').style.display = "none";
  }
});


// add parent modal's image preview
document.getElementById('parent-avatar-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('parent-avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        alert('Please select a valid image file.');
    }
});

 // add child modal's image preview
document.getElementById('child-avatar-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('child-avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        alert('Please select a valid image file.');
    }
});

// edit child modal's image preview
document.getElementById('edit-child-avatar-upload').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit-child-avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        alert('Please select a valid image file.');
    }
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
function openEditModal(name, gender, kidNumber, birthday, school, year, childId, childImage) {
    // use to check debug log
    console.log("Opening edit modal with childId:", childId); 
    console.log("Child image path:", childImage); 

    document.getElementById("childName").value = name;
    document.getElementById("childGender").value = gender;
    document.getElementById("editkidNumber").value = kidNumber;
    document.getElementById("childBirthday").value = birthday;
    document.getElementById("childSchool").value = school;
    document.getElementById("childId").value = childId;

    // Set avatar preview with correct path
    const avatarPreview = document.getElementById("edit-child-avatar-preview");
    
    // Check if childImage contains the full path
    if (childImage && childImage.includes('uploads/child_images/')) {
        avatarPreview.src = childImage;
    } else if (childImage) {
        avatarPreview.src = `uploads/child_images/${childImage}`;
    } else {
        avatarPreview.src = "img/user.jpg";
    }

    // Add error handling for image loading
    avatarPreview.onerror = function() {
        console.error("Failed to load image:", this.src);
        this.src = "img/user.jpg"; // Fallback to default image
    };

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
    
    // Add file input to formData if exists
    const fileInput = document.getElementById('edit-child-avatar-upload');
    if (fileInput.files.length > 0) {
        formData.append('child_image', fileInput.files[0]);
    }

    fetch("profile_editchild.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            // Show success message
            showToast("Child information updated successfully!");

            // Close modal
            document.getElementById('childFormModal').style.display = "none";

            // Reload page to show updated data
            setTimeout(() => { 
                location.reload(); 
            }, 3000);
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

    // Validate required fields
    const requiredFields = ['child_name', 'child_gender', 'child_kidNumber', 'child_birthday', 'child_school', 'child_year'];
    for (let field of requiredFields) {
        if (!formData.get(field)) {
            showToast(`${field.replace('child_', '')} is required`, true);
            return;
        }
    }

    // Validate avatar upload
    const fileInput = document.getElementById('child-avatar-upload');
    if (!fileInput || fileInput.files.length === 0) {
        showToast("Child image is required", true);
        return;
    }

    // Add avatar file
    formData.append('child_image', fileInput.files[0]);

    fetch("profile_addchild.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || "Child information added successfully!");
            document.getElementById('addChildModal').style.display = 'none';
            setTimeout(() => { location.reload(); }, 2000);
        } else {
            showToast("Error: " + (data.error || "Failed to add child"), true);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showToast("Error: " + error.message, true);
    });
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
         
         
                const parentAvatarPreview = document.getElementById("parent-avatar-preview");
                if (data.data.parent_image) {
                    // Check if the path includes the full directory structure
                    const imagePath = data.data.parent_image.includes('uploads/parent_images/') 
                        ? data.data.parent_image 
                        : `uploads/parent_images/${data.data.parent_image}`;
                    
                    parentAvatarPreview.src = imagePath;
                    
                  
                    parentAvatarPreview.onerror = function() {
                        console.error("Failed to load parent image:", this.src);
                        this.src = "img/user.jpg"; 
                    };
                } else {
                    parentAvatarPreview.src = "img/user.jpg"; // Default image if no profile image
                }
            } else {
                alert("Failed to load profile: " + data.message);
            }
        })
        .catch(error => console.error("Error fetching profile:", error));
});

 // Password strength evaluation
    function evaluatePasswordStrength(password) {
      let strength = 0;
      const hasLower = /[a-z]/.test(password);
      const hasUpper = /[A-Z]/.test(password);
      const hasNumber = /\d/.test(password);
      const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
      const length = password.length;

      // Count character types
      if (hasLower) strength++;
      if (hasUpper) strength++;
      if (hasNumber) strength++;
      if (hasSpecial) strength++;

      // Adjust based on length
      if (length >= 12) strength += 1;
      else if (length < 8) strength = Math.min(strength, 1);

      // Determine strength level
      let strengthLevel = 'weak';
      let strengthText = '';
      let progressWidth = 0;
      let progressClass = 'weak';

      if (length === 0) {
        strengthLevel = 'none';
        strengthText = 'Enter a password';
        progressWidth = 0;
      } else if (length < 8) {
        strengthLevel = 'weak';
        strengthText = 'Weak: Must be at least 8 characters';
        progressWidth = 25;
      } else if (strength <= 2) {
        strengthLevel = 'weak';
        strengthText = 'Weak: Add uppercase, numbers, or special characters';
        progressWidth = 25;
      } else if (strength === 3) {
        strengthLevel = 'medium';
        strengthText = 'Medium: Add special characters for stronger password';
        progressWidth = 50;
        progressClass = 'medium';
      } else if (strength === 4) {
        strengthLevel = 'strong';
        strengthText = 'Strong: Good password!';
        progressWidth = 75;
        progressClass = 'strong';
      } else {
        strengthLevel = 'very-strong';
        strengthText = 'Very Strong: Excellent password!';
        progressWidth = 100;
        progressClass = 'very-strong';
      }

      return { strengthLevel, strengthText, progressWidth, progressClass };
    }

  // Real-time password strength check
    document.getElementById('new-password').addEventListener('input', function () {
        const password = this.value;
        const { strengthLevel, strengthText, progressWidth, progressClass } = evaluatePasswordStrength(password);
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthTextEl = document.getElementById('password-strength-text');
        const saveButton = document.querySelector('form button[type="submit"]');

        strengthBar.style.width = `${progressWidth}%`;
        strengthBar.className = `progress-bar ${progressClass}`;
        strengthTextEl.textContent = strengthText;

        // Enable/disable save button based on password strength
        if (strengthLevel !== 'medium' && strengthLevel !== 'strong' && strengthLevel !== 'very-strong') {
            saveButton.disabled = true;
            saveButton.style.opacity = '0.5';
            saveButton.style.cursor = 'not-allowed';
        } else {
            saveButton.disabled = false;
            saveButton.style.opacity = '1';
            saveButton.style.cursor = 'pointer';
        }
    });


document.querySelector("form").addEventListener("submit", async function (event) {
    event.preventDefault();

    const successMessage = document.getElementById("success-message");
    successMessage.style.display = "none";

    const formData = new FormData();
    formData.append("parent_name", document.getElementById("username").value);
    formData.append("parent_gender", document.getElementById("gender").value);
    formData.append("ic_number", document.getElementById("ic-num").value);
    formData.append("phone_number", document.getElementById("phone-num-1").value);
    formData.append("phone_number2", document.getElementById("phone-num-2").value);
    formData.append("parent_relationship", document.getElementById("relationship").value);
    formData.append("parent_address", document.getElementById("address").value);
    formData.append("parent_name2", document.getElementById("contact-name").value);
    formData.append("parent_relationship2", document.getElementById("contact-relationship").value);
    formData.append("parent_num2", document.getElementById("contact-phone").value);
    formData.append("current_password", document.getElementById("current-password").value.trim());
    formData.append("new_password", document.getElementById("new-password").value.trim());
    formData.append("confirm_password", document.getElementById("confirm-password").value.trim());

    const imageFile = document.getElementById("parent-avatar-upload").files[0];
    if (imageFile) {
        formData.append("parent_image", imageFile);
    }

    try {
        const response = await fetch("update_profile.php", {
            method: "POST",
            body: formData,
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
            // Reset form fields
            document.getElementById("current-password").value = "";
            document.getElementById("new-password").value = "";
            document.getElementById("confirm-password").value = "";
            
            // Reset password strength indicators
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthTextEl = document.getElementById('password-strength-text');
            strengthBar.style.width = "0%";
            strengthBar.className = "progress-bar";
            strengthTextEl.textContent = "";
            
            // Reset error messages
            document.getElementById("current-password-error").textContent = "";
            document.getElementById("new-password-error").textContent = "";

            // Show and hide success message
            successMessage.textContent = "Profile updated successfully!";
            successMessage.style.display = "block";
            successMessage.style.color = "green";

            // Hide success message after delay
            setTimeout(() => {
                successMessage.style.display = "none";
                successMessage.textContent = "";
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
            console.log("Received child data:", data); 
            const tbody = document.querySelector("#children-info-content tbody");
            tbody.innerHTML = ""; 

            if (data.status === "success") {
                data.data.forEach(child => {
                    const row = document.createElement("tr");
                    // Clean up the image path before passing it
                    const imagePath = child.child_image ? child.child_image.replace(/^uploads\/child_images\//, '') : '';
                    
                    row.innerHTML = `
                        <td>${child.child_name}</td>
                        <td>${child.child_gender}</td>
                        <td>${child.child_kidNumber}</td>
                        <td>${child.child_birthday}</td>
                        <td>${child.child_school}</td>
                        <td>${child.child_year}</td>
                        <td>
                            <i class="pointer-cursor fas fa-edit text-warning edit-btn" 
                               onclick="openEditModal('${child.child_name}', '${child.child_gender}', 
                               '${child.child_kidNumber}','${child.child_birthday}', 
                               '${child.child_school}', '${child.child_year}', 
                               '${child.child_id}', '${imagePath}')"></i>
                            
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

                const grouped = {};

                // grouping data by payment_id
                data.data.forEach(item => {
                    if (!grouped[item.payment_id]) {
                        grouped[item.payment_id] = {
                            payment_method: item.payment_method,
                            payment_total_amount: item.payment_total_amount,
                            payment_time: item.payment_time,
                            children: []
                        };
                    }
                    grouped[item.payment_id].children.push({
                        child_name: item.child_name,
                        subject_name: item.subject_name,
                        part_name: item.part_name
                    });
                });

                // sorting by payment_time
                const sortedGroups = Object.entries(grouped).sort((a, b) => {
                    return new Date(b[1].payment_time) - new Date(a[1].payment_time);
                });

                //insert data into table
                sortedGroups.forEach(([payment_id, group], groupIndex) => {
                    const rowClass = groupIndex % 2 === 0 ? 'even-row' : 'odd-row';
                    const row = document.createElement("tr");
                    row.classList.add(rowClass);

                    
                    const childNames = group.children.map(c => c.child_name).join(", ");
                    const subjectNames = group.children.map(c => c.subject_name).join(", ");
                    const partNames = group.children.map(c => c.part_name).join(", ");

                    row.innerHTML = `
                        <td>${childNames}</td>
                        <td>${partNames}</td>
                        <td>${subjectNames}</td>
                        <td>${group.payment_method}</td>
                        <td>RM${parseFloat(group.payment_total_amount).toFixed(2)}</td>
                        <td>${new Date(group.payment_time).toLocaleString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        })}</td>
                    `;

                    tableBody.appendChild(row);
                });
            } else {
                console.error("Failed to load payment history:", data.message);
            }
        })
        .catch(error => console.error("Error fetching payment history:", error.message));
});


// Format phone number input
function addPhoneNumberValidation(inputId) {
    const input = document.getElementById(inputId);
    
    // Add error message element after input
    let errorDiv = input.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains('error-message')) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.8em';
        errorDiv.style.marginTop = '5px';
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }

    input.addEventListener("input", function(e) {
        // Remove non-digits
        let value = this.value.replace(/\D/g, '');
        if (value.length > 10) value = value.substr(0, 10);
        
        // Add hyphen automatically
        if (value.length >= 3) {
            value = value.substr(0, 3) + '-' + value.substr(3);
        }
        this.value = value;
        
        // Validate format
        let phoneNumber = value.replace(/-/g, '');
        if (phoneNumber.length > 0 && phoneNumber.length < 10) {
            errorDiv.textContent = "Phone number must be exactly 10 digits (000-0000000)";
        } else if (phoneNumber.length === 10) {
            errorDiv.textContent = "";
        } else {
            errorDiv.textContent = "";
        }
    });
}

// Apply phone number validation
document.addEventListener("DOMContentLoaded", function() {
    // For primary phone number
    addPhoneNumberValidation("phone-num-1");
    // For secondary phone number
    addPhoneNumberValidation("phone-num-2");
    // For additional contact phone
    addPhoneNumberValidation("contact-phone");
});
// parent's IC number validation
function addICNumberValidation(inputId) {
    const input = document.getElementById(inputId);
    
    // Add error message element after input
    let errorDiv = input.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains('error-message')) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.8em';
        errorDiv.style.marginTop = '5px';
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }

    input.addEventListener("input", function(e) {
        // Remove non-digits and format with hyphens
        let value = this.value.replace(/\D/g, '');
        if (value.length > 12) value = value.substr(0, 12);
        
        // Add hyphens automatically
        if (value.length >= 6) {
            value = value.substr(0, 6) + '-' + value.substr(6);
        }
        if (value.length >= 9) {
            value = value.substr(0, 9) + '-' + value.substr(9);
        }
        this.value = value;
        
        // Validate format
        let icNumber = value.replace(/-/g, '');
        if (icNumber.length > 0 && icNumber.length < 12) {
            errorDiv.textContent = "IC number must be exactly 12 digits (000000-00-0000)";
        } else if (icNumber.length === 12) {
            errorDiv.textContent = "";
        } else {
            errorDiv.textContent = "";
        }
    });
}

// Apply IC number validation to parent's IC input
document.addEventListener("DOMContentLoaded", function() {
    addICNumberValidation("ic-num");
});



// Validate kid number and auto-fill birthday(2018-2025)
function addKidNumberValidation(inputId, birthdayId) {
    const input = document.getElementById(inputId);
    const birthdayInput = document.getElementById(birthdayId);
    
    // Add error message element after input
    let errorDiv = input.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains('error-message')) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.8em';
        errorDiv.style.marginTop = '5px';
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }

    input.addEventListener("input", function(e) { //format: 000000-00-0000
        // Remove non-digits and format with hyphens
        let value = this.value.replace(/\D/g, '');
        if (value.length > 12) value = value.substr(0, 12);
        
        // Add hyphens automatically
        if (value.length >= 6) {
            value = value.substr(0, 6) + '-' + value.substr(6);
        }
        if (value.length >= 9) {
            value = value.substr(0, 9) + '-' + value.substr(9);
        }
        this.value = value;
        
        // Remove hyphens for validation
        let kidNumber = value.replace(/-/g, '');
        
        if (kidNumber.length >= 6) {
            let currentYear = new Date().getFullYear();
            let currentYearLastTwo = currentYear % 100; //get last two digits of current year 2025=25
            let yearInput = parseInt(kidNumber.substring(0, 2));
            let month = kidNumber.substring(2, 4);
            let day = kidNumber.substring(4, 6);
            
            // Calculate valid year range
            let validEndYear = (currentYearLastTwo - 7);// 2025-7 years ago = 2018（endyear）
            let validStartYear = validEndYear - 5;// 2018-5 = 2013（startyear）

            if (validStartYear < 0) {
                validStartYear += 100;
            }
            if (validEndYear < 0) {
                validEndYear += 100;
            }

            // Validate year range
            let yearPrefix = "20";
            let isValidYear = false;
            
            if (validStartYear < validEndYear) {
                isValidYear = yearInput >= validStartYear && yearInput <= validEndYear;
            } else {
                isValidYear = (yearInput >= validStartYear && yearInput <= 99) || 
                             (yearInput >= 0 && yearInput <= validEndYear);
            }

            // Show error messages
            if (!isValidYear) {
                errorDiv.textContent = `Invalid year. Must be between 20${validStartYear} and 20${validEndYear}`;
                birthdayInput.value = "";
                return;
            }

            // Validate month and day
            if (month < "01" || month > "12") {
                errorDiv.textContent = "Invalid month. Must be between 01 and 12";
                birthdayInput.value = "";
                return;
            }

            if (day < "01" || day > "31") {
                errorDiv.textContent = "Invalid day. Must be between 01 and 31";
                birthdayInput.value = "";
                return;
            }

            // Final date validation
            let fullYear = yearPrefix + yearInput;
            let date = new Date(fullYear + "-" + month + "-" + day);
            if (!(date instanceof Date) || isNaN(date)) {
                errorDiv.textContent = "Invalid date";
                birthdayInput.value = "";
                return;
            }

            if (kidNumber.length === 12) {
                // All validations pass
                errorDiv.textContent = "";
                birthdayInput.value = `${fullYear}-${month}-${day}`;
            } else {
                errorDiv.textContent = "MyKid number must be exactly 12 digits (000000-00-0000)";
                birthdayInput.value = "";
            }
        } else {
            if (kidNumber.length > 0) {
                errorDiv.textContent = "";
            } else {
                errorDiv.textContent = "";
            }
            birthdayInput.value = "";
        }
    });
}

// Apply kidnumber validation to both forms
document.addEventListener("DOMContentLoaded", function() {
    // For add child form
    addKidNumberValidation("kidNumber", "birthday");
    
    // For edit child form
    addKidNumberValidation("editkidNumber", "childBirthday");
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
                <!-- 品牌标语 -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">The Seeds Learning Tuition Centre</h4>
                    <p>Every child is a different kind of flower. We nurture their growth.</p>
                </div>
                <!-- Quick Link -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">Quick Link</h4>
                    <a class="btn btn-link" href="about.html">About Us</a><br>
                </div>
                <!-- Contact -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-4">Contact</h4>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>TheSeeds@gmail.com</p>
                    <!-- 社交媒体（可替换为真实链接） -->
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="https://www.facebook.com/people/The-Seeds-Learning-Centre/100063525220441/#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="https://www.instagram.com/theseeds_kulai?igsh=dGt4YWpiOWJiem44"><i class="fab fa-instagram"></i></a>
                        <a class="btn btn-outline-light btn-social" href="https://wa.me/60117758990"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        © <a class="border-bottom" href="#">The Seeds Learning Tuition Centre</a>, All Right Reserved.
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
