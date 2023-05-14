<?php
require_once __DIR__ . "/../../data/model/User.php";
require_once '../../data/service/Sanitizer.php';
require_once __DIR__ . '/../../data/repository/UserRepository.php';

session_start(); // Start the session

$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] === false) {
    // Redirect to home page or any other authorized page
    header('Location: login.php');
    exit;
}

$currentLoggedInUser = unserialize($_SESSION['user']);

// Check if site has been hard refreshed
$isSiteHardRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] === 'no-cache');
if ($isSiteHardRefreshed) {
    // Create a new user repository with the JSON data provider
    $_SESSION['user'] = serialize($userRepository->getUserById($currentLoggedInUser->getUserId()));
    $currentLoggedInUser = $userRepository->getUserById($currentLoggedInUser->getUserId());
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Profile</title>
        <link rel="stylesheet" href="../../css/theme/theme.css">
        <link rel="stylesheet" href="../../css/user-profile.css">
    </head>
    <body>
        <div class="column-container">
            <nav class="navbar">
                <div class="navbar-content">
                    <div class="navbar-start">
                        <a href="../" class="navbar-logo">
                            <img src="../../res/images/image-placeholder.svg" alt="Logo">
                        </a>
                    </div>
                    <div class="navbar-center"></div>
                    <div class="navbar-end">
                        <button class="navbar-profile-btn">
                            <img src="<?php echo $currentLoggedInUser->getProfilePicture(); ?>" alt="Profile" style="margin-right: 1rem">
                            <div class="column-container title-small text-left">
                                <span>Hi, <?php echo $currentLoggedInUser->getName(); ?>!</span>
                                <span class="font-bold">Membership: None</span>
                            </div>
                            <span class="md-24 material-icons-outlined">arrow_drop_down</span>
                        </button>
                        <div class="navbar-dropdown">
                            <div class="column-container">
                                <a href="" class="navbar-dropdown-item">
                                    <div class="row-container center-horizontal">
                                        <span class="material-icons navbar-dropdown-item-icon">account_circle</span>
                                        Profile
                                    </div>
                                </a>
                                <a href="#" class="navbar-dropdown-item">
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
                    </div>
                </div>
            </nav>
    
            <div class="header">
                <div class="background"></div>
                <span class="welcome">
                    Welcome back, 
                    <span class="font-bold">
                        <?php echo $currentLoggedInUser->getName(); ?>
                    </span>
                </span>
            </div>
    
            <div class="status-con">
                <p style="font-size: 22px; font-weight: 300;">Reservation Status: <span class="font-bold">Accepted</span></p>
            </div>
    
            <?php 
                // Only show card if booking history is not empty!
                $bookingHistory = $currentLoggedInUser->getBookingHistory();
                if(!empty($bookingHistory)) {
            ?>
                    <?php $room = $bookingHistory[0]->getRoom(); ?>
                    <div class="info-con">
                    <p class="info-con-header">
                        <?php 

                        if($bookingHistory[0]->getBookingStatus() == BookingStatus::BOOKED) {
                            echo "In progress";
                        } else {
                            echo "Completed";
                        }
                        
                        ?>
                    </p>
                    <div class="card emphasis info-main-con">
                        <div class="card-content">
                            <div class="info-main">
                                <div>
                                    <p>Room Name: 
                                        <span class="font-medium">
                                            <?php echo $room->getRoomName(); ?>
                                        </span>
                                    </p>
                                    <p>Room Type:
                                        <span class="font-medium">
                                            <?php echo $room->getRoomType(); ?>
                                        </span>
                                    </p>
                                    <p>Room Address: 
                                        <span class="font-medium">
                                            <?php echo $room->getRoomAddress(); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="column-container">
                                    <p>Date of Departure</p>
                                    <span class="font-medium">
                                        <?php echo $bookingHistory[0]->getDepartureDate(); ?>
                                    </span>
                                </div>
                                <div class="column-container">
                                    <p>Date of Arrival</p>
                                    <span class="font-medium">
                                        <?php echo $bookingHistory[0]->getArrivalDate(); ?>
                                    </span>
                                </div>
                            </div>
            
                            <div style="margin-top: 70px;">
                                <p>Confirmation: 
                                    <span class="font-medium">
                                        Receipt
                                    </span>
                                </p>
                                <p>Status: 
                                    <span class="font-medium">
                                        <?php echo $bookingHistory[0]->getBookingStatus(); ?>
                                    </span>
                                </p>
                            </div>
            
                            <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                                <span>Price:<span class="font-medium"> ₱<?php echo number_format($bookingHistory[0]->calculateBookingPrice(), 2, '.', ','); ?></span></span>
                                <img src="../../res/images/barcode.png" alt="" class="barcode">
                            </div>
                        </div>
                    </div>
            </div>
            <?php } ?>
            <div class="promo-con">
                <div>
                    <h5 class="promo-header">Summer 2023</h5>
                    <p class="promo-overview">
                        Book your stay at our luxurious hotel and enjoy the ultimate vacation experience! With our current promo, 
                        you can enjoy special rates and exclusive perks that will make your stay unforgettable. Whether you're looking 
                        for a romantic getaway or a family vacation, our hotel offers something for everyone. Don't miss out on this amazing 
                        opportunity and book your stay today!
                    </p>
                </div>
                <div class="book-con">
                    <img src="../../res/images/image-placeholder.svg" alt="">
                    <button class="button">Book now</button>
                </div>
            </div>
        </div>
    </body>
</html>