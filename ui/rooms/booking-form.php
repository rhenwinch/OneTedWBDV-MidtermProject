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
$roomTypes = array_keys($roomRepository->getAllRooms());

$currentLoggedInUser = unserialize($_SESSION['user']);

// If is navigating back but the referer is invalid
if(isset($_SESSION['form_1']) && isset($_SERVER['HTTP_REFERER']) && isset($_SESSION['form_1']) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== "http://localhost/ui/rooms/booking-summary.php") {
    unset($_SESSION['form_1']);
    header('Location: ../');
    exit;
}

$isNavigatingBack = isset($_SESSION['form_1']) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] === "http://localhost/ui/rooms/booking-summary.php" || isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] === "http://localhost/ui/rooms/booking-summary.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $isNavigatingBack) {
    $_POST = isset($_SESSION['form_1']) ? $_SESSION['form_1'] : $_POST;

    // Check if the required data is present
    if (isset($_POST['room_id'], $_POST['arrival-date'], $_POST['departure-date'], $_POST['guest_no'], $_POST['booking_id'])) {
        $_SESSION['form_1'] = $_POST;
        $roomId = (int)$_POST['room_id'];
        $arrivalDateString = $_POST['arrival-date'];
        $departureDateString = $_POST['departure-date'];
        $guestNumber = $_POST['guest_no'];
        $bookingId = $_POST['booking_id'];

        if (!$bookingService->isBookingIdUnique($bookingId)) {
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
    header('Location: ../rooms/');
    exit;
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
    <link rel="stylesheet" href="../../css/booking-form.css">
    <link rel="stylesheet" href="../../css/booking-form-mobile.css">
    <title>Booking Form</title>
</head>

<body>
    <div class="column-container center">
        <nav class="navbar">
            <div class="navbar-content">
                <div class="navbar-start">
                    <a href="../" class="navbar-logo">
                        <img src="../../res/images/site_logo.svg" alt="Logo">
                    </a>
                    <a href="../" class="navbar-navigation-icon hidden">
                        <img src="../../res/images/arrow_back.svg" alt="Logo">
                    </a>
                </div>
                <div class="navbar-center">
                    <h2 class="progress hidden">Booking Information</h2>
                    <div class="progress-bar">
                        <div class="progress-item active">
                            <div class="progress-circle">✔</div>
                            <div class="progress-text">Booking Information</div>
                        </div>
                        <div class="progress-item">
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
        <div class="wrap-content body-content">
            <form id="form" action="./booking-summary.php" method="POST">
                <input name="booking_id" value="<?php echo $bookingId; ?>" hidden />
                <div class="column-container">
                    <h1 class="display-medium text-center">Booking Form</h1>
                    <div class="card">
                        <div class="card-content">
                            <div class="column-container form-content">
                                <div class="row-container center-vertical booking-form form-element-container calendar-container">
                                    <!-- Date of Arrival -->
                                    <div class="column-container booking-form-element">
                                        <!-- Calendar Picker Dialog -->
                                        <label class="body-large font-medium">Date of Arrival <span class="error-text">*</span></label>
                                        <input type="text" class="calendar-input" name="arrival_date" value="<?php echo $arrivalDateString; ?>" hidden required>

                                        <div class="calendar-picker">
                                            <div class="calendar-header">
                                                <span class="material-icons-outlined prev-month">navigate_before</span>
                                                <div class="select-container">
                                                    <select class="select-month select text-center"></select>
                                                </div>
                                                <div class="select-container">
                                                    <select class="select-year select text-center"></select>
                                                </div>
                                                <span class="material-icons-outlined next-month">navigate_next</span>
                                            </div>
                                            <div class="calendar" data-selected="<?php echo $arrivalDateString; ?>"></div>
                                        </div>
                                    </div>

                                    <!-- Date of Departure -->
                                    <div class="column-container mobile-margin booking-form-element">
                                        <!-- Calendar Picker Dialog -->
                                        <label class="body-large font-medium">Date of Departure <span class="error-text">*</span></label>
                                        <input type="text" class="calendar-input" name="departure_date" value="<?php echo $departureDateString; ?>" hidden required>

                                        <div class="calendar-picker">
                                            <div class="calendar-header">
                                                <span class="material-icons-outlined prev-month">navigate_before</span>
                                                <div class="select-container">
                                                    <select class="select-month select text-center"></select>
                                                </div>
                                                <div class="select-container">
                                                    <select class="select-year select text-center"></select>
                                                </div>
                                                <span class="material-icons-outlined next-month">navigate_next</span>
                                            </div>
                                            <div class="calendar" data-selected="<?php echo $departureDateString; ?>"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Divider/Line -->
                                <div class="horizontal-divider booking-form"></div>


                                <div class="row-container center-vertical form-element-container booking-form room-info-container">
                                    <div class="row-container center-horizontal fill-parent booking-form-element">
                                        <label class="guest-number-label label-margin-1 body-large font-medium">Guests <span class="error-text">*</span></label>
                                        <div class="column-container">
                                            <div class="textfield-container" style="--textfield-width: 5rem">
                                                <input type="text" id="guest-number" name="guest_no" class="textfield-input" placeholder=" " value="<?php echo $guestNumber; ?>" required />
                                                <div class="textfield-underline"></div>
                                            </div>
                                            <div class="row-container guest-buttons">
                                                <button id="decrease-guest" class="button fill-parent guest-button">–</button>
                                                <button id="increase-guest" class="button fill-parent guest-button">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-container  center-horizontal fill-parent booking-form-element mobile-margin">
                                        <label class="label-margin-2 body-large font-medium mobile-margin-room-label">Room <span class="error-text">*</span></label>
                                        <div class="select-container">
                                            <select name="room_id" class="select-month select fill-parent" required>
                                                <option>Choose a Room</option>
                                                <?php
                                                foreach ($roomRepository->getAllRooms() as $category => $rooms) {
                                                    foreach ($rooms as $room) {
                                                ?>
                                                    <option value="<?php echo $room["id"]; ?>" <?php echo $roomId === $room["id"] ? "selected" : ""; ?>><?php echo $room["name"] . " - " . ucfirst($category); ?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-container center-vertical booking-form form-element-container">
                                    <div class="row-container center-horizontal fill-parent booking-form-element">
                                        <label class="label-margin-2 body-large font-medium">Contact #: <span class="error-text">*</span></label>
                                        <div class="textfield-container">
                                            <input type="text" name="contact" id="contact" class="textfield-input" placeholder="09999999999" value="<?php echo $currentLoggedInUser->getPhoneNumber(); ?>" required />
                                            <div class="textfield-underline"></div>
                                            <span class="error-message">Invalid phone number</span>
                                        </div>
                                    </div>
                                    <div class="row-container  center-horizontal fill-parent booking-form-element mobile-margin">
                                        <label class="label-margin-2 body-large font-medium mobile-margin-email-label">Email: <span class="error-text">*</span></label>
                                        <div class="textfield-container">
                                            <input type="email" name="email" id="email" class="textfield-input" placeholder="johndoe@gmail.com" value="<?php echo $currentLoggedInUser->getEmail(); ?>" required />
                                            <div class="textfield-underline"></div>
                                            <span class="error-message">Invalid email address</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Divider/Line -->
                                <div class="horizontal-divider booking-form"></div>

                                <div class="column-container booking-form booking-form-element special-request-container">
                                    <span class="body-large font-medium label-margin-3">
                                        Special Request:
                                    </span>
                                    <div class="outlined-textfield-container" style="--textfield-width: 100%">
                                        <textarea class="outlined-textfield-input" name="special_request" placeholder="Note your special requests" rows="5"></textarea>
                                    </div>
                                </div>

                                <!-- Additional margin -->
                                <div class="booking-form"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row-container booking-form">
                        <div class="price-container display-small fill-parent booking-form-element">
                            <span>Price:</span>
                            <input type="number" name="price" id="price-input" value="<?php echo $roomRepository->getRoomById($roomId)['price']; ?>" hidden />
                            <span id="price">₱<?php echo $roomRepository->getRoomById($roomId)['price']; ?></span>
                        </div>
                        <button id="confirm" class="button label-margin-2 booking-form-buttons">Confirm</button>
                        <button id="cancel" class="button label-margin-2 booking-form-buttons cancel-button">Cancel</button>
                    </div>

                    <!-- Additional margin -->
                    <div class="booking-form"></div>
                </div>
            </form>
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
<script src="../../scripts/calendar.js"></script>
<script src="../../scripts/validators.js"></script>
<script src="../../scripts/booking/price-calculator.js"></script>
<script src="../../scripts/booking/booking-form.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contactInput = document.getElementById('contact');
        const emailInput = document.getElementById('email');
        const guestNumberInput = document.getElementById('guest-number');
        const increaseGuestButton = document.getElementById('increase-guest');
        const decreaseGuestButton = document.getElementById('decrease-guest');

        guestNumberInput.addEventListener('change', (e) => {
            e.target.value = validateGuestNumber(e.target.value);
        })

        emailInput.addEventListener('input', (e) => {
            const parentContainer = e.target.parentElement;
            const isValid = validateEmail(e.target.value) || isEmpty(e.target.value);

            if (isValid) {
                parentContainer.classList.remove('error-container');
            } else {
                parentContainer.classList.add('error-container');
            }
        })

        contactInput.addEventListener('input', (e) => {
            const parentContainer = e.target.parentElement;
            const isValid = validatePhilippinePhoneNumber(e.target.value) || isEmpty(e.target.value);

            if (isValid) {
                parentContainer.classList.remove('error-container');
            } else {
                parentContainer.classList.add('error-container');
            }
        })

        increaseGuestButton.addEventListener('click', () => {
            guestNumberInput.value = increaseGuest(guestNumberInput.value);
        })

        decreaseGuestButton.addEventListener('click', () => {
            guestNumberInput.value = decreaseGuest(guestNumberInput.value);
        })
    })

    function increaseGuest(inputValue) {
        let increasedGuestNumber = parseInt(inputValue) + 1;

        if (increasedGuestNumber >= MAXIMUM_GUEST_ALLOWED) {
            increasedGuestNumber = MAXIMUM_GUEST_ALLOWED;
        }

        return increasedGuestNumber;
    }

    function decreaseGuest(inputValue) {
        let decreasedGuestNumber = parseInt(inputValue) - 1;

        if (decreasedGuestNumber <= 0) {
            decreasedGuestNumber = MINIMUM_GUEST_ALLOWED;
        }

        return decreasedGuestNumber;
    }
</script>
<script>
    const navigationButton = document.querySelector(".navbar-navigation-icon");
    navigationButton.addEventListener('click', backToHome);
    document.getElementById('cancel').addEventListener('click', backToHome);

    function backToHome(e) {
        e.preventDefault();
        window.location.href = '../'
    }
</script>
</html>