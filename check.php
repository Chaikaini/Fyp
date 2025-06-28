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

// Get phone number and saved credit cards using parent_id
$userPhoneNumber = "";
$savedCards = [];
$parent_id = $_SESSION['parent_id'];

// Debug: Log parent_id
error_log("Session parent_id: " . $parent_id);

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

// Get all saved credit cards
$stmt = $conn->prepare("SELECT credit_card_id, last_four, expiry_date FROM credit_cards WHERE parent_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $savedCards[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Check Out</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="The Seeds Learning Centre, Check Out" name="keywords">
    <meta content="The Seeds Learning Centre | Check Out" name="description">
    <!-- Disable cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link href="img/the_seeds.jpg" rel="icon" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
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
            z-index: 11000;
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
            position: relative;
        }
        .modal-content button {
            pointer-events: auto;
            cursor: pointer;
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

        .toast.warning .toast-image {
            content: url('img/warning.png');
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
    </style>
</head>

<body>
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="member.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
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
            </div>
            <a href="logout.php" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Log out<i class="fa fa-arrow-right ms-3"></i></a>
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
                        <input type="radio" id="creditcard" name="payment" value="Credit Card" checked>
                    </label>
                </div>
                <div id="credit-card-field" style="display: block;">
                    <label>Step 2: Select or Enter Credit Card Details</label>
                    <?php if ($savedCards): ?>
                        <?php foreach ($savedCards as $index => $card): ?>
                            <div class="saved-card">
                                <label>
                                    <input type="radio" name="card-option" value="<?php echo htmlspecialchars($card['credit_card_id']); ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                    Use Saved Card: **** **** **** <?php echo htmlspecialchars($card['last_four']); ?> (Expires: <?php echo htmlspecialchars($card['expiry_date']); ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div class="saved-card">
                            <label>
                                <input type="radio" name="card-option" value="new">
                                Use New Card
                            </label>
                        </div>
                    <?php else: ?>
                        <div class="saved-card">
                            <label>
                                <input type="radio" name="card-option" value="new" checked>
                                Use New Card
                            </label>
                        </div>
                    <?php endif; ?>
                    <div class="credit-card-input" id="new-card-input" style="<?php echo $savedCards ? 'display: none;' : 'display: block;'; ?>">
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

    <div id="toast" class="toast">
        <img class="toast-image" src="" alt="Toast Status">
        <p class="toast-message"></p>
        <button class="toast-close">OK</button>
    </div>

    <script>
        // Check login status and load content
        async function checkLoginStatus() {
            try {
                const response = await fetch('check_login.php', {
                    credentials: 'include',
                    cache: 'no-store'
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                console.log('Login check response:', data);

                if (!data.isLoggedIn) {
                    window.location.href = 'login.html';
                }
            } catch (error) {
                console.error('Error fetching login status:', error);
                window.location.href = 'login.html';
            }
        }

        // Load navigation dynamically
        async function loadNavigation() {
            try {
                const response = await fetch('check_login.php', { credentials: 'include', cache: 'no-store' });
                if (!response.ok) throw new Error('Failed to fetch login status');
                const data = await response.json();

                const navLinks = document.getElementById('navbarCollapse').querySelector('.navbar-nav');
                const authButton = document.getElementById('navbarCollapse').querySelector('.btn');

                if (data.isLoggedIn) {
                    navLinks.innerHTML = `
                        <a href="member.html" class="nav-item nav-link">Home</a>
                        <a href="subject.html" class="nav-item nav-link">Subject</a>
                        <a href="about.html" class="nav-item nav-link">About us</a>
                        <a href="contact.html" class="nav-item nav-link">Contact us</a>
                    `;
                    authButton.textContent = 'Log out';
                    authButton.href = 'logout.php';
                } else {
                    navLinks.innerHTML = `
                        <a href="888.html" class="nav-item nav-link">Home</a>
                        <a href="about.html" class="nav-item nav-link">About us</a>
                        <a href="contact.html" class="nav-item nav-link">Contact us</a>
                    `;
                    authButton.textContent = 'Log in';
                    authButton.href = 'login.html';
                }
            } catch (error) {
                console.error('Error fetching login status:', error);
                const navLinks = document.getElementById('navbarCollapse').querySelector('.navbar-nav');
                const authButton = document.getElementById('navbarCollapse').querySelector('.btn');
                navLinks.innerHTML = `
                    <a href="888.html" class="nav-item nav-link">Home</a>
                    <a href="about.html" class="nav-item nav-link">About us</a>
                    <a href="contact.html" class="nav-item nav-link">Contact us</a>
                `;
                authButton.textContent = 'Log in';
                authButton.href = 'login.html';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize page
            checkLoginStatus();
            loadNavigation();

            const creditCardRadio = document.getElementById("creditcard");
            const creditCardField = document.getElementById("credit-card-field");
            const newCardInput = document.getElementById("new-card-input");
            const cardOptions = document.querySelectorAll('input[name="card-option"]');
            const paymentButton = document.querySelector('.payment button');

            // Toggle new card input based on card option
            cardOptions.forEach(option => {
                option.addEventListener("change", function() {
                    newCardInput.style.display = option.value === "new" ? "block" : "none";
                });
            });

            // Default to showing credit card field since it's the only option
            creditCardRadio.checked = true;
            creditCardField.style.display = "block";

            if (paymentButton) {
                paymentButton.addEventListener('click', validateForm);
            }

            // Card number formatting and validation
            const cardNumberInput = document.getElementById('card-number');
            let inputTimeout = null;
            cardNumberInput.addEventListener('input', function(e) {
                clearTimeout(inputTimeout);
                inputTimeout = setTimeout(() => {
                    console.log('Card number input event triggered. Raw value:', e.target.value);
                    
                    const cursorPosition = e.target.selectionStart;
                    const previousValue = e.target.value;
                    
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 16) value = value.slice(0, 16);
                    console.log('Cleaned digits:', value);

                    let formattedValue = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i > 0 && i % 4 === 0) formattedValue += ' ';
                        formattedValue += value[i];
                    }
                    console.log('Formatted card number:', formattedValue);

                    e.target.value = formattedValue;

                    const newLength = formattedValue.length;
                    const oldLength = previousValue.length;
                    const spacesBeforeCursor = (cursorPosition > 0 ? Math.floor((cursorPosition - 1) / 4) : 0);
                    const newCursorPosition = cursorPosition + (newLength - oldLength) + spacesBeforeCursor;
                    e.target.setSelectionRange(newCursorPosition, newCursorPosition);

                    e.target.setCustomValidity('');
                }, 50);
            });

            cardNumberInput.addEventListener('blur', function() {
                console.log('Card number blur event triggered. Value:', this.value);
                const digits = this.value.replace(/\D/g, '');
                if (digits.length > 0 && digits.length < 16) {
                    showToast('error', 'Please enter a valid 16-digit card number.');
                    this.value = '';
                    this.setCustomValidity('Invalid card number');
                } else {
                    this.setCustomValidity('');
                }
            });

            // Expiry date validation
            const expiryDateInput = document.getElementById('expiry-date');
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1;
            const currentYear = currentDate.getFullYear() % 100;
            const maxYear = 36; // Restrict to 2036

            let expiryTimeout = null;
            expiryDateInput.addEventListener('input', function(e) {
                console.log('Input event triggered. Raw value:', e.target.value);
                clearTimeout(expiryTimeout);

                expiryTimeout = setTimeout(() => {
                    let value = e.target.value.replace(/[^\d\/]/g, '');
                    if (value.length > 5) value = value.slice(0, 5);
                    const digits = value.replace(/\D/g, '');
                    console.log('Processed digits:', digits);

                    let formattedValue = '';
                    if (digits.length === 0) {
                        formattedValue = '';
                    } else if (digits.length <= 2) {
                        formattedValue = digits;
                    } else {
                        const month = digits.slice(0, 2);
                        const year = digits.slice(2);
                        formattedValue = month + (year ? '/' + year : '');
                    }
                    console.log('Formatted value:', formattedValue, 'Raw digits:', digits);
                    e.target.value = formattedValue;

                    e.target.setCustomValidity('');

                    if (formattedValue.length === 5 && formattedValue.match(/^\d{2}\/\d{2}$/) && digits.length === 4) {
                        const [inputMonth, inputYear] = formattedValue.split('/').map(num => parseInt(num));
                        console.log('Validating:', { inputMonth, inputYear });
                        if (isNaN(inputMonth) || isNaN(inputYear)) {
                            e.target.setCustomValidity('Invalid date format (MM/YY).');
                            showToast('error', 'Invalid date format (MM/YY).');
                            e.target.value = '';
                            return;
                        }
                        const isValid = validateExpiryDate(inputMonth, inputYear);
                        if (!isValid) {
                            e.target.setCustomValidity('Please enter a valid future date (MM/YY) not exceeding 12/36.');
                            showToast('error', 'Expiry date must be in the future and not exceed 12/36 (MM/YY).');
                            e.target.value = '';
                        } else {
                            e.target.setCustomValidity('');
                        }
                    }
                }, 100);
            });

            expiryDateInput.addEventListener('blur', function() {
                console.log('Blur event triggered. Value:', this.value);
                const value = this.value;
                if (value.length === 5 && value.match(/^\d{2}\/\d{2}$/)) {
                    const [inputMonth, inputYear] = value.split('/').map(num => parseInt(num));
                    if (isNaN(inputMonth) || isNaN(inputYear)) {
                        this.setCustomValidity('Invalid date format (MM/YY).');
                        showToast('error', 'Invalid date format (MM/YY).');
                        this.value = '';
                        return;
                    }
                    const isValid = validateExpiryDate(inputMonth, inputYear);
                    if (!isValid) {
                        this.setCustomValidity('Please enter a valid future date (MM/YY) not exceeding 12/36.');
                        showToast('error', 'Expiry date must be in the future and not exceed 12/36 (MM/YY).');
                        this.value = '';
                    } else {
                        this.setCustomValidity('');
                    }
                } else if (value.length > 0) {
                    showToast('error', 'Please enter a complete date in MM/YY format.');
                    this.value = '';
                    this.setCustomValidity('');
                }
            });

            function validateExpiryDate(month, year) {
                console.log('validateExpiryDate:', { month, year, currentMonth, currentYear, maxYear });
                if (month < 1 || month > 12) return false;
                if (year < currentYear || year > maxYear) return false;
                const inputDate = new Date(`20${year}-${month}-01`);
                const isFuture = inputDate > currentDate;
                console.log('Date comparison:', { inputDate, currentDate, isFuture });
                return isFuture;
            }

            // Fetch notifications
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
                        if (badge) badge.style.display = 'block';
                    } else {
                        console.log('No unread notifications');
                    }
                })
                .catch(error => console.error('Fetch notification error:', error));

            // Fetch cart data
            fetch("checkout.php", {
                credentials: 'include',
                cache: 'no-store'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Invalid JSON:', text);
                            throw new Error('Invalid JSON response from checkout.php');
                        }
                    });
                })
                .then(data => {
                    console.log("Cart Data:", data);
                    if (data.status !== 'success') {
                        throw new Error(data.message || 'Failed to load cart data');
                    }
                    const cartItems = data.cart || [];
                    const container = document.getElementById("cart-container");
                    if (!Array.isArray(cartItems) || cartItems.length === 0) {
                        container.innerHTML = "<p>Your cart is empty. Please add items to proceed.</p>";
                        document.getElementById("subject-fee").innerText = "RM0";
                        document.getElementById("total-amount").innerText = "RM0";
                        document.querySelector('.payment button').disabled = true;
                        return;
                    }
                    let totalAmount = 0;
                    const enrollmentFee = 100; // Always include enrollment fee
                    container.innerHTML = "";
                    cartItems.forEach(item => {
                        totalAmount += parseFloat(item.price || 0);
                        // Normalize subject_image path
                        const imagePath = (item.subject_image || 'images/default.png').replace(/\\/g, '/');
                        const courseItem = `
                            <div class="course-item" 
                                 data-cart-id="${item.cart_id}" 
                                 data-class-id="${item.class_id}" 
                                 data-child-id="${item.child_id}" 
                                 data-subject-id="${item.subject_id}" 
                                 data-teacher-id="${item.teacher_id}">
                                <img src="${imagePath}" alt="${item.subject_name || 'Subject'}">
                                <div>
                                    <p><b>${item.subject_name || 'Unknown Subject'}</b></p>
                                    <p><b>Teacher:</b> ${item.teacher_name || 'Unknown Teacher'}</p>
                                    <p><b>Price:</b> RM${parseFloat(item.price || 0).toFixed(2)}</p>
                                    <p><b>Time:</b> ${item.class_time || 'N/A'}</p>
                                    <p><b>Child:</b> ${item.child_name || 'Unknown Student'}</p>
                                </div>
                            </div>
                            <hr>
                        `;
                        container.innerHTML += courseItem;
                    });
                    document.getElementById("subject-fee").innerText = `RM${totalAmount.toFixed(2)}`;
                    document.getElementById("enrollment-fee-row").classList.remove('hidden-row'); // Always show enrollment fee
                    document.getElementById("total-amount").innerText = `RM${(totalAmount + enrollmentFee).toFixed(2)}`;
                })
                .catch(error => {
                    console.error("Error fetching cart data:", error.message);
                    console.error("Full error:", error);
                    const container = document.getElementById("cart-container");
                    container.innerHTML = `<p>Error loading cart data: ${error.message}. Please try again later.</p>`;
                    document.querySelector('.payment button').disabled = true;
                });

            async function validateForm() {
                console.log("validateForm called");
                const paymentButton = document.querySelector('.payment button');
                paymentButton.disabled = true;

                const creditCardRadio = document.getElementById('creditcard');
                if (!creditCardRadio || !creditCardRadio.checked) {
                    showToast('error', "Please select a payment method.");
                    paymentButton.disabled = false;
                    return;
                }

                let cardDetails = null;
                const selectedCardOption = document.querySelector('input[name="card-option"]:checked');
                if (!selectedCardOption) {
                    showToast('error', "Please select a credit card or enter new card details.");
                    paymentButton.disabled = false;
                    return;
                }

                if (selectedCardOption.value !== "new") {
                    cardDetails = { credit_card_id: parseInt(selectedCardOption.value) };
                } else {
                    const cardNumber = document.getElementById('card-number').value.replace(/\D/g, '');
                    const expiryDate = document.getElementById('expiry-date').value;
                    const cvv = document.getElementById('cvv').value;

                    if (!cardNumber || cardNumber.length !== 16) {
                        showToast('error', "Please enter a valid 16-digit card number.");
                        paymentButton.disabled = false;
                        return;
                    }
                    if (!expiryDate || !/^\d{2}\/\d{2}$/.test(expiryDate)) {
                        showToast('error', "Please enter a valid expiry date (MM/YY).");
                        paymentButton.disabled = false;
                        return;
                    }
                    if (!cvv || !/^\d{3}$/.test(cvv)) {
                        showToast('error', "Please enter a valid 3-digit CVV code.");
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

                let cartItems = Array.from(document.querySelectorAll(".course-item")).map(item => ({
                    cart_id: item.getAttribute("data-cart-id"),
                    class_id: item.getAttribute("data-class-id"),
                    child_id: item.getAttribute("data-child-id"),
                    subject_id: item.getAttribute("data-subject-id"),
                    teacher_id: item.getAttribute("data-teacher-id")
                }));
                const cartIds = cartItems.map(item => item.cart_id).join(",");

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
                        showToast('error', result.message || "Some subjects are already registered for the selected child.");
                        paymentButton.disabled = false;
                        return;
                    }

                    if (result.conflicts && result.conflicts.length > 0) {
                        const conflictMessage = result.conflicts.map(conflict => conflict.message).join("\n");
                        showToast('error', conflictMessage);

                        result.conflicts.forEach(conflict => {
                            const itemToRemove = document.querySelector(
                                `.course-item[data-child-id="${conflict.child_id}"][data-subject-id="${conflict.subject_id}"]`
                            );
                            if (itemToRemove) {
                                itemToRemove.remove();
                                const hr = itemToRemove.nextElementSibling;
                                if (hr && hr.tagName === 'HR') hr.remove();
                            }
                        });

                        let newTotal = 0;
                        const remainingItems = Array.from(document.querySelectorAll(".course-item"));
                        remainingItems.forEach(item => {
                            const priceElement = item.querySelector('p:nth-child(3)').textContent;
                            const price = parseFloat(priceElement.replace('Price: RM', ''));
                            newTotal += price;
                        });
                        document.getElementById("subject-fee").innerText = `RM${newTotal.toFixed(2)}`;
                        const enrollmentFee = 100; // Always include enrollment fee
                        document.getElementById("total-amount").innerText = `RM${(newTotal + enrollmentFee).toFixed(2)}`;
                    }

                    cartItems = result.valid_items || cartItems;
                    if (cartItems.length === 0) {
                        showToast('error', "No valid items to process after checking registrations.");
                        paymentButton.disabled = false;
                        return;
                    }
                } catch (error) {
                    console.error("Error checking registration:", error);
                    showToast('error', "Unable to check registration status: " + error.message);
                    paymentButton.disabled = false;
                    return;
                }

                let subjectTotal = 0;
                for (const item of cartItems) {
                    try {
                        const priceResponse = await fetch(`get_subject_price.php?subject_id=${item.subject_id}`);
                        if (!priceResponse.ok) throw new Error(`HTTP error! Status: ${priceResponse.status}`);
                        const priceData = await priceResponse.json();
                        if (!priceData.price) throw new Error(`Invalid price for subject_id ${item.subject_id}`);
                        subjectTotal += parseFloat(priceData.price);
                    } catch (error) {
                        console.error("Error fetching subject price:", error);
                        showToast('error', "Unable to fetch subject price: " + error.message);
                        paymentButton.disabled = false;
                        return;
                    }
                }

                const enrollmentFee = 100; // Always include enrollment fee
                const totalAmount = subjectTotal + enrollmentFee;

                const orderData = {
                    cart_items: cartItems,
                    subject_total: subjectTotal,
                    enrollment_fee: enrollmentFee,
                    total_amount: totalAmount,
                    payment_method: "Credit Card",
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
                        signal: AbortSignal.timeout(10000)
                    });
                    console.log("Payment response status:", response.status);
                    console.log("Payment response headers:", response.headers.get("Content-Type"));
                    const responseText = await response.text();
                    console.log("Payment response text:", responseText);
                    let data;
                    try {
                        data = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error("JSON parsing failed:", parseError);
                        throw new Error("Invalid JSON response: " + parseError.message);
                    }

                    if (data.success) {
                        showPaymentModal(true, data.payment_id);
                    } else {
                        showToast('error', data.message || "Payment failed.");
                    }
                } catch (error) {
                    console.error("Payment processing failed:", error);
                    showToast('error', "Payment processing failed: " + error.message);
                } finally {
                    paymentButton.disabled = false;
                }
            }

            function showToast(type, message) {
                console.log("showToast called:", { type, message });
                const toast = document.getElementById("toast");
                const toastImage = toast.querySelector('.toast-image');
                const toastMessage = toast.querySelector('.toast-message');
                const toastClose = toast.querySelector('.toast-close');

                toast.className = `toast ${type} show`;
                toastImage.src = type === 'success' ? 'img/payment_s.png' : type === 'error' ? 'img/payment_ns.png' : 'img/warning.png';
                toastImage.onerror = () => { toastImage.src = "https://via.placeholder.com/80"; };
                toastMessage.innerText = message;

                toast.style.display = 'block';

                toastClose.onclick = () => {
                    toast.className = `toast ${type}`;
                    setTimeout(() => { toast.style.display = 'none'; }, 300);
                };

                setTimeout(() => {
                    toast.className = `toast ${type}`;
                    setTimeout(() => { toast.style.display = 'none'; }, 300);
                }, 5000);
            }

            function showPaymentModal(isSuccess, message = '') {
                console.log("showPaymentModal called:", { isSuccess, message });
                const modal = document.getElementById("paymentModal");
                const paymentIcon = document.getElementById("payment_picture");
                const paymentTitle = document.getElementById("paymentTitle");
                const paymentMessage = document.getElementById("paymentMessage");
                const okButton = document.querySelector('#paymentModal button');
                if (isSuccess) {
                    paymentIcon.src = "img/payment_s.png";
                    paymentIcon.onerror = () => { paymentIcon.src = "https://via.placeholder.com/80"; };
                    paymentTitle.innerText = "Payment Successful";
                    paymentMessage.innerText = `You can view your payment history on the profile page.`;
                    okButton.style.display = 'none';
                    setTimeout(() => {
                        modal.style.display = "none";
                        window.location.href = "subject.html";
                    }, 5000);
                } else {
                    paymentIcon.src = "img/payment_ns.png";
                    paymentIcon.onerror = () => { paymentIcon.src = "https://via.placeholder.com/80"; };
                    paymentTitle.innerText = "Payment Failed";
                    paymentMessage.innerText = message || "There was an issue processing your payment.";
                    okButton.style.display = 'block';
                }
                modal.style.display = "flex";
                console.log("Modal display set to flex");
            }

            function closeModal() {
                console.log("closeModal called");
                document.getElementById("paymentModal").style.display = "none";
            }

            document.querySelector('#paymentModal button').addEventListener('click', () => {
                console.log('OK button clicked');
                closeModal();
            });
        });
    </script>

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <!-- Brand Slogan -->
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
                    <!-- Social Media -->
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

    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow.min.js"></script>
    <script src="lib/easing.min.js"></script>
    <script src="lib/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>