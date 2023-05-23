<?php
require_once __DIR__ . "/../../data/model/User.php";
require_once __DIR__ . '/../../data/service/Sanitizer.php';
require_once __DIR__ . '/../../data/repository/UserRepository.php';
require_once __DIR__ . "/../../data/service/BookingService.php";

session_start(); // Start the session

$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);
$bookingService = new BookingService($userRepository);

// Check if user is not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] === false) {
    // Redirect to home page or any other authorized page
    header('Location: login.php');
    exit;
}

$currentLoggedInUser = unserialize($_SESSION['user']);
$currentLoggedInUser = $bookingService->updateUserBookings($currentLoggedInUser);
$_SESSION['user'] = serialize($currentLoggedInUser);

// Check if site has been hard refreshed
$isSiteHardRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && isset($_SESSION["user"]) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] === 'no-cache');
if ($isSiteHardRefreshed) {
    // Create a new user repository with the JSON data provider
    $_SESSION['user'] = serialize($userRepository->getUserById($currentLoggedInUser->getUserId()));
    $currentLoggedInUser = $userRepository->getUserById($currentLoggedInUser->getUserId());
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
    <link rel="stylesheet" href="../../css/user-profile.css">
    <title>My Bookings</title>
    <style>
        body {
            margin: 0;
        }

        .body-content {
            margin-top: 5rem;
            margin-left: 15rem;
            width: 80%;
        }

        h2 {
            margin-top: 5rem;
        }

        .info-main-con {
            margin-bottom: 3rem;
        }

        .navbar {
            width: 100%;
        }

        @media (max-width: 1186px) {
            .body-content {
                margin: 0 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="column-container">
        <nav class="navbar sticky-navbar" id="navbar">
            <div class="navbar-content">
                <div class="navbar-start">
                    <a href="../" class="navbar-logo">
                        <img src="../../res/images/site_logo.svg" alt="Logo">
                        <h3>Home</h3>
                    </a>
                </div>
                <div class="navbar-center"></div>
                <div class="navbar-end">
                    <button class="navbar-menu hidden" id="drawer-toggle">
                        <img src="../../res/images/menu.svg" alt="Navigation menu icon">
                    </button>
                    <a href="../rooms/index.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Rooms
                    </a>
                    <a href="./" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Profile
                    </a>
                    <a href="./edit-profile.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Settings
                    </a>
                    <a class="button navbar-item active" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        History
                    </a>
                    <a href="./logout.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Logout
                    </a>
                </div>
            </div>
        </nav>
        <div class="wrap-content body-content">
            <div class="column-container center-start">
            <?php
            // Only show card if booking history is not empty!
            $bookingHistory = $currentLoggedInUser->getBookingHistory();
            if (!empty($bookingHistory)) {
            ?>
            <h2 class="font-medium text-left">Current Reservation</h2>
            <?php
                foreach ($bookingHistory as $booking) {
                    if($booking->getBookingStatus() !== BookingStatus::COMPLETED) {
                        $room = $booking->getRoom();
            ?>
                <div class="card emphasis info-main-con">
                    <div class="card-content">
                        <div class="info-main">
                            <div>
                                <p>Room Name:
                                    <span class="font-medium"><?php echo $room->getRoomName(); ?></span>
                                </p>
                                <p>Room Type:
                                    <span class="font-medium"><?php echo $room->getRoomType(); ?></span>
                                </p>
                                <p>Room Address:
                                    <span class="font-medium"><?php echo $room->getRoomAddress(); ?></span>
                                </p>
                            </div>
                            <div class="column-container">
                                <p>Date of Departure</p>
                                <span class="font-medium"><?php echo $booking->getDepartureDate(); ?></span>
                            </div>
                            <div class="column-container">
                                <p>Date of Arrival</p>
                                <span class="font-medium"><?php echo $booking->getArrivalDate() ?></span>
                            </div>
                        </div>

                        <div style="margin-top: 70px;">
                            <p>Confirmation:
                                <span class="font-medium">Receipt</span>
                            </p>
                            <p>Status:
                                <span class="font-medium"><?php echo $booking->getBookingStatus() ?></span>
                            </p>
                        </div>

                        <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                            <span>Price:
                                <span class="font-medium"><?php echo $booking->getBookingPrice() ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php 
                    }
                } 
            ?>
                <?php
                // Only show card if booking history is not empty!
                $pastBookings = 0;
                foreach ($bookingHistory as $index => $booking) {
                    if($booking->getBookingStatus() !== BookingStatus::COMPLETED) {
                        continue;
                    }

                    ++$pastBookings;

                    $room = $booking->getRoom();

                    if($pastBookings === 1) {
                ?>
                <h2 class="font-medium">Past Bookings</h2>
                <?php } ?>
                <div class="card info-main-con">
                    <div class="card-content">
                        <div class="info-main">
                            <div>
                                <p>Room Name:
                                    <span class="font-medium"><?php echo $room->getRoomName(); ?></span>
                                </p>
                                <p>Room Type:
                                    <span class="font-medium"><?php echo $room->getRoomType(); ?></span>
                                </p>
                                <p>Room Address:
                                    <span class="font-medium"><?php echo $room->getRoomAddress(); ?></span>
                                </p>
                            </div>
                            <div class="column-container">
                                <p>Date of Departure</p>
                                <span class="font-medium"><?php echo $booking->getDepartureDate(); ?></span>
                            </div>
                            <div class="column-container">
                                <p>Date of Arrival</p>
                                <span class="font-medium"><?php echo $booking->getArrivalDate() ?></span>
                            </div>
                        </div>

                        <div style="margin-top: 70px;">
                            <p>Confirmation:
                                <span class="font-medium">Receipt</span>
                            </p>
                            <p>Status:
                                <span class="font-medium"><?php echo $booking->getBookingStatus() ?></span>
                            </p>
                        </div>

                        <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                            <span>Price:
                                <span class="font-medium"><?php echo $booking->getBookingPrice() ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php 
                }
            }
            ?>
            </div>
        </div>

        <?php 
            if(empty($bookingHistory)) {
            ?>
            <div class="column-container center vh100">
                <h1 class="display-large font-black text-center">You have no bookings yet!</h1>
                <p class="title-medium font-black text-center">You can start reserving now</p>
                <div class="row-container center button-margin">
                    <a href="../rooms/index.php" class="button text-center">Go to Rooms</a>
                </div>
            </div>
        <?php } ?>
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
            <a href="./index.php" class="navbar-dropdown-item">
                <div class="row-container center-horizontal">
                    <span class="material-icons navbar-dropdown-item-icon">account_circle</span>
                    Profile
                </div>
            </a>
            <a href="./edit-profile.php" class="navbar-dropdown-item">
                <div class="row-container center-horizontal">
                    <span class="material-icons navbar-dropdown-item-icon">settings</span>
                    Settings
                </div>
            </a>
            <a href="./booking-history.php" class="navbar-dropdown-item">
                <div class="row-container center-horizontal">
                    <span class="material-icons navbar-dropdown-item-icon">auto_stories</span>
                    My Bookings
                </div>
            </a>
            <a href="./logout.php" class="navbar-dropdown-item">
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
            <a href="../index.php">
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