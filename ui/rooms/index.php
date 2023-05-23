<?php
require_once __DIR__ . "/../../data/model/User.php";
require_once __DIR__ . "/../../data/service/BookingService.php";
require_once __DIR__ . '/../../data/repository/UserRepository.php';
require_once __DIR__ . '/../../data/repository/RoomRepository.php';

session_start(); // Start the session

$userRepository = new UserRepository(__DIR__ . '/../../data/users.json');
$roomRepository = new RoomRepository(__DIR__ . '/../../data/rooms.json');
$bookingService = new BookingService($userRepository);
$roomFilters = array_keys($roomRepository->getAllRooms());

$currentLoggedInUser = null;

// Check if user is not logged in
$isLoggedIn = (array_key_exists('loggedIn', $_SESSION) && $_SESSION['loggedIn'] === true) || false;
if ($isLoggedIn) {
    // Redirect to home page or any other authorized page
    $currentLoggedInUser = unserialize($_SESSION['user']);
}

$roomType = $roomFilters[0];
if(isset($_GET['roomType'])) {
    $roomType = $_GET['roomType'];
}


// Check if site has been hard refreshed
$isSiteHardRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && isset($_SESSION["user"]) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] === 'no-cache');
if ($isSiteHardRefreshed && $isLoggedIn) {
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
    <link rel="stylesheet" href="../../css/room-details.css">
    <link rel="stylesheet" href="../../css/room-details-mobile.css">
    <title>Rooms</title>
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
                    <?php if ($isLoggedIn) { ?>
                        <button class="navbar-menu hidden" id="drawer-toggle">
                            <img src="../../res/images/menu.svg" alt="Navigation menu icon">
                        </button>
                        <a class="button navbar-item active" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
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
                        <a href="./logout.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
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
        <div class="column-container body-content center">
            <h2 class="display-large font-black primary-text">Rooms</h2>

            <!-- Room Items -->
            <div class="row-container room-container center-spaced">
                <div class="left-arrow fill-parent clickable">
                    <img src="../../res/images/Left_arrow.svg">
                </div>
                <div class="viewpager-container">
                    <?php
                    foreach ($roomRepository->getAllRooms() as $category => $rooms) {
                    ?>
                        <div class="viewpager<?php echo $category === $roomType ? "" : " hidden"; ?>">
                            <?php foreach ($rooms as $room) { ?>
                                <div class="row-container room-item center w100 page">
                                    <div class="card center elevation-3 room-card" style="--card-height: 22.3rem">
                                        <div class="card-content body-medium h100">
                                            <form action="../rooms/booking-form.php" class="h100" method="POST">
                                                <div class="column-container center h100 room-item-content">
                                                    <img class="img-mobile hidden" src="<?php echo "../../res/images/content/ROOMS/".ucfirst($category)."/".$room["name"]."/1.jpg"; ?>">
                                                    <h1 class="text-center"><?php echo $room["name"]; ?></h1>
                                                    <p class="text-justify"><?php echo $room["description"]['long']; ?></p>
                                                    <input name="room_id" id="room_id" value="<?php echo $room["id"]; ?>" hidden>
                                                    <input name="arrival-date" id="arrival_date" value="" hidden>
                                                    <input name="departure-date" id="departure_date" value="" hidden>
                                                    <input name="guest_no" id="guest_no" value="1" hidden>
                                                    <input name="booking_id" id="booking_id" value="<?php echo $bookingService->generateRandomBookingId(); ?>" hidden>

                                                    <div class="book-button fill-parent">
                                                        <button class="button" id="book-button">Book</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="room-item-image" style="background-image: url(<?php echo "../../res/images/content/ROOMS/".ucfirst($category)."/".$room["name"]."/1.jpg"; ?>"; ?>);"></div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="right-arrow fill-parent clickable">
                    <img src="../../res/images/Right_arrow.svg">
                </div>
            </div>

            <!-- Filters -->

            <div class="row-container filters-container">
                <?php foreach ($roomFilters as $index => $filter) { ?>
                    <button class="button room-category-filter font-medium <?php echo $filter == $roomType ? "active" : ""; ?>" id="<?php echo $filter; ?>">
                        <?php echo ucwords($filter); ?>
                    </button>
                <?php } ?>
            </div>


            <!-- Room Information -->
            <?php
            foreach ($roomRepository->getAllRooms() as $category => $rooms) {
                for ($i = 0; $i < count($rooms); $i++) {
                    $roomKeys = array_values($rooms);
                    $room = $roomKeys[$i];
            ?>
                    <div class="column-container center room-info-container <?php echo $category == $roomFilters[0] && $i == 0 ? "active" : "hidden"; ?>">
                        <!-- Top Information -->
                        <div class="row-container room-main-info center-spaced">
                            <div class="room-info-image" style="background-image: url(<?php echo "../../res/images/content/ROOMS/".ucfirst($category)."/".$room["name"]."/1.jpg"; ?>"; ?>););"></div>
                            <div class="column-container center-start font-medium">
                                <p>Size: <?php echo $room["sqm"]; ?> sqm</p>
                                <p>Rooms: <?php echo $room["maxRooms"]; ?></p>
                                <p>Guest: <?php echo $room["maxHeads"]; ?></p>
                                <p>Beds: <?php echo $room["maxBeds"]; ?></p>
                            </div>
                        </div>

                        <!-- Gallery -->
                        <h1 class="display-small font-medium">Gallery</h1>
                        <div class="row-container center-spaced gallery-container">
                            <?php for ($j = 1; $j <= 3; $j++) {
                                echo '<div class="gallery-item" style="background-image: url(../../res/images/content/ROOMS/'.ucfirst($category).'/'.$room["name"].'/'.($j + 1).'.jpg);"></div>';
                            } ?>
                        </div>


                        <!-- Room Reviews -->
                        <h1 class="display-small font-medium">Room Reviews</h1>
                        <div class="row-container reviews-container">
                            <?php
                            // Read the reviews from the CSV file
                            $reviews = [];
                            if (($handle = fopen(__DIR__ . "/../../data/sample_room_testimonials.csv", 'r')) !== false) {
                                while (($data = fgetcsv($handle)) !== false) {
                                    $reviews[] = $data;
                                }
                                fclose($handle);
                            }

                            // Get three random reviews
                            $randomReviews = array_rand($reviews, 3);

                            // Iterate over the random reviews and insert them into the HTML structure
                            foreach ($randomReviews as $index) {
                                $review = $reviews[$index];
                                $description = $review[0];
                                $author = $review[1];

                                // Output the HTML structure with the review data
                                echo '<div class="card center elevation-1 reviews-item">';
                                echo '<div class="card-content body-medium">';
                                echo '<p><em>"' . $description . '"</em></p>';
                                echo '<p><b>— ' . $author . '</b></p>';
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>

                        </div>
                    </div>
            <?php
                }
            }
            ?>
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
<script src="../../scripts/room-details/viewpager.js"></script>
<script src="../../scripts/room-details/filters.js"></script>

</html>