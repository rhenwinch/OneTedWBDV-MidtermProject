<?php
require_once __DIR__ . "/../../data/common/BookingStatus.php";
require_once __DIR__ . "/../../data/model/Room.php";
require_once __DIR__ . "/../../data/model/User.php";
require_once __DIR__ . '/../../data/repository/RoomRepository.php';
require_once __DIR__ . '/../../data/service/UserService.php';
require_once __DIR__ . '/../../data/service/BookingService.php';
require_once __DIR__ . '/../../data/repository/UserRepository.php';

session_start(); // Start the session

$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] === false) {
    // Redirect to home page or any other authorized page
    header('Location: ../profile/login.php');
    exit;
}

$roomRepository = new RoomRepository(__DIR__ . '/../../data/rooms.json');
$userService = new UserService($userRepository);
$bookingService = new BookingService($userRepository);
$currentLoggedInUser = unserialize($_SESSION['user']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required data is present
    if (isset($_POST['price'], $_POST['room_id'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['contact'], $_POST['booking_id'])) {
        $roomId = $_POST['room_id'];
        $price = (float)$_POST['price'];
        $arrivalDateString = $_POST['arrival_date'];
        $departureDateString = $_POST['departure_date'];
        $contact = $_POST['contact'];
        $bookingId = $_POST['booking_id'];

        if (!$bookingService->isBookingIdUnique($bookingId)) {
            // Required data not present, redirect to home
            header('Location: ../');
            exit;
        }

        $arrivalDate = DateTime::createFromFormat('Y-n-j', $arrivalDateString);
        $departureDate = DateTime::createFromFormat('Y-n-j', $departureDateString);

        $room = Room::fromJson($roomRepository->getRoomById($roomId));
        $booking = new Booking(
            $room,
            new DateTime(),
            $arrivalDate,
            $departureDate,
            $bookingId,
            BookingStatus::CONFIRMED,
            $price
        );

        $currentLoggedInUser = $bookingService->addBooking($currentLoggedInUser, $booking);
        $currentLoggedInUser = $userService->updateUser($currentLoggedInUser, null, null, null, $contact);
        $_SESSION['user'] = serialize($currentLoggedInUser);
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="../../res/images/site_logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="../../css/theme/theme.css">
    <title>Booking Confirmed!</title>

    <style>
        /* Override global variables */
        :root {
            --button-size: 1rem 3rem;
        }

        .success-message {
            width: 100vw;
            height: 100vh;
        }

        .success-message>* {
            margin: 1rem 0;
        }

        .navbar-navigation-icon {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="column-container">
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
                    <h2 class="progress hidden">Booking Confirmation</h2>
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
                        <div class="progress-item active">
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

        <div class="column-container center success-message">
            <h1 class="display-large font-black text-center">Transaction Done!</h1>
            <p class="title-medium font-black text-center">You can now view this reservation in your profile</p>
            <div class="row-container center button-margin">
                <a href="../profile/index.php" class="button text-center">Go to Profile</a>
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
            <a href="">
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

</html>