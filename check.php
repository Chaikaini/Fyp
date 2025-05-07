<?php
// Start session
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['parent_id'])) {
    $_SESSION['message'] = 'Please log in to proceed with checkout';
    header('Location: login.html');
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "the seeds");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get phone number and saved credit card using parent_id
$userPhoneNumber = "";
$savedCard = null;
$parent_id = $_SESSION['parent_id'];

// Check if user has previous enrollments
$hasPreviousEnrollment = false;
$enrollmentCheckStmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM enrollment 
    WHERE parent_id = ?
");
$enrollmentCheckStmt->bind_param("i", $parent_id);
$enrollmentCheckStmt->execute();
$result = $enrollmentCheckStmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hasPreviousEnrollment = $row['count'] > 0;
}
$enrollmentCheckStmt->close();

// Get user phone number
$stmt = $conn->prepare("SELECT phone_number FROM parent WHERE parent_id = ?");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userPhoneNumber = $row['phone_number'] ?? '';
}
$stmt->close();

// Check for saved credit card
$stmt = $conn->prepare("SELECT last_four, expiry_date FROM credit_cards WHERE parent_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $savedCard = $result->fetch_assoc();
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre, checkout" name="keywords">
    <meta content="The Seeds Learning Centre | Checkout" name="description">

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

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .breadcrumb-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .breadcrumb-container h2 {
            margin-bottom: 10px;
        }

        .breadcrumb-container .breadcrumb {
            display: flex;
            gap: 5px;
            font-size: 14px;
        }

        .breadcrumb-container .breadcrumb li {
            color: #555;
        }

        .breadcrumb-container .breadcrumb li a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb-container .breadcrumb li a:hover {
            text-decoration: underline;
        }

        .containerp {
            max-width: 650px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-top: 50px;
        }

        .course-info, .payment-info {
            margin-bottom: 20px;
        }

        .course-info h2, .payment-info h2 {
            margin-bottom: 10px;
        }

        .course-item {
            margin-top: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .course-item img {
            width: 180px;
            height: 200px;
            margin-right: 20px;
            transition: none !important;
            transform: none !important;
        }

        .course-item img:hover {
            transform: none !important;
            transition: none !important;
        }

        .total-amount {
            font-weight: 0;
            margin-top: 10px;
            text-align: left;
        }

        .amount-detail {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .payment-method {
            margin-top: 20px;
        }

        .payment-method label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .payment-method img {
            margin-right: 10px;
            width: 24px;
            height: 24px;
        }

        .option-content {
            display: flex;
            align-items: center;
            flex-grow: 1; /* Ensure option-content takes up remaining space */
        }

        #phone-number-field, #credit-card-field {
            display: none;
            margin-top: 5px;
            margin-left: 0; /* Remove padding-left to avoid horizontal shift */
        }

        .phone-input {
            display: block;
        }

        .credit-card-input {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .credit-card-input input {
            border: none;
            border-bottom: 1px solid lightgray;
            background-color: #f0f0f0;
            padding: 5px;
            outline: none;
        }

        .credit-card-input input:focus {
            border-bottom: 2px solid #15ca39;
            background-color: white;
        }

        .saved-card {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .payment {
            display: flex;
            justify-content: flex-end;
            margin-top: 50px;
        }

        .payment button {
            background-color: #15ca39;
            color: white;
            padding: 10px 50px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .payment button:hover {
            background-color: #14a631;
        }

        .error-message {
            color: red;
            display: none;
            font-size: 20px;
        }

        .modal {
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

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 350px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .payment_picture {
            width: 80px;
            height: 80px;
            margin: 10px auto;
            display: block;
        }

        #paymentTitle {
            color: black;
            font-size: 22px;
            margin: 10px 0;
        }

        #paymentMessage {
            font-size: 16px;
            color: #333;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #218838;
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

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            z-index: 9999;
        }

        .toast.success {
            background-color: green;
        }

        .toast.error {
            background-color: red;
        }

        .hidden-row {
            display: none !important;
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
                <a href="contact.html" class="nav-item nav-link">Contact us</a>
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

    <!-- Breadcrumb Section -->
    <div class="breadcrumb-container">
        <h1>Checkout</h1>
        <ul class="breadcrumb">
            <li><a href="Home.html">Home</a></li>
            <li>></li>
            <li><a href="cart.html">Shopping Cart</a></li>
            <li>></li>
            <li>Checkout</li>
        </ul>
    </div>

    <!-- Payment Start -->
    <div class="containerp">
        <h2>Checkout</h2>
        <div id="cart-container"></div>

         <div class="total-amount">
            <h4>Payment Details</h4>
            <div class="amount-detail">
                <p>Subject Fee:</p>
                <p id="subject-fee">RM0</p>
            </div>
            <div class="amount-detail <?php echo $hasPreviousEnrollment ? 'hidden-row' : ''; ?>" id="enrollment-fee-row">
                <p>Enrollment Fee:</p>
                <p>RM100</p>
            </div>
            <div class="amount-detail">
                <h5><p><b>Total Amount:</b></p></h5>
                <p id="total-amount"><b>RM0</b></p>
            </div>
        </div>

        <hr>

        <div class="payment-info">
            <h4>Choose Your Payment Method</h4>
            <div class="payment-method">
                <br>
                <label for="tng" class="payment-option">
                    <div class="option-content">
                        <img src="img/tng.png" alt="Touch 'n Go" class="payment-icon"> Touch 'n Go eWallet
                    </div>
                    <input type="radio" id="tng" name="payment" value="tng">
                </label>
                <div id="phone-number-field" style="display: none;">
                    <label>Your phone number:</label>
                    <div class="phone-input">
                        <span>+60 <?php echo !empty($userPhoneNumber) ? htmlspecialchars($userPhoneNumber) : 'Phone number not found'; ?></span>
                        <input type="hidden" id="phone-number" name="phone-number" 
                               value="<?php echo !empty($userPhoneNumber) ? htmlspecialchars($userPhoneNumber) : ''; ?>">
                    </div>
                </div>
                <br>
                <label for="creditcard" class="payment-option">
                    <div class="option-content">
                        <img src="img/creditcard.png" alt="Credit Card" class="payment-icon"> Credit Card
                    </div>
                    <input type="radio" id="creditcard" name="payment" value="creditcard">
                </label>
                <div id="credit-card-field" style="display: none;">
                    <label>Credit Card Details:</label>
                    <?php if ($savedCard): ?>
                        <div class="saved-card">
                            <label>
                                <input type="radio" name="card-option" value="saved" checked>
                                Use saved card: **** **** **** <?php echo htmlspecialchars($savedCard['last_four']); ?> (Expires: <?php echo htmlspecialchars($savedCard['expiry_date']); ?>)
                            </label>
                        </div>
                        <label>
                            <input type="radio" name="card-option" value="new">
                            Use a new card
                        </label>
                    <?php endif; ?>
                    <div class="credit-card-input" id="new-card-input" style="<?php echo $savedCard ? 'display: none;' : ''; ?>">
                        <input type="text" id="card-number" placeholder="Card Number (e.g. 1234 5678 9012 3456)" maxlength="19">
                        <input type="text" id="expiry-date" placeholder="MM/YY" maxlength="5">
                        <input type="text" id="cvv" placeholder="CVV" maxlength="3">
                        <label><input type="checkbox" id="save-card" checked> Save card for future payments</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="payment">
            <button type="submit" onclick="validateForm()">Payment</button>
        </div>
    </div>

    <!-- Payment Confirmation Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <img id="payment_picture" src="" alt="Payment Status" class="payment_picture">
            <h2 id="paymentTitle">Payment Successful</h2>
            <p id="paymentMessage">You can check your payment history in the profile page.</p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
          document.addEventListener('DOMContentLoaded', function() {
            // 初始化支付方法字段
            const tngRadio = document.getElementById("tng");
            const creditCardRadio = document.getElementById("creditcard");
            const phoneNumberField = document.getElementById("phone-number-field");
            const creditCardField = document.getElementById("credit-card-field");
            const newCardInput = document.getElementById("new-card-input");
            const savedCardOption = document.querySelector('input[name="card-option"][value="saved"]');
            const newCardOption = document.querySelector('input[name="card-option"][value="new"]');

            // 设置初始状态
            phoneNumberField.style.display = "none";
            creditCardField.style.display = "none";

            // 处理支付方法切换
            tngRadio.addEventListener("change", function() {
                phoneNumberField.style.display = tngRadio.checked ? "block" : "none";
                creditCardField.style.display = "none";
            });

            creditCardRadio.addEventListener("change", function() {
                creditCardField.style.display = creditCardRadio.checked ? "block" : "none";
                phoneNumberField.style.display = "none";
            });

            if (newCardOption) {
                newCardOption.addEventListener("change", function() {
                    newCardInput.style.display = newCardOption.checked ? "block" : "none";
                });
                savedCardOption.addEventListener("change", function() {
                    newCardInput.style.display = savedCardOption.checked ? "none" : "block";
                });
            }

            // Fetch notifications
            fetch('get_notification.php')
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Notifications data:', data);
                    if (data.notifications && data.notifications.some(notif => notif.read_status === 'unread')) {
                        console.log('Unread notifications found!');
                        const badge = document.querySelector('.notification-badge');
                        if (badge) {
                            badge.style.display = 'block';
                        }
                    } else {
                        console.log('No unread notifications');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });

            // Fetch cart data
            fetch("checkout.php")
                .then(response => response.json())
                .then(data => {
                    console.log("Cart Data:", data);
                    const container = document.getElementById("cart-container");
                    let totalAmount = 0;
                    
                    // 从PHP传递的值决定是否显示注册费
                    const showEnrollmentFee = <?php echo $hasPreviousEnrollment ? 'false' : 'true'; ?>;
                    const enrollmentFee = showEnrollmentFee ? 100 : 0;
                    
                    container.innerHTML = "";
                    data.forEach(item => {
                        totalAmount += parseFloat(item.price);
                        const courseItem = `
                            <div class="course-item" 
                                 data-cart-id="${item.cart_id}" 
                                 data-class-id="${item.class_id}" 
                                 data-child-id="${item.child_id}" 
                                 data-subject-id="${item.subject_id}" 
                                 data-teacher-id="${item.teacher_id}">
                                <img src="${item.subject_image || 'images/default.png'}" alt="${item.subject_name}">
                                <div>
                                    <p><b>${item.subject_name}</b></p>
                                    <p><b>Teacher:</b> ${item.teacher_name}</p>
                                    <p><b>Price:</b> RM${item.price}</p>
                                    <p><b>Time:</b> ${item.class_time}</p>
                                    <p><b>Student:</b> ${item.child_name}</p>
                                </div>
                            </div>
                            <hr>
                        `;
                        container.innerHTML += courseItem;
                    });
                    
                    document.getElementById("subject-fee").innerText = `RM${totalAmount}`;
                    document.getElementById("total-amount").innerText = `RM${totalAmount + enrollmentFee}`;
                })
                .catch(error => console.error("Error fetching cart data:", error));


        async function validateForm() {
            const tngSelected = document.getElementById('tng').checked;
            const creditCardSelected = document.getElementById('creditcard').checked;
            const paymentMethod = tngSelected ? "TNG" : creditCardSelected ? "Credit Card" : "";

            if (!paymentMethod) {
                showToast("Please select a payment method.", "error");
                return;
            }

            let phoneNumber = "";
            let cardDetails = null;
            if (tngSelected) {
                phoneNumber = document.getElementById('phone-number').value;
                if (!phoneNumber) {
                    showToast("Phone number is required for TNG payment.", "error");
                    return;
                }
            } else if (creditCardSelected) {
                const useSavedCard = document.querySelector('input[name="card-option"][value="saved"]')?.checked;
                if (!useSavedCard) {
                    const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
                    const expiryDate = document.getElementById('expiry-date').value;
                    const cvv = document.getElementById('cvv').value;
                    if (!cardNumber || !/^\d{16}$/.test(cardNumber)) {
                        showToast("Please enter a valid 16-digit card number.", "error");
                        return;
                    }
                    if (!expiryDate || !/^\d{2}\/\d{2}$/.test(expiryDate)) {
                        showToast("Please enter a valid expiry date (MM/YY).", "error");
                        return;
                    }
                    if (!cvv || !/^\d{3}$/.test(cvv)) {
                        showToast("Please enter a valid 3-digit CVV.", "error");
                        return;
                    }
                    cardDetails = {
                        card_number: cardNumber,
                        expiry_date: expiryDate,
                        cvv: cvv,
                        save_card: document.getElementById('save-card').checked
                    };
                }
            }

            const cartItems = Array.from(document.querySelectorAll(".course-item")).map(item => ({
                cart_id: item.getAttribute("data-cart-id"),
                class_id: item.getAttribute("data-class-id"),
                child_id: item.getAttribute("data-child-id"),
                subject_id: item.getAttribute("data-subject-id"),
                teacher_id: item.getAttribute("data-teacher-id")
            }));
            const cartIds = cartItems.map(item => item.cart_id).join(",");
            const totalAmount = document.getElementById("total-amount").innerText.replace("RM", "").trim();

            if (cartItems.length === 0) {
                showToast("No items in cart.", "error");
                return;
            }

            // 检查购物车中的每个 (child_id, subject_id) 是否已经注册
            try {
                const response = await fetch("check_registration.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ cart_items: cartItems })
                });
                const result = await response.json();

                if (!result.success) {
                    showToast(result.message || "Some subjects have already been registered for the selected children.", "error");
                    return;
                }
            } catch (error) {
                console.error("Error checking registration:", error);
                showToast("Error checking registration status.", "error");
                return;
            }

            const orderData = {
                cart_items: cartItems,
                total_amount: totalAmount,
                payment_method: paymentMethod,
                cart_ids: cartIds,
                phone: phoneNumber,
                card_details: cardDetails
            };

            console.log("Order Data:", orderData);

            fetch("process_payment.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                console.log("Payment Response:", data);
                if (data.success) {
                    showPaymentModal(true);
                    setTimeout(() => { window.location.href = "subject.html"; }, 4000);
                } else {
                    showPaymentModal(false);
                    showToast(data.message || "Payment failed.", "error");
                }
            })
            .catch(error => {
                console.error("Error processing payment:", error);
                showPaymentModal(false);
                showToast("Error processing payment.", "error");
            });
        }

        function showToast(message, type) {
            const toastContainer = document.createElement('div');
            toastContainer.classList.add('toast', type);
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.padding = '15px';
            toastContainer.style.borderRadius = '5px';
            toastContainer.style.color = 'white';
            toastContainer.style.zIndex = '9999';
            toastContainer.style.backgroundColor = type === 'error' ? 'red' : 'green';
            toastContainer.innerText = message;
            document.body.appendChild(toastContainer);
            setTimeout(() => {
                toastContainer.remove();
            }, 3000);
        }

        function showPaymentModal(isSuccess) {
            const modal = document.getElementById("paymentModal");
            const paymentIcon = document.getElementById("payment_picture");
            const paymentTitle = document.getElementById("paymentTitle");
            const paymentMessage = document.getElementById("paymentMessage");
            if (isSuccess) {
                paymentIcon.src = "img/payment_s.png";
                paymentTitle.innerText = "Payment Successful";
                paymentMessage.innerText = "You can check your payment history in the profile page.";
            } else {
                paymentIcon.src = "img/payment_ns.png";
                paymentTitle.innerText = "Payment Failed";
                paymentMessage.innerText = "There was an issue processing your payment.";
            }
            modal.style.display = "flex";
        }

        function closeModal() {
            document.getElementById("paymentModal").style.display = "none";
        }
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
                        © <a class="border-bottom" href="#">The Seeds Learning Centre</a>, All Rights Reserved.
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