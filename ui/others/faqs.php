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
    <link rel="stylesheet" href="../../css/theme/theme.css">
    <title>FAQs</title>
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
        </div>
    </nav>
    <div class="body-content">
        <h1 class="display-large font-black">Frequently Asked Questions</h1>

        <h2>Q: How can I make a room reservation at Grand Eden Oasis?</h2>
        <p><em>A:</em> You can make a room reservation at Grand Eden Oasis by visiting their official website. Online booking is available 24/7.</p>

        <h2>Q: What information do I need to provide while making a reservation?</h2>
        <p><em>A:</em> To make a room reservation, you will typically need to provide your name, contact details, preferred dates of stay, the number of guests, and any special requests you may have.</p>

        <h2>Q: What types of rooms are available at Grand Eden Oasis?</h2>
        <p><em>A:</em> Grand Eden Oasis provides a variety of room types to accommodate a variety of preferences and budgets. They may offer standard rooms, deluxe rooms, suites, and special themed rooms. For more information, visit our website.</p>

        <h2>Q: What payment methods are accepted at the hotel?</h2>
        <p><em>A:</em> At Grand Eden Oasis, we accept various payment methods for hotel room booking reservations. These include:</p>
        <ul>
            <li>Credit Cards: We accept Visa, Mastercard, American Express, and Discover. During the reservation process, you can securely provide your credit card information.</li>
            <li>Debit Cards: We also accept debit cards from major payment networks. Check that your debit card has enough funds to cover the reservation amount.</li>
            <li>E-Wallets: We understand the convenience of e-wallets and gladly accept popular digital payment options such as Apple Pay, Google Pay, and PayPal. Simply select the e-wallet option during the booking process and follow the instructions to complete your payment.</li>
            <li>Bank/Wire Transfer: <em>For bank/wire transfer payment, please contact our customer support team for detailed instructions and bank account information. Once the transfer is completed, please provide the transaction details to us for verification and confirmation of your reservation.</em></li>
        </ul>

        <h2>Q: Can I request a specific room location or view?</h2>
        <p><em>A:</em> Yes, special requests for specific room locations or views are possible, but they are subject to availability. We will do our best to accommodate your preferences, but we cannot guarantee that they will be met.</p>

        <h2>Q: What amenities are included in the room?</h2>
        <p><em>A:</em> The Grand Eden Oasis provides a variety of amenities that vary depending on the room type. Comfortable beds, private bathrooms, toiletries, air conditioning, Wi-Fi, television, mini-fridge, and in-room safe are all standard amenities. Some rooms may also include extras like a balcony, kitchenette, or separate living area.</p>

        <h2>Q: Are the rates on your website per person or room?</h2>
        <p><em>A:</em> Unless otherwise stated, the rates displayed on our website for Grand Eden Oasis room reservations are typically per room. The mentioned rate usually covers the cost of the room regardless of the number of occupants (up to the maximum occupancy allowed for that specific room type). Certain packages or promotions, on the other hand, may have different pricing structures, so it's always a good idea to read the details provided during the booking process.</p>
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
                <p><a href="./about-us.php" class="on-primary-text">About Us</a></p>
                <p><a class="on-primary-text">FAQs</a></p>
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