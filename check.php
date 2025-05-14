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

// Check if user has previous registrations
$hasPreviousEnrollment = false;
$registrationCheckStmt = $conn->prepare("SELECT COUNT(*) as count FROM registration_class WHERE parent_id = ?");
$registrationCheckStmt->bind_param("i", $parent_id);
$registrationCheckStmt->execute();
$result = $registrationCheckStmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hasPreviousEnrollment = $row['count'] > 0;
    error_log("Registration count for parent_id $parent_id: " . $row['count']);
} else {
    error_log("No registration records found for parent_id $parent_id");
}
$registrationCheckStmt->close();

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

error_log("hasPreviousEnrollment: " . ($hasPreviousEnrollment ? 'true' : 'false'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre, checkout" name="keywords">
    <meta content="The Seeds Learning Centre | Checkout" name="description">

    <link href="img/the_seeds.jpg" rel="icon" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate.min.css" rel="stylesheet">
    <link href="lib/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
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

        .payment-option-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }

        .payment-method label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .payment-method i {
            margin-right: 10px;
            font-size: 24px;
        }

        .option-content {
            display: flex;
            align-items: center;
            flex-grow: 1;
        }

        #credit-card-field {
            display: none;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .credit-card-input {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .credit-card-input input {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px;
            outline: none;
            font-size: 14px;
        }

        .credit-card-input input:focus {
            border-color: #15ca39;
            box-shadow: 0 0 5px rgba(21, 202, 57, 0.3);
        }

        .saved-card {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .security-note {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
            display: flex;
            align-items: center;
        }

        .security-note i {
            margin-right: 5px;
            color: #15ca39;
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
            padding: 10px 50px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
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
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            text-align: center;
            width: 300px;
        }

        .toast.show {
            display: block;
        }

        .toast.error .toast-image {
            content: url('img/payment_ns.png');
        }

        .toast.success .toast-image {
            content: url('img/payment_s.png');
        }

        .toast-image {
            width: 80px;
            height: 80px;
            margin: 10px auto;
        }

        .toast-message {
            font-size: 16px;
            color: #333;
            margin: 10px 0;
        }

        .toast-close {
            background-color: #15ca39;
            color: white;
            border: none;
            padding: 10px 50px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .toast-close:hover {
            background-color: #14a631;
        }

        .hidden-row {
            display: none !important;
        }
    </style>
</head>

<body>
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

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
                <a href="subject.html" class="nav-item nav-link">Subjects</a>
                <a href="about.html" class="nav-item nav-link">About Us</a>
                <a href="contact.html" class="nav-item nav-link">Contact Us</a>
                <a href="comment.html" class="nav-item nav-link">Reviews</a>
            </div>
            <a href="login.html" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Log Out<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>

    <div class="icon-bar">
        <a href="notification.php" class="notification-icon">
            <i class="fas fa-bell"></i>
            <span class="notification-badge" style="display: none;"></span>
        </a>
        <a href="cart.html"><i class="fas fa-shopping-cart"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>

    <div class="breadcrumb-container">
        <h1>Checkout</h1>
        <ul class="breadcrumb">
            <li><a href="Home.html">Home</a></li>
            <li>></li>
            <li><a href="cart.html">Cart</a></li>
            <li>></li>
            <li>Checkout</li>
        </ul>
    </div>

    <div class="containerp">
        <h2>Checkout</h2>
        <div id="cart-container"></div>

        <div class="total-amount">
            <h4>Payment Details</h4>
            <div class="amount-detail">
                <p>Subject Fees:</p>
                <p id="subject-fee">RM0</p>
            </div>
            <div class="amount-detail" id="enrollment-fee-row">
                <p>Enrollment Fee:</p>
                <p id="enrollment-fee-amount">RM100</p>
            </div>
            <div class="amount-detail">
                <h5><p><b>Total Amount:</b></p></h5>
                <p id="total-amount"><b>RM0</b></p>
            </div>
        </div>

        <hr>

        <div class="payment-info">
            <h4>Payment Method</h4>
            <div class="payment-method">
                <div class="payment-option-card">
                    <label for="creditcard" class="payment-option">
                        <div class="option-content">
                            <i class="fas fa-credit-card"></i>
                            Credit Card
                        </div>
                        <input type="radio" id="creditcard" name="payment" value="Credit Card">
                    </label>
                </div>
                <div id="credit-card-field">
                    <label>Step 2: Enter Credit Card Details</label>
                    <?php if ($savedCard): ?>
                        <div class="saved-card">
                            <label>
                                <input type="radio" name="card-option" value="saved" checked>
                                Use Saved Card: **** **** **** <?php echo htmlspecialchars($savedCard['last_four']); ?> (Expires: <?php echo htmlspecialchars($savedCard['expiry_date']); ?>)
                            </label>
                        </div>
                        <label>
                            <input type="radio" name="card-option" value="new">
                            Use New Card
                        </label>
                    <?php endif; ?>
                    <div class="credit-card-input" id="new-card-input" style="<?php echo $savedCard ? 'display: none;' : ''; ?>">
                        <input type="text" id="card-number" placeholder="Card Number (e.g., 1234 5678 9012 3456)" maxlength="19">
                        <input type="text" id="expiry-date" placeholder="MM/YY" maxlength="5">
                        <input type="text" id="cvv" placeholder="CVV" maxlength="3">
                        <label><input type="checkbox" id="save-card" checked> Save card for future payments</label>
                    </div>
                    <div class="security-note">
                        <i class="fas fa-lock"></i>
                        Your payment information is securely encrypted.
                    </div>
                </div>
            </div>
        </div>
        <div class="payment">
            <button type="button">Confirm Payment</button>
        </div>
    </div>

    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <img id="payment_picture" src="" alt="Payment Status" class="payment_picture">
            <h2 id="paymentTitle">Payment Successful</h2>
            <p id="paymentMessage">You can view your payment history on the profile page.</p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const creditCardRadio = document.getElementById("creditcard");
            const creditCardField = document.getElementById("credit-card-field");
            const newCardInput = document.getElementById("new-card-input");
            const savedCardOption = document.querySelector('input[name="card-option"][value="saved"]');
            const newCardOption = document.querySelector('input[name="card-option"][value="new"]');
            const paymentButton = document.querySelector('.payment button');

            creditCardField.style.display = "none";

            creditCardRadio.addEventListener("change", function() {
                creditCardField.style.display = creditCardRadio.checked ? "block" : "none";
            });

            if (newCardOption) {
                newCardOption.addEventListener("change", function() {
                    newCardInput.style.display = newCardOption.checked ? "block" : "none";
                });
                savedCardOption.addEventListener("change", function() {
                    newCardInput.style.display = savedCardOption.checked ? "none" : "block";
                });
            }

            if (paymentButton) {
                paymentButton.addEventListener('click', validateForm);
            }

            fetch('get_notification.php')
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Notification data:', data);
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
                    console.error('Fetch notification error:', error);
                });

            fetch("checkout.php")
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log("Cart Data:", data);
                    const container = document.getElementById("cart-container");
                    let totalAmount = 0;
                    const hasPreviousEnrollment = <?php echo $hasPreviousEnrollment ? 'true' : 'false'; ?>;
                    let enrollmentFee = hasPreviousEnrollment ? 0 : 100;
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

                    document.getElementById("subject-fee").innerText = `RM${totalAmount.toFixed(2)}`;

                    console.log("Has Previous Enrollment:", hasPreviousEnrollment);
                    console.log("Enrollment Fee:", enrollmentFee);

                    const enrollmentFeeRow = document.getElementById("enrollment-fee-row");
                    if (hasPreviousEnrollment) {
                        enrollmentFeeRow.classList.add('hidden-row');
                    } else {
                        enrollmentFeeRow.classList.remove('hidden-row');
                    }

                    document.getElementById("total-amount").innerText = `RM${(totalAmount + enrollmentFee).toFixed(2)}`;
                })
                .catch(error => console.error("Error fetching cart data:", error));
        });

        async function validateForm() {
            console.log("validateForm called");
            const paymentButton = document.querySelector('.payment button');
            paymentButton.disabled = true; // Prevent multiple submissions

            const creditCardRadio = document.getElementById('creditcard');
            if (!creditCardRadio) {
                showToast("Payment option unavailable. Please try again later.", "error");
                paymentButton.disabled = false;
                return;
            }
            const creditCardSelected = creditCardRadio.checked;
            const paymentMethod = creditCardSelected ? "Credit Card" : "";
            
            console.log("creditCardSelected:", creditCardSelected);
            console.log("Payment Method:", paymentMethod);

            if (!paymentMethod) {
                console.log("No payment method selected");
                showToast("Please select a payment method.", "error");
                paymentButton.disabled = false;
                return;
            }

            let cardDetails = null;
            if (creditCardSelected) {
                const useSavedCard = document.querySelector('input[name="card-option"][value="saved"]')?.checked;
                if (!useSavedCard) {
                    const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
                    const expiryDate = document.getElementById('expiry-date').value;
                    const cvv = document.getElementById('cvv').value;
                    if (!cardNumber || !/^\d{16}$/.test(cardNumber)) {
                        showToast("Please enter a valid 16-digit card number.", "error");
                        paymentButton.disabled = false;
                        return;
                    }
                    if (!expiryDate || !/^\d{2}\/\d{2}$/.test(expiryDate)) {
                        showToast("Please enter a valid expiry date (MM/YY).", "error");
                        paymentButton.disabled = false;
                        return;
                    }
                    if (!cvv || !/^\d{3}$/.test(cvv)) {
                        showToast("Please enter a valid 3-digit CVV code.", "error");
                        paymentButton.disabled = false;
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

            let subjectTotal = 0;
            for (const item of cartItems) {
                try {
                    const priceResponse = await fetch(`get_subject_price.php?subject_id=${item.subject_id}`);
                    if (!priceResponse.ok) throw new Error(`HTTP error! Status: ${priceResponse.status}`);
                    const priceData = await priceResponse.json();
                    if (!priceData.price) throw new Error(`Invalid price for subject_id ${item.subject_id}`);
                    subjectTotal += parseFloat(priceData.price);
                } catch (error) {
                    console.error("Failed to fetch subject price:", error);
                    showToast("Unable to fetch subject price: " + error.message, "error");
                    paymentButton.disabled = false;
                    return;
                }
            }

            const isFirstEnrollment = <?php echo $hasPreviousEnrollment ? 'false' : 'true'; ?>;
            const enrollmentFee = isFirstEnrollment ? 100 : 0;
            const totalAmount = subjectTotal + enrollmentFee;

            if (cartItems.length === 0) {
                showToast("No items in the cart.", "error");
                paymentButton.disabled = false;
                return;
            }

            try {
                const response = await fetch("check_registration.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ cart_items: cartItems })
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const result = await response.json();
                console.log("Registration check response:", result);

                if (!result.success) {
                    showToast(result.message || "Some subjects are already registered for the selected child.", "error");
                    paymentButton.disabled = false;
                    return;
                }
            } catch (error) {
                console.error("Failed to check registration status:", error);
                showToast("Unable to check registration status: " + error.message, "error");
                paymentButton.disabled = false;
                return;
            }

            const orderData = {
                cart_items: cartItems,
                subject_total: subjectTotal,
                enrollment_fee: enrollmentFee,
                total_amount: totalAmount,
                payment_method: paymentMethod,
                phone: "<?php echo htmlspecialchars($userPhoneNumber); ?>",
                cart_ids: cartIds,
                card_details: cardDetails
            };

            console.log("Order data:", orderData);

            try {
                const response = await fetch("process_payment.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(orderData),
                    signal: AbortSignal.timeout(10000) // 10-second timeout
                });
                console.log("Payment response status:", response.status);
                console.log("Payment response headers:", response.headers.get("Content-Type"));
                const responseText = await response.text(); // Get raw response text
                console.log("Payment response text:", responseText);
                let data;
                try {
                    data = JSON.parse(responseText); // Attempt JSON parsing
                } catch (parseError) {
                    console.error("JSON parsing failed:", parseError);
                    throw new Error("Invalid JSON response: " + parseError.message);
                }

                if (data.success) {
                    showPaymentModal(true, data.payment_id);
                    setTimeout(() => { window.location.href = "subject.html"; }, 4000);
                } else {
                    showPaymentModal(false);
                    showToast(data.message || "Payment failed.", "error");
                }
            } catch (error) {
                console.error("Payment processing failed:", error);
                showPaymentModal(false);
                showToast("Payment processing failed: " + error.message, "error");
            } finally {
                paymentButton.disabled = false; // Re-enable button
            }
        }

        function showToast(message, type) {
            console.log("Showing toast:", message, type);
            if (!document.body) {
                console.error("Document body not available.");
                return;
            }
            const toast = document.getElementById('toast');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <img src="" alt="Toast image" class="toast-image">
                <div class="toast-message">${message}</div>
                <button class="toast-close" onclick="this.parentElement.classList.remove('show')">OK</button>
            `;
            if (type === 'error') {
                toast.querySelector('.toast-image').src = 'img/payment_ns.png';
            } else if (type === 'success') {
                toast.querySelector('.toast-image').src = 'img/payment_s.png';
            }
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function showPaymentModal(isSuccess, paymentId = '') {
            const modal = document.getElementById("paymentModal");
            const paymentIcon = document.getElementById("payment_picture");
            const paymentTitle = document.getElementById("paymentTitle");
            const paymentMessage = document.getElementById("paymentMessage");
            if (isSuccess) {
                paymentIcon.src = "img/payment_s.png";
                paymentTitle.innerText = "Payment Successful";
                paymentMessage.innerText = `Payment ID: ${paymentId || 'Unknown'}. You can view your payment history on the profile page.`;
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

    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Quick Links</h4>
                    <a class="btn btn-link" href="about.html">About Us</a><br>
                    <a class="btn btn-link" href="contact.html">Contact Us</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-4">Contact Info</h4>
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

    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow.min.js"></script>
    <script src="lib/easing.min.js"></script>
    <script src="lib/waypoints.min.js"></script>
    <script src="lib/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>