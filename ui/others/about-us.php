<?php
require_once __DIR__ . "/../../data/model/User.php";
require_once __DIR__ . '/../../data/repository/UserRepository.php';

session_start(); // Start the session

$userRepository = new UserRepository(__DIR__ . '/../../data/users.json');

$currentLoggedInUser = null;
if (isset($_SESSION['user'])) {
    $currentLoggedInUser = unserialize($_SESSION['user']);
}

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
    <link rel="icon" href="../../res/images/site_logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="../../css/theme/theme.css">
    <title>About Us</title>
    <style>
        .body-content {
            margin: 9rem 9rem;
            width: 75%;
        }

        p {
            font-size: 1.2rem;
        }

        h2 {
            font-weight: 700;
        }

        @media (max-width: 1186px) {
            .body-content {
                margin: 9rem 2rem;
                width: 80%;
            }

            .navbar-logo {
                display: initial !important;
            }
        }
    </style>
</head>

<body>
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
                <?php if ($currentLoggedInUser !== null) { ?>
                    <button class="navbar-menu hidden" id="drawer-toggle">
                        <img src="../../res/images/menu.svg" alt="Navigation menu icon">
                    </button>
                    <a href="../rooms/index.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Rooms
                    </a>
                    <a href="../profile/" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Profile
                    </a>
                    <a href="../profile/edit-profile.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Settings
                    </a>
                    <a href="../profile/booking-history.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        History
                    </a>
                    <a href="../profile/logout.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Logout
                    </a>
                <?php } else { ?>
                    <a href="../profile/sign_up.php" class="button" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Sign Up
                    </a>
                    <a href="../profile/login.php" class="button" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Login
                    </a>
                <?php } ?>
            </div>
        </div>
    </nav>
    <div class="body-content">
        <h1 class="display-large font-black">About Us</h1>

        <div class="section">
            <h2>Welcome to Grand Eden Oasis</h2>
            <p>Grand Eden Oasis is a luxurious hotel that provides all guests with an unforgettable experience. Our hotel, nestled in the heart of a lush oasis, is a tranquil haven that combines modern conveniences with natural beauty. We aim to provide a perfect retreat for both leisure and business travelers by providing exceptional hospitality, exquisite accommodations, and a variety of amenities.</p>
        </div>

        <div class="section">
            <h2>Rooms and Suites</h2>
            <p>At Grand Eden Oasis, we are delighted to offer six categories of rooms: Accessible, Connecting, Deluxe, Executive, Standard, and Suite. Our well-appointed rooms and suites have been meticulously designed to ensure utmost comfort and relaxation for our esteemed guests. Each room is thoughtfully adorned with elegant furnishings, plush bedding, and contemporary amenities. Whether you opt for a cozy standard room or a generously spacious suite, we guarantee a harmonious fusion of style and practicality. Our primary goal is to create an ideal sanctuary for both leisure and business travelers alike.</p>
        </div>

        <div class="section">
            <h2>Dining Options</h2>
            <p>Indulge in a culinary journey at one of our exceptional restaurants. Our talented chefs create delectable menus that highlight the best of local and international flavors. Our restaurants provide a diverse range of options to satisfy every palate, from gourmet fine dining experiences to casual all-day dining.</p>
        </div>

        <div class="section">
            <h2>Recreation and Wellness</h2>
            <p>Our hotel offers a variety of wellness and recreational facilities for those looking to unwind. Relax in our spa, where skilled therapists provide a variety of rejuvenating treatments. Stay active in our cutting-edge fitness center, cool off in our sparkling swimming pool, or simply stroll through our beautifully landscaped gardens.</p>
        </div>

        <div class="section">
            <h2>Events and Meetings</h2>
            <p>Grand Eden Oasis is an excellent location for business meetings, conferences, and special events. Our adaptable event spaces are outfitted with cutting-edge technology and staffed by an experienced events team. We strive to ensure that every event, whether a corporate gathering or a memorable celebration, is flawlessly executed.</p>
        </div>

        <div class="section">
            <h2>Impeccable Service</h2>
            <p>We believe that personalized service is the key to creating extraordinary experiences at Grand Eden Oasis. Our dedicated team of hospitality professionals is dedicated to anticipating and exceeding your needs. We are committed to providing warm, attentive, and personalized service from the moment you arrive until you depart.</p>
        </div>

        <div class="section">
            <h2>Your Ultimate Retreat Awaits</h2>
            <p>At Grand Eden Oasis, we prioritize your comfort, satisfaction, and happiness. We strive to foster an atmosphere in which every moment is treasured and every need is met. With us, you can experience the pinnacle of luxury and hospitality. Book a stay at Grand Eden Oasis and make memories to last a lifetime.</p>
        </div>
    </div>
    <div id="drawer" class="drawer hidden">
        <div class="column-container drawer-menu">
            <?php if ($currentLoggedInUser !== null) { ?>
                <a href="" class="drawer-logo">
                    <img src="<?php

                                if ($currentLoggedInUser != null) {
                                    echo $currentLoggedInUser->getProfilePicture();
                                } else {
                                    echo "../../res/images/image-placeholder.svg";
                                }

                                ?>" alt="Logo">
                </a>
                <a href="../profile/" class="navbar-dropdown-item">
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
            <?php } ?>
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
                <p><a class="on-primary-text">About Us</a></p>
                <p><a href="./faqs.php" class="on-primary-text">FAQs</a></p>
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