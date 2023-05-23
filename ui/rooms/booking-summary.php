<?php
require_once __DIR__ . "/../../data/model/User.php";
require_once __DIR__ . '/../../data/repository/RoomRepository.php';
require_once __DIR__ . '/../../data/service/BookingService.php';
require_once __DIR__ . '/../../data/repository/UserRepository.php';
session_start(); // Start session

$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] === false) {
    // Redirect to home page or any other authorized page
    header('Location: ../profile/login.php');
    exit;
}

$roomRepository = new RoomRepository(__DIR__ . '/../../data/rooms.json');
$bookingService = new BookingService($userRepository);

$currentLoggedInUser = unserialize($_SESSION['user']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required data is present
    if (isset($_POST['room_id'], $_POST['contact'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['guest_no'], $_POST['special_request'], $_POST['email'], $_POST['price'], $_POST['booking_id'])) {
        $roomId = (int)$_POST['room_id'];
        $arrivalDateString = $_POST['arrival_date'];
        $departureDateString = $_POST['departure_date'];
        $guestNumber = $_POST['guest_no'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $specialRequest = $_POST['special_request'];
        $price = $_POST['price'];
        $stayDuration = calculateStayDuration($arrivalDateString, $departureDateString);
        $bookingId = $_POST['booking_id'];

        if(!$bookingService->isBookingIdUnique($bookingId)) {
            // Required data not present, redirect to home
            header('Location: ../');
            exit;
        }
    } else {
        // Required data not present, redirect to home
        header('Location: ../');
        exit;
    }
} else {
    // Redirect to home if accessed directly without POST request
    header('Location: ../profile/');
    exit;
}

function calculateStayDuration($arrivalDate, $departureDate) {
    // Create DateTime objects from the date strings
    $arrivalDateTime = DateTime::createFromFormat('Y-m-d', $arrivalDate);
    $departureDateTime = DateTime::createFromFormat('Y-m-d', $departureDate);

    // Calculate the stay duration
    $interval = $arrivalDateTime->diff($departureDateTime);

    $stayDuration = '';
    if ($interval->y > 0) {
        $stayDuration .= $interval->y . ' year';
        if ($interval->y > 1) {
            $stayDuration .= 's';
        }
    }

    if ($interval->m > 0) {
        if (!empty($stayDuration)) {
            $stayDuration .= ' ';
        }
        $stayDuration .= $interval->m . ' month';
        if ($interval->m > 1) {
            $stayDuration .= 's';
        }
    }

    if ($interval->d > 0 || empty($stayDuration)) {
        if (!empty($stayDuration)) {
            $stayDuration .= ' ';
        }
        $stayDuration .= $interval->d . ' day';
        if ($interval->d > 1) {
            $stayDuration .= 's';
        }
    }

    return $stayDuration;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="../../res/images/site_logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="../../css/theme/theme.css">
    <link rel="stylesheet" href="../../css/booking-summary.css">
    <link rel="stylesheet" href="../../css/booking-summary-mobile.css">
    <title>Booking Summary</title>
</head>

<body>
    <div class="column-container">
        <nav class="navbar">
            <div class="navbar-content">
                <div class="navbar-start">
                    <a href="../" class="navbar-logo">
                        <img src="../../res/images/site_logo.svg" alt="Logo">
                    </a>
                    <a class="navbar-navigation-icon hidden">
                        <img src="../../res/images/arrow_back.svg" alt="Logo">
                    </a>
                </div>
                <div class="navbar-center">
                    <h2 class="progress hidden">Payment Confirmation</h2>
                    <div class="progress-bar">
                        <div class="progress-item active">
                            <div class="progress-circle">✔</div>
                            <div class="progress-text">Booking Information</div>
                        </div>
                        <div class="progress-item active">
                            <div class="progress-line"></div>
                            <div class="progress-circle">✔</div>
                            <div class="progress-text">Payment Information</div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-line"></div>
                            <div class="progress-circle">3</div>
                            <div class="progress-text">Booking Confirmation</div>
                        </div>
                    </div>
                </div>
                <div class="navbar-end">
                    <button class="navbar-menu hidden" id="drawer-toggle">
                        <img src="../../res/images/menu.svg" alt="Navigation menu icon">
                    </button>
                    <button class="navbar-profile-btn">
                        <img src="../../res/images/person.png" alt="Profile icon">
                        <span class="md-24 material-icons-outlined">arrow_drop_down</span>
                    </button>
                    <div class="navbar-dropdown">
                        <div class="column-container">
                            <a href="../profile/index.php" class="navbar-dropdown-item">
                                <div class="row-container center-horizontal">
                                    <span class="material-icons navbar-dropdown-item-icon">account_circle</span>
                                    Profile
                                </div>
                            </a>
                            <a href="../profile/edit-profile.php" class="navbar-dropdown-item">
                                <div class="row-container center-horizontal">
                                    <span class="material-icons navbar-dropdown-item-icon">settings</span>
                                    Settings
                                </div>
                            </a>
                            <a href="../profile/booking-history.php" class="navbar-dropdown-item">
                                <div class="row-container center-horizontal">
                                    <span class="material-icons navbar-dropdown-item-icon">auto_stories</span>
                                    My Bookings
                                </div>
                            </a>
                            <a href="../profile/logout.php" class="navbar-dropdown-item">
                                <div class="card" style="--card-width: auto">
                                    <div class="card-content">
                                        <div class="column-container center">
                                            <span class="text-center">Log Out</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <h1 class="display-medium text-center booking-summary-header">Booking Summary</h1>
        <div class="row-container content center">
            <div class="wrap-content">
                <div class="column-container booking-summary-info">
                    <div class="row-container">
                        <div class="body-large label-margin">
                            <span>Duration:</span>
                            <span id="duration" class="font-medium"><?php echo $stayDuration; ?></span>
                        </div>
                    </div>

                    <!-- Divider/Line -->
                    <div class="horizontal-divider "></div>

                    <div class="row-container">
                        <div class="body-large label-margin">
                            <span>Guest Number:</span>
                            <span id="duration" class="font-medium"><?php echo $guestNumber; ?></span>
                        </div>
                    </div>
                    <!-- Divider/Line -->
                    <div class="horizontal-divider "></div>

                    <div class="row-container">
                        <div class="body-large label-margin">
                            <span>Room:</span>
                            <span id="duration" class="font-medium"><?php 
                            
                            foreach ($roomRepository->getAllRooms() as $category => $rooms) {
                                foreach ($rooms as $room) {
                                    if($room["id"] == $roomId) {
                                        echo $room["name"]." - ".strtoupper($category);
                                        break;
                                    }
                                }
                            }

                            ?></span>
                        </div>
                    </div>
                    <!-- Divider/Line -->
                    <div class="horizontal-divider"></div>

                    <div class="row-container   label-margin">
                        <div class="row-container fill-parent">
                            <div class="body-large">
                                <span>Contact #:</span>
                                <span id="duration" class="font-medium"><?php echo $contact; ?></span>
                            </div>
                        </div>
                        <div class="row-container fill-parent">
                            <div class="body-large">
                                <span>Email:</span>
                                <span id="email" class="font-medium"><?php echo $email; ?></span>
                            </div>
                        </div>
                    </div>
                    <!-- Divider/Line -->
                    <div class="horizontal-divider "></div>

                    <span class="label-margin">Special Request:</span>
                    <div class="outlined-textfield-container fill-parent" style="--textfield-width: 100%">
                        <textarea class="outlined-textfield-input" name="special-request" placeholder="Note your special requests" rows="5" readonly><?php echo $specialRequest; ?></textarea>
                    </div>
                    <div class="row-container">
                        <div class="display-small label-margin">
                            <span>Price:</span>
                            <span id="price">₱<?php echo number_format($price, 0, '.', ','); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column-container stick-on-top payment-container">
                <h3 class="title-large font-bold text-center">Payment Method</h3>
                <div class="card payment-card">
                    <div class="card-content">
                        <form id="form" action="./booking-complete.php" method="POST">
                            <input name="booking_id" value="<?php echo $bookingId; ?>" hidden/>
                            <input name="arrival_date" value="<?php echo $arrivalDateString; ?>" hidden required />
                            <input name="departure_date" value="<?php echo $departureDateString; ?>" hidden required />
                            <input name="room_id" value="<?php echo $roomId; ?>" hidden required />
                            <input type="number" name="price" value="<?php echo $price; ?>" hidden required />
                            <input name="contact" value="<?php echo $contact; ?>" hidden required />
                            <label class="font-medium">Payment method: <span class="error-text">*</span></label>
                            <div class="select-container">
                                <select id="payment-method" name="payment-method" class="select">
                                    <option value="">Select a payment method</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="card">Card</option>
                                    <option value="wire">Wire Transfer</option>
                                </select>
                            </div>

                            <div class="textfield-container payment-field hidden" id="gcash-field">
                                <input type="text" name="gcash_number" class="textfield-input ewallet-number" placeholder=" " required />
                                <label class="textfield-label">Account Number <span class="error-text">*</span></label>
                                <div class="textfield-underline"></div>
                                <span class="error-message">Invalid Phone Number</span>
                            </div>

                            <div class="textfield-container payment-field hidden" id="paymaya-field">
                                <input type="text" name="paymaya_number" class="textfield-input ewallet-number" placeholder=" " required />
                                <label class="textfield-label">Account Number <span class="error-text">*</span></label>
                                <div class="textfield-underline"></div>
                                <span class="error-message">Invalid Phone Number</span>
                            </div>

                            <div class="textfield-container payment-field hidden" id="paypal-field">
                                <input type="email" name="paypal_number" id="paypal-email" class="textfield-input" placeholder=" " required />
                                <label class="textfield-label">PayPal Email <span class="error-text">*</span></label>
                                <div class="textfield-underline"></div>
                                <span class="error-message">Invalid Email</span>
                            </div>

                            <div id="card-field" class="column-container payment-field hidden">
                                <div class="textfield-container field-margin">
                                    <input type="text" id="cc" name="card_number" class="textfield-input" placeholder=" " required />
                                    <label class="textfield-label">Card Number (MC/Visa) <span class="error-text">*</span></label>
                                    <div class="textfield-underline"></div>
                                    <span class="error-message">Invalid CC Number</span>
                                </div>
                                <div class="row-container field-margin">
                                    <div class="textfield-container fill-parent field-margin-1" style="--textfield-width: 80%;">
                                        <input type="text" id="exp-month" name="month" class="textfield-input" placeholder=" " required />
                                        <label class="textfield-label">Month <span class="error-text">*</span></label>
                                        <div class="textfield-underline"></div>
                                        <span class="error-message">Invalid month</span>
                                    </div>
                                    <div class="textfield-container fill-parent" style="--textfield-width: 80%">
                                        <input type="text" id="exp-year" name="year" class="textfield-input" placeholder=" " required />
                                        <label class="textfield-label">Year <span class="error-text">*</span></label>
                                        <div class="textfield-underline"></div>
                                        <span class="error-message">Invalid year</span>
                                    </div>
                                </div>
                                <div class="textfield-container field-margin">
                                    <input type="text" name="cvv" id="cvv" class="textfield-input" placeholder=" " required />
                                    <label class="textfield-label">CVV <span class="error-text">*</span></label>
                                    <div class="textfield-underline"></div>
                                    <span class="error-message">Invalid CVV</span>
                                </div>
                            </div>

                            <div id="wire-field" class="column-container payment-field hidden">
                                <div class="textfield-container field-margin">
                                    <input type="text" name="bank_name" class="textfield-input" placeholder=" " required />
                                    <label class="textfield-label">Bank Name <span class="error-text">*</span></label>
                                    <div class="textfield-underline"></div>
                                </div>
                                <div class="textfield-container field-margin">
                                    <input type="text" name="acc_name" class="textfield-input" placeholder=" " required />
                                    <label class="textfield-label">Account Name <span class="error-text">*</span></label>
                                    <div class="textfield-underline"></div>
                                </div>
                                <div class="textfield-container field-margin">
                                    <input type="text" name="wire_number" class="textfield-input" placeholder=" " required />
                                    <label class="textfield-label">Account Number <span class="error-text">*</span></label>
                                    <div class="textfield-underline"></div>
                                </div>
                            </div>

                            <div id="submit-container" class="row-container button-container center payment-field hidden">
                                <button type="submit" id="confirm" class="button field-margin-1" style="--button-size: 0.65rem 1rem">Confirm</button>
                                <button id="cancel" class="button field-margin-1 cancel-button" style="--button-size: 0.65rem 1rem">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="drawer" class="drawer hidden">
        <div class="column-container drawer-menu">
            <a href="" class="drawer-logo">
                <img src="<?php

                            if ($currentLoggedInUser != null) {
                                echo $currentLoggedInUser->getProfilePicture();
                            } else {
                                echo "../../res/images/image-placeholder.svg";
                            }

                            ?>" alt="Logo">
            </a>
            <a href="../" class="navbar-dropdown-item">
                <div class="row-container center-horizontal">
                    <span class="material-icons navbar-dropdown-item-icon">account_circle</span>
                    Profile
                </div>
            </a>
            <a href="../profile/edit-profile.php" class="navbar-dropdown-item">
                <div class="row-container center-horizontal">
                    <span class="material-icons navbar-dropdown-item-icon">settings</span>
                    Settings
                </div>
            </a>
            <a href="../profile/booking-history.php" class="navbar-dropdown-item">
                <div class="row-container center-horizontal">
                    <span class="material-icons navbar-dropdown-item-icon">auto_stories</span>
                    My Bookings
                </div>
            </a>
            <a href="../profile/logout.php" class="navbar-dropdown-item">
                <div class="card" style="--card-width: auto">
                    <div class="card-content">
                        <div class="column-container center">
                            <span class="text-center">Log Out</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div id="overlay" class="overlay"></div>
    <div id="snackbar" class="error-snackbar">Error: Something went wrong.</div>
    <footer class="main-footer">
        <div class="row-container footer-content">
            <div class="column-container company-info">
                <p>Hotel Name: Grand Eden Oasis</p>
                <p>Address: 123 Main Street, Anytown USA</p>
                <p>Phone: (123) 456-7890</p>
                <p>Email: info@geoasis.com</p>
                <p>Website: www.geoasis.com</p>
                <p>Site Developed by OneTed Devs</p>
            </div>
            <div class="column-container other-info">
                <p><a href="../others/about-us.php" class="on-primary-text">About Us</a></p>
                <p><a href="../others/faqs.php" class="on-primary-text">FAQs</a></p>
            </div>
            <a href="../">
                <div class="column-container center">
                    <div class="elevation-2 footer-logo"></div>
                    <h2 class="surface-text">Grand Eden Oasis</h2>
                </div>
            </a>
        </div>
        <div class="column-container center copyright-text">
            <p>&copy; Grand Eden Oasis. All Rights Reserved.</p>
        </div>
    </footer>
</body>
<script src="../../scripts/navbar.js"></script>
<script src="../../scripts/snackbar.js"></script>
<script src="../../scripts/validators.js"></script>
<script src="../../scripts/booking/booking-summary.js"></script>
<script>
    const navigationButton = document.querySelector(".navbar-navigation-icon");
	navigationButton.addEventListener("click", backToFirstForm);
    document.getElementById('cancel').addEventListener("click", backToFirstForm);

    function backToFirstForm(e) {
		e.preventDefault();
        window.location.href = 'booking-form.php';
    }
</script>
</html>