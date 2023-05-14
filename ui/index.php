<?php
require_once __DIR__ . "/../data/model/User.php";
require_once __DIR__ . '/../data/repository/UserRepository.php';
require_once __DIR__ . '/../data/repository/RoomRepository.php';

session_start(); // Start the session

$userRepository = new UserRepository(__DIR__ . '/../data/users.json');
$roomRepository = new RoomRepository(__DIR__ . '/../data/rooms.json');
$roomFilters = ["standard", "suite", "deluxe"];
$selectedRoomFilter = "standard";
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    $selectedRoomFilter = $filter;
}

$currentLoggedInUser = null;

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

    <link rel="stylesheet" href="../css/theme/theme.css">
    <link rel="stylesheet" href="../css/home.css">
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
                        <img src="../../res/images/image-placeholder.svg" alt="Logo">
                    </a>
                </div>
                <div class="navbar-center"></div>
                <div class="navbar-end">
                    <?php if ($isLoggedIn) { ?>
                        <button class="navbar-profile-btn">
                            <img src="<?php echo $currentLoggedInUser->getProfilePicture(); ?>" alt="Profile" style="margin-right: 1rem">
                            <div class="column-container title-small text-left">
                                <span>Hi,
                                    <?php echo $currentLoggedInUser->getName(); ?>!
                                </span>
                                <span class="font-bold">Membership: None</span>
                            </div>
                            <span class="md-24 material-icons-outlined">arrow_drop_down</span>
                        </button>
                        <div class="navbar-dropdown">
                            <div class="column-container">
                                <a href="./profile/" class="navbar-dropdown-item">
                                    <div class="row-container center-horizontal">
                                        <span class="material-icons navbar-dropdown-item-icon">account_circle</span>
                                        Profile
                                    </div>
                                </a>
                                <a href="./profile/booking-history.html" class="navbar-dropdown-item">
                                    <div class="row-container center-horizontal">
                                        <span class="material-icons navbar-dropdown-item-icon">auto_stories</span>
                                        My Bookings
                                    </div>
                                </a>
                                <a href="#" class="navbar-dropdown-item">
                                    <div class="row-container center-horizontal">
                                        <span class="material-icons navbar-dropdown-item-icon">local_activity</span>
                                        Voucher
                                    </div>
                                </a>
                                <a href="#" class="navbar-dropdown-item">
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

                <div class="row-container navigation-button-container center-spaced">
                    <?php if ($isLoggedIn) { ?>
                        <a href="./profile/" class="button navigation-button font-medium"">
                            <div class=" row-container center">
                            <img src="<?php echo $currentLoggedInUser->getProfilePicture(); ?>" class="user" alt="Profile" style="margin-right: 1rem">
                            <div class="column-container title-small text-left">
                                <span>Hi,
                                    <?php echo $currentLoggedInUser->getName(); ?>!
                                </span>
                                <span class="font-bold">Membership: None</span>
                            </div>
                </div>
                </a>
            <?php } else { ?>
                <a href="./profile/sign_up.php" class="button navigation-button font-medium">Sign Up</a>
                <a href="./profile/login.php" class="button navigation-button font-medium">Login</a>
            <?php } ?>
            </div>
        </div>

        <div class="column-container">
            <div id="top-content">
                <div class="relative-container headline-container">
                    <div class="headline-image"></div>
                    <div class="column-container center headline-content">
                        <h1 class="display-large font-black">Grand Eden Oasis</h1>
                        <div class="card elevation-2 booking-selector-container">
                            <form class="h100" method="POST" action="./rooms/booking-form.php" id="form">
                                <div class="row-container center-spaced h100">
                                    <div class="relative-container w100">
                                        <button class="button booking-selector-button" id="roomButton">
                                            <div class="row-container center">
                                                <img class="booking-selector-button-icon" src="../res/images/Lable.svg" alt="An icon of rooms button">
                                                <input type="text" id="room-id" name="room_id" readonly hidden required/>
                                                <input type="text" id="room-type" name="room_type" readonly hidden required/>
                                                <input type="text" id="room-name" value="Rooms" readonly required/>
                                            </div>
                                        </button>
                                        <div id="roomTypeDropdown" class="dropdown">
                                            <ul>
                                                <li class="dropdown-item">Standard</li>
                                                <li class="dropdown-item">Suite</li>
                                                <li class="dropdown-item">Deluxe</li>
                                            </ul>
                                        </div>

                                        <div class="dropdown roomDropdown">
                                            <ul>
                                                <?php 
                                                    foreach ($roomRepository->getAllRoomsByType("standard") as $room) {
                                                ?>
                                                    <li 
                                                        class="dropdown-room-item"
                                                        data-id="<?php echo $room["id"]; ?>"
                                                    ><?php echo $room["name"]; ?></li>
                                                <?php 
                                                    }
                                                ?>
                                            </ul>
                                        </div>

                                        <div class="dropdown roomDropdown">
                                            <ul>
                                                <?php 
                                                    foreach ($roomRepository->getAllRoomsByType("suite") as $room) {
                                                ?>
                                                    <li 
                                                        class="dropdown-room-item"
                                                        data-id="<?php echo $room["id"]; ?>"
                                                    ><?php echo $room["name"]; ?></li>
                                                <?php 
                                                    }
                                                ?>
                                            </ul>
                                        </div>

                                        <div class="dropdown roomDropdown">
                                            <ul>
                                                <?php 
                                                    foreach ($roomRepository->getAllRoomsByType("deluxe") as $room) {
                                                ?>
                                                    <li 
                                                        class="dropdown-room-item"
                                                        data-id="<?php echo $room["id"]; ?>"
                                                    ><?php echo $room["name"]; ?></li>
                                                <?php 
                                                    }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="vertical-divider booking-selector-divider"></div>
                                    <div class="relative-container w100">
                                        <button class="button booking-selector-button" id="datesButton">
                                            <div class="row-container center">
                                                <img class="booking-selector-button-icon" src="../res/images/Date_range.svg" alt="An icon of dates button">
                                                <input type="text" id="date" name="date" value="Dates" readonly required/>
                                            </div>
                                        </button>
                                        <div class="calendar-dropdown">
                                            <div class="calendar-picker">
                                                <div class="calendar-header">
                                                    <span class="material-icons-outlined prev-month">navigate_before</span>
                                                    <select class="select select-month date-selector"></select>
                                                    <select class="select select-year date-selector"></select>
                                                    <span class="material-icons-outlined next-month">navigate_next</span>
                                                </div>
                                                <div class="calendar"></div>
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="vertical-divider booking-selector-divider"></div>
                                    <div class="row-container guest-no-container center-start">
                                        <div class="textfield-container">
                                            <div class="start-drawable guest-no-icon">
                                                <img src="../res/images/User.svg" alt="An icon of guest number button">
                                            </div>
                                            <input type="number" name="guest_no" id="guest_no" class="textfield-input guest-no-left-margin" placeholder="Guest No." required/>
                                        </div>
                                    </div>
                                    <button class="button booking-button" id="book">Book</button>
                                </div>
                            </form>
                        </div>

                        <h4 class="display-small font-light">Check in for the night, check out our tech might</h1>
                    </div>
                </div>
                <div class="column-container about-us-container ce">
                    <h4 class="title-large font-bold">About Us</h1>
                        <p class="body-large about-us-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Proin aliquet, urna ac congue volutpat, urna lectus ullamcorper enim, vitae facilisis
                            ante
                            eros eget risus. Proin eu velit est. In eu tristique quam. Nam ornare, sem sit amet
                            commodo
                            pharetra</p>
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
                            <ul id="standard" class="list">
                                <?php
                                foreach ($roomRepository->getAllRoomsByType("standard") as $room) {
                                ?>
                                    <li>
                                        <div class="card center elevation-3 room-item clickable">
                                            <div class="relative-container">
                                                <div class="initial-content">
                                                    <img class="room-item-image" src="
                                                        <?php echo $room["gallery"][0]; ?>">
                                                    <div class="card-content room-item-content body-medium">
                                                        <p>
                                                            <?php echo $room["description"]; ?>
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
                                                        <?php echo $room["description"]; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                            <ul id="suite" class="list invisible">
                                <?php
                                foreach ($roomRepository->getAllRoomsByType("suite") as $room) {
                                ?>
                                    <li>
                                        <div class="card center elevation-3 room-item clickable">
                                            <div class="relative-container">
                                                <div class="initial-content">
                                                    <img class="room-item-image" src="
                                                        <?php echo $room["gallery"][0]; ?>">
                                                    <div class="card-content room-item-content body-medium">
                                                        <p>
                                                            <?php echo $room["description"]; ?>
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
                                                        <?php echo $room["description"]; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                            <ul id="deluxe" class="list invisible">
                                <?php
                                foreach ($roomRepository->getAllRoomsByType("deluxe") as $room) {
                                ?>
                                    <li>
                                        <div class="card center elevation-3 room-item clickable">
                                            <div class="relative-container">
                                                <div class="initial-content">
                                                    <img class="room-item-image" src="
                                                        <?php echo $room["gallery"][0]; ?>">
                                                    <div class="card-content room-item-content body-medium">
                                                        <p>
                                                            <?php echo $room["description"]; ?>
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
                                                        <?php echo $room["description"]; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row-container">
                        <?php foreach ($roomFilters as $filter) { ?>
                            <button class="button room-category-filter font-medium <?php echo $filter == $selectedRoomFilter ? "active" : ""; ?>" id="<?php echo $filter; ?>">
                                <?php echo ucwords($filter); ?>
                            </button>
                        <?php } ?>
                    </div>


                    <!-- Amenities -->
                    <div class="column-container center amenities-container">
                        <h2 class="display-large font-black primary-text">Amenities</h2>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <div class="amenity-img-1"></div>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Amenity Title</h2>
                                <p>n eleifend leo, vitae eleifend odio. Donec elementum pretium aliquet. Phasellus
                                    luctus maximus volutpat. Phasellus pellentesque nunc ut massa tincidunt suere,
                                    mauris ante aliquam arcu, a fringilla massa arcu at nisl. Donec et elit quis
                                    tortor gravida euismod. Aliquam eget orci tincidunt, imperdie</p>
                            </div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Amenity Title</h2>
                                <p>n eleifend leo, vitae eleifend odio. Donec elementum pretium aliquet. Phasellus
                                    luctus maximus volutpat. Phasellus pellentesque nunc ut massa tincidunt suere,
                                    mauris ante aliquam arcu, a fringilla massa arcu at nisl. Donec et elit quis
                                    tortor gravida euismod. Aliquam eget orci tincidunt, imperdie</p>
                            </div>
                            <div class="amenity-img-1"></div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <div class="amenity-img-1"></div>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Amenity Title</h2>
                                <p>n eleifend leo, vitae eleifend odio. Donec elementum pretium aliquet. Phasellus
                                    luctus maximus volutpat. Phasellus pellentesque nunc ut massa tincidunt suere,
                                    mauris ante aliquam arcu, a fringilla massa arcu at nisl. Donec et elit quis
                                    tortor gravida euismod. Aliquam eget orci tincidunt, imperdie</p>
                            </div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Amenity Title</h2>
                                <p>n eleifend leo, vitae eleifend odio. Donec elementum pretium aliquet. Phasellus
                                    luctus maximus volutpat. Phasellus pellentesque nunc ut massa tincidunt suere,
                                    mauris ante aliquam arcu, a fringilla massa arcu at nisl. Donec et elit quis
                                    tortor gravida euismod. Aliquam eget orci tincidunt, imperdie</p>
                            </div>
                            <div class="amenity-img-1"></div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <div class="amenity-img-1"></div>
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Amenity Title</h2>
                                <p>n eleifend leo, vitae eleifend odio. Donec elementum pretium aliquet. Phasellus
                                    luctus maximus volutpat. Phasellus pellentesque nunc ut massa tincidunt suere,
                                    mauris ante aliquam arcu, a fringilla massa arcu at nisl. Donec et elit quis
                                    tortor gravida euismod. Aliquam eget orci tincidunt, imperdie</p>
                            </div>
                        </div>

                        <!-- Amenity item -->
                        <div class="row-container center">
                            <div class="column-container amenities-text-container">
                                <h2 class="font-bold">Amenity Title</h2>
                                <p>n eleifend leo, vitae eleifend odio. Donec elementum pretium aliquet. Phasellus
                                    luctus maximus volutpat. Phasellus pellentesque nunc ut massa tincidunt suere,
                                    mauris ante aliquam arcu, a fringilla massa arcu at nisl. Donec et elit quis
                                    tortor gravida euismod. Aliquam eget orci tincidunt, imperdie</p>
                            </div>
                            <div class="amenity-img-1"></div>
                        </div>
                    </div>


                    <!-- Reviews -->
                    <h2 class="display-large font-black primary-text">Testimonials</h2>
                    <div class="horizontal-list reviews-list-container">
                        <div class="arrow left-arrow">&lt;</div>
                        <div class="arrow right-arrow">&gt;</div>
                        <div class="list-container">
                            <ul class="list">
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="card center elevation-3 reviews-item">
                                        <div class="card-content body-medium">
                                            <p>
                                                <em>
                                                    "Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                    Aliquam
                                                    vulputate lorem mauris, et rutrum augue placerat vel. Morbi
                                                    tincidunt id
                                                    libero sit amet viverra. Duis"
                                                </em>
                                            </p>
                                            <p><b>— Mr. Ipsum</b></p>
                                        </div>
                                    </div>
                                </li>
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
    </div>
    
    <div id="snackbar" class="error-snackbar">Error: Something went wrong.</div>
    <footer class="main-footer">
        <div class="row-container footer-content">
            <div class="column-container company-info">
                <p>Hotel Name: ABC Hotel</p>
                <p>Address: 123 Main Street, Anytown USA</p>
                <p>Phone: (123) 456-7890</p>
                <p>Email: info@abchotel.com</p>
                <p>Website: www.abchotel.com</p>
                <p>Social Media: Links to Facebook, Twitter, Instagram, LinkedIn</p>
            </div>
            <div class="column-container other-info">
                <p>About Us</p>
                <p>FAQs</p>
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