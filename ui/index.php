<?php
require_once __DIR__ . "/../data/model/User.php";
require_once __DIR__ . "/../data/service/BookingService.php";
require_once __DIR__ . '/../data/repository/UserRepository.php';
require_once __DIR__ . '/../data/repository/RoomRepository.php';

session_start(); // Start the session

$userRepository = new UserRepository(__DIR__ . '/../data/users.json');
$roomRepository = new RoomRepository(__DIR__ . '/../data/rooms.json');
$bookingService = new BookingService($userRepository);
$roomFilters = array_keys($roomRepository->getAllRooms());

$currentLoggedInUser = null;

try {
    unset($_SESSION['form_1']);
} catch (\Throwable $th) {
}

// Check if user is not logged in
$isLoggedIn = (array_key_exists('loggedIn', $_SESSION) && $_SESSION['loggedIn'] === true) || false;
if ($isLoggedIn) {
    // Redirect to home page or any other authorized page
    $currentLoggedInUser = unserialize($_SESSION['user']);
}


// Check if site has been hard refreshed
$isSiteHardRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] === 'no-cache');
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

    <link rel="icon" href="../res/images/site_logo.svg" type="image/x-icon">
    <link rel="stylesheet" href="../css/theme/theme.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/home-mobile.css">
    <title>Grand Eden Oasis</title>
</head>

<body>
    <button id="goToTopBtn" class="tertiary">
        <img src="../res/images/Up_arrow.svg" alt="Back to top button">
    </button>

    <div class="column-container">
        <nav class="navbar sticky-navbar" id="navbar">
            <div class="navbar-content">
                <div class="navbar-start">
                    <a href="#" class="navbar-logo">
                        <img src="../res/images/site_logo.svg" alt="Logo">
                    </a>
                </div>
                <div class="navbar-center"></div>
                <div class="navbar-end">
                    <?php if ($isLoggedIn) { ?>
                        <button class="navbar-menu hidden" id="drawer-toggle">
                            <img src="../../res/images/menu.svg" alt="Navigation menu icon">
                        </button>
                        <a href="./rooms/index.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                            Rooms
                        </a>
                        <a href="./profile/" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                            Profile
                        </a>
                        <a href="./profile/edit-profile.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                            Settings
                        </a>
                        <a href="./profile/booking-history.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                            History
                        </a>
                        <a href="./profile/logout.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                            Logout
                        </a>
                    <?php } else { ?>
                        <a href="./profile/sign_up.php" class="button" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                            Sign Up
                        </a>
                        <a href="./profile/login.php" class="button" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                            Login
                        </a>
                    <?php } ?>
                </div>
            </div>
        </nav>
        <div class="relative-container">
            <div class="row-container header-container center-spaced" id="resting-navbar">
                <a href="">
                    <div class="card elevation-2 logo clickable"></div>
                </a>

                <?php if ($isLoggedIn) { ?>
                    <div class="row-container navigation-button-container profile-container center-spaced">
                        <a href="./profile/" class="button navigation-button font-medium">
                            <div class=" row-container center">
                                <img src="<?php echo $currentLoggedInUser->getProfilePicture(); ?>" class="user" alt="Profile" style="margin-right: 1rem">
                                <div class="column-container title-small text-left profile-info">
                                    <span>Hi,
                                        <?php echo $currentLoggedInUser->getName(); ?>!
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="row-container navigation-button-container center-spaced auth-button">
                        <a href="./profile/sign_up.php" class="button navigation-button font-medium">Sign Up</a>
                        <a href="./profile/login.php" class="button navigation-button font-medium">Login</a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="column-container">
            <div id="top-content" class="column-container">
                <div class="relative-container headline-container fill-parent">
                    <div class="headline-image"></div>
                    <div class="column-container center headline-content">
                        <h1 class="display-large font-black text-center">Grand Eden Oasis</h1>
                        <div class="card elevation-2 booking-selector-container">
                            <form class="h100" method="POST" action="./rooms/booking-form.php" id="form">
                                <input name="booking_id" value="<?php echo $bookingService->generateRandomBookingId(); ?>" hidden />
                                <div class="row-container center-spaced h100">
                                    <div class="relative-container w100">
                                        <button class="button booking-selector-button" id="roomButton">
                                            <div class="row-container center">
                                                <img class="booking-selector-button-icon" src="../res/images/Lable.svg" alt="An icon of rooms button">
                                                <input type="text" id="room-id" name="room_id" readonly hidden required />
                                                <input type="text" id="room-type" name="room_type" readonly hidden required />
                                                <input type="text" id="room-name" value="Rooms" readonly required />
                                            </div>
                                        </button>
                                        <div id="roomTypeDropdown" class="dropdown">
                                            <ul>
                                                <?php foreach ($roomFilters as $filter) { ?>
                                                    <li class="dropdown-item"><?php echo ucfirst($filter) ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>

                                        <?php
                                        foreach ($roomRepository->getAllRooms() as $category => $rooms) {
                                        ?>
                                            <div class="dropdown roomDropdown">
                                                <ul>
                                                    <?php foreach ($rooms as $room) { ?>
                                                        <li class="dropdown-room-item" data-id="<?php echo $room["id"]; ?>"><?php echo $room["name"]; ?></li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="vertical-divider booking-selector-divider"></div>
                                    <div class="relative-container w100">
                                        <button class="button booking-selector-button" id="datesButton">
                                            <div class="row-container center">
                                                <img class="booking-selector-button-icon" src="../res/images/Date_range.svg" alt="An icon of dates button">
                                                <span id="duration" class="w100 text-left">Days</span>
                                                <input type="text" class="date-input" name="arrival-date" hidden required />
                                                <input type="text" class="date-input" name="departure-date" hidden required />
                                            </div>
                                        </button>
                                        <div class="row-container calendar-dropdown">
                                            <div class="calendar-picker">
                                                <div class="calendar-header">
                                                    <span class="material-icons-outlined prev-month">navigate_before</span>
                                                    <select class="select select-month date-selector"></select>
                                                    <select class="select select-year date-selector"></select>
                                                    <span class="material-icons-outlined next-month">navigate_next</span>
                                                </div>
                                                <div class="calendar arrival"></div>
                                            </div>

                                            <div class="calendar-picker">
                                                <div class="calendar-header">
                                                    <span class="material-icons-outlined prev-month">navigate_before</span>
                                                    <select class="select select-month date-selector"></select>
                                                    <select class="select select-year date-selector"></select>
                                                    <span class="material-icons-outlined next-month">navigate_next</span>
                                                </div>
                                                <div class="calendar departure"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="vertical-divider booking-selector-divider"></div>
                                    <div class="row-container guest-no-container center-start">
                                        <div class="textfield-container">
                                            <div class="start-drawable guest-no-icon">
                                                <img src="../res/images/User.svg" class="booking-selector-button-icon" alt="An icon of guest number button">
                                            </div>
                                            <input type="number" name="guest_no" id="guest_no" class="textfield-input guest-no-left-margin" placeholder="No. of Guests" required />
                                        </div>
                                    </div>
                                    <button class="button booking-button" id="book">Book</button>
                                </div>
                            </form>
                        </div>

                        <h4 class="display-small font-light text-center">Where Luxury Meets Serenity</h1>
                    </div>
                </div>
                <div class="column-container about-us-container fill-parent">
                    <h4 class="title-large font-bold">About Us</h1>
                        <p class="body-large about-us-text">Welcome to Grand Eden Oasis, where luxury meets paradise. Nestled in the heart of a lush oasis, our hotel offers an unrivaled experience of refined elegance and unparalleled tranquility. Immerse yourself in a sanctuary where modern sophistication merges seamlessly with the captivating beauty of nature. Indulge in impeccable service, exquisite accommodations, and a myriad of amenities that will leave you with cherished memories of a truly remarkable stay.</p>
                        <a id="down-arrow" class="fill-parent down-arrow clickable"><img src="../res/images/Down_arrow.svg"></a>
                </div>
            </div>
            <section id="body-content">
                <div class="column-container center">
                    <h2 class="display-large font-black primary-text">Rooms</h2>

                    <!-- Room Items -->
                    <div class="horizontal-list rooms-list-container">
                        <div class="arrow left-arrow">&lt;</div>
                        <div class="arrow right-arrow">&gt;</div>
                        <div class="list-container">
                            <?php
                            foreach ($roomRepository->getAllRooms() as $category => $rooms) {
                            ?>
                                <ul id="<?php echo strtolower($category); ?>" class="list <?php echo $category != $roomFilters[0] ? "hidden" : ""; ?>">
                                    <?php
                                    $i = 0;
                                    foreach ($rooms as $room) {
                                    ?>
                                        <li>
                                            <a href="./rooms/index.php<?php echo "?roomType=$category&page=".$i++; ?>">
                                                <div class="card center elevation-3 room-item clickable">
                                                    <div class="relative-container">
                                                        <div class="initial-content">
                                                            <img class="room-item-image" src="
                                                                <?php echo "../../res/images/content/ROOMS/".ucfirst($category)."/".$room["name"]."/".rand(1,3).".jpg"; ?>">
                                                            <div class="card-content room-item-content body-medium">
                                                                <p>
                                                                    <?php echo $room["description"]['short']; ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="hidden-content column-container">
                                                            <h3 class="font-medium">Room ID:
                                                                <span class="font-regular">
                                                                    <?php echo $room["id"]; ?>
                                                                </span>
                                                            </h3>
                                                            <h3 class="font-medium">Guest No:
                                                                <span class="font-regular">
                                                                    <?php echo $room["maxHeads"]; ?>
                                                                </span>
                                                            </h3>
                                                            <h3 class="font-medium">Beds:
                                                                <span class="font-regular">
                                                                    <?php echo $room["maxBeds"]; ?>
                                                                </span>
                                                            </h3>
                                                            <p class="body-medium">
                                                                <?php echo $room["description"]['long']; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row-container filters-container">
                        <?php foreach ($roomFilters as $index => $filter) { ?>
                            <button class="button room-category-filter font-medium <?php echo $index == 0 ? "active" : ""; ?>" id="<?php echo $filter; ?>">
                                <?php echo ucwords($filter); ?>
                            </button>
                        <?php } ?>
                    </div>


                    <!-- Amenities -->
                    <div class="column-container center amenities-container">
                        <h2 class="display-large font-black primary-text">Amenities</h2>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <h2 class="font-bold hidden">Exquisite Gathering Halls</h2>
                            <div class="amenity-img" style="background-image: url('../res/images/content/VENUES/VENUES<?php echo rand(1, 9) ?>.jpeg');"></div>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Majestic Event Spaces</h2>
                                <p>Discover enchanting venues that bring your dreams to life. From elegant ballrooms to picturesque gardens, our venues provide the perfect backdrop for your special occasions. Let the magic unfold as you create unforgettable memories</p>
                            </div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <h2 class="font-bold hidden">Serene Aquatic Retreats</h2>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Crystal Waterscapes</h2>
                                <p>Dive into a world of tranquility and luxury with our exquisite pools. Immerse yourself in crystal-clear waters, surrounded by lush landscapes and breathtaking views. Whether you seek relaxation or invigoration, our pools offer a refreshing escape from the ordinary.</p>
                            </div>
                            <div class="amenity-img" style="background-image: url('../res/images/content/POOLS/<?php echo rand(1, 3) ?>.jpeg');"></div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <h2 class="font-bold hidden">Invigorating Fitness Centers</h2>
                            <div class="amenity-img" style="background-image: url('../res/images/content/GYM/<?php echo rand(1, 3) ?>.jpeg');"></div>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Fitness Sanctuaries</h2>
                                <p>Elevate your fitness journey in our state-of-the-art gym. Unleash your potential with cutting-edge equipment, personalized training programs, and expert guidance. Discover a haven where strength meets serenity, empowering you to achieve your fitness goals like never before.</p>
                            </div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <h2 class="font-bold hidden">Amusing Recreations</h2>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Enchanting Entertainments</h2>
                                <p>Experience a whirlwind of entertainment that will captivate your senses. From live performances that will leave you breathless to immersive experiences that transport you to another realm, our entertainment offerings promise to ignite your imagination and create lasting memories.</p>
                            </div>
                            <div class="amenity-img" style="background-image: url('../res/images/content/ENTERTAINMENT/<?php echo rand(1, 2) ?>.jpg');"></div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <h2 class="font-bold hidden">Epicurean Feasts</h2>
                            <div class="amenity-img" style="background-image: url('../res/images/content/FOODS/FOODS<?php echo rand(1, 5) ?>.jpg');"></div>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Culinary Masterpieces</h2>
                                <p>Savor a culinary voyage that tantalizes your taste buds and leaves you craving for more. Indulge in a symphony of flavors, artfully crafted by our master chefs. From gourmet delights to international cuisine, our diverse culinary offerings promise to take your dining experience to new heights.</p>
                            </div>
                        </div>
                    </div>


                    <!-- Reviews -->
                    <h2 class="display-large font-black primary-text">Testimonials</h2>
                    <div class="horizontal-list reviews-list-container">
                        <div class="arrow left-arrow">&lt;</div>
                        <div class="arrow right-arrow">&gt;</div>
                        <div class="list-container">
                            <ul class="list">
                                <?php
                                $file = fopen(__DIR__ . "/../data/sample_site_testimonials.csv", "r");

                                if ($file) {
                                    while (($data = fgetcsv($file)) !== false) {
                                        $description = $data[0];
                                        $author = $data[1];

                                        echo '<li>';
                                        echo '<div class="card center elevation-3 reviews-item">';
                                        echo '<div class="card-content body-medium">';
                                        echo '<p><em>"' . $description . '"</em></p>';
                                        echo '<p><b>— ' . $author . '</b></p>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</li>';
                                    }

                                    fclose($file);
                                }
                                ?>
                                ``

                            </ul>
                        </div>
                    </div>

                    <!-- Partners -->
                    <div class="column-container center partners-container">
                        <h2 class="display-large font-black primary-text">Our Partners</h2>
                        <div class="row-container">
                            <div class="partners-logo starbucks"></div>
                            <div class="partners-logo sm"></div>
                            <div class="partners-logo ayala-malls"></div>
                            <div class="partners-logo jco"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <div id="drawer" class="drawer hidden">
            <div class="column-container drawer-menu">
                <a class="drawer-logo">
                    <img src="<?php

                                if ($currentLoggedInUser != null) {
                                    echo $currentLoggedInUser->getProfilePicture();
                                } else {
                                    echo "../../res/images/image-placeholder.svg";
                                }

                                ?>" alt="Logo">
                </a>
                <a href="./profile/" class="navbar-dropdown-item">
                    <div class="row-container center-horizontal">
                        <span class="material-icons navbar-dropdown-item-icon">account_circle</span>
                        Profile
                    </div>
                </a>
                <a href="./profile/edit-profile.php" class="navbar-dropdown-item">
                    <div class="row-container center-horizontal">
                        <span class="material-icons navbar-dropdown-item-icon">settings</span>
                        Settings
                    </div>
                </a>
                <a href="./profile/booking-history.php" class="navbar-dropdown-item">
                    <div class="row-container center-horizontal">
                        <span class="material-icons navbar-dropdown-item-icon">auto_stories</span>
                        My Bookings
                    </div>
                </a>
                <a href="./profile/logout.php" class="navbar-dropdown-item">
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
                    <p><a href="./others/about-us.php" class="on-primary-text">About Us</a></p>
                    <p><a href="./others/faqs.php" class="on-primary-text">FAQs</a></p>
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
<script src="../scripts/navbar.js"></script>
<script src="../scripts/calendar.js"></script>
<script src="../scripts/home/handle-booking.js"></script>
<script src="../scripts/home/calendar-dropdown.js"></script>
<script src="../scripts/home/index.js"></script>
<script src="../scripts/home/room-dropdown.js"></script>
<script src="../scripts/validators.js"></script>
<script src="../scripts/snackbar.js"></script>
<script>
    // Validate guest number
    document.getElementById('guest_no').addEventListener('change', (e) => {
        e.target.value = validateGuestNumber(e.target.value);
    })
</script>


</html>