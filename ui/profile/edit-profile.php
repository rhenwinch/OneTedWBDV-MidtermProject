<?php
require_once __DIR__ . "/../../data/model/User.php";
require_once __DIR__ . '/../../data/service/UserService.php';
require_once __DIR__ . '/../../data/service/Sanitizer.php';
require_once __DIR__ . '/../../data/repository/UserRepository.php';

session_start(); // Start the session

$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}

// Check if user is not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] === false) {
    // Redirect to home page or any other authorized page
    header('Location: login.php');
    exit;
}

$userService = new UserService($userRepository);
$currentLoggedInUser = unserialize($_SESSION['user']);

// Check if site has been hard refreshed
$isSiteHardRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && isset($_SESSION["user"]) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] === 'no-cache');
if ($isSiteHardRefreshed) {
    $updatedUser = $userService->getUpdatedUser($currentLoggedInUser->getUserId());
    $_SESSION['user'] = serialize($updatedUser);

    $currentLoggedInUser = $updatedUser;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted form data
    $name = Sanitizer::sanitizeString($_POST['name']);
    $email = Sanitizer::sanitizeEmail($_POST['email']);
    $password = Sanitizer::sanitizeString($_POST['password']);
    $contactNumber = Sanitizer::sanitizeString($_POST['contact_number']);
    $profilePicturePath = null;

    if (empty($email) || empty($password) || empty($name)) {
        // Throw an error if inputs were empty
        $errorMessage = 'Name, Email or Password should not be empty!';
        header("Location: $_SERVER[PHP_SELF]?error=$errorMessage");
        exit;
    }

    // Check if the form was submitted and a file was uploaded successfully
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        // Define the directory to save the uploaded file
        $uploadDirectory = "../../res/images/users/" . $currentLoggedInUser->getUserId() . "/";

        // Create the directory if it doesnt exist
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Generate a unique name for the file
        $fileName = basename($_FILES['profile_picture']['name']);

        // Define the path to save the uploaded file
        $profilePicturePath = $uploadDirectory . $fileName;

        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profilePicturePath);
    }

    // Create a new User object
    $currentLoggedInUser = $userService->updateUser(
        $currentLoggedInUser,
        $email,
        $password,
        $name,
        $contactNumber,
        $profilePicturePath
    );

    $_SESSION['user'] = serialize($currentLoggedInUser);
    header("Location: index.php");
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
    <link rel="stylesheet" href="../../css/edit-profile.css">
    <link rel="stylesheet" href="../../css/edit-profile-mobile.css">
    <title>Edit Profile</title>
</head>

<body>
    <!-- Dialog error -->
    <div id="dialog-container">
        <div id="dialog">
            <h2 class="title-large font-bold">Error</h2>
            <p id="dialog-message"><?php
                                    echo $_GET['error'];
                                    unset($_GET['error']);
                                    ?></p>
            <button class="button" id="dismiss-dialog">OK</button>
        </div>
    </div>

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
                    <a class="button navbar-item active" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Settings
                    </a>
                    <a href="./booking-history.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        History
                    </a>
                    <a href="./logout.php" class="button navbar-item" style="--button-border-radius: 0; margin: 0 0.8rem; padding: 0.625rem 3rem;">
                        Logout
                    </a>
                </div>
            </div>
        </nav>
        <div class="wrap-content">
            <div class="column-container main-container center">
                <h6 class="title-large font-medium">Edit Profile</h6>
                <div class="card main-content">
                    <div class="card-content card-padding">
                        <form method="post" enctype="multipart/form-data">
                            <div class="column-container center">
                                <div class="profile-pic">
                                    <img src="<?php echo $currentLoggedInUser->getProfilePicture(); ?>" alt="Profile Picture" id="preview">
                                    <div class="profile-overlay column-container">
                                        <label for="profile-picture" class="text">Change Picture</label>
                                        <input type="file" id="profile-picture" name="profile_picture">
                                    </div>
                                </div>
                                <span class="margin-1 sub-header">Profile Details:</span>
                                <div class="column-container center">
                                    <div class="row-container forms center-vertical">
                                        <div class="column-container margin-3">
                                            <div class="textfield-container margin-2">
                                                <input type="text" name="name" id="name" class="textfield-input" placeholder=" " value="<?php echo $currentLoggedInUser->getName(); ?>" />
                                                <label class="textfield-label">Name</label>
                                                <div class="textfield-underline"></div>
                                                <span class="error-message">Invalid Name</span>
                                            </div>
                                            <div class="textfield-container margin-2">
                                                <input type="text" name="email" id="email" class="textfield-input" placeholder=" " value="<?php echo $currentLoggedInUser->getEmail(); ?>" />
                                                <label class="textfield-label">Email</label>
                                                <div class="textfield-underline"></div>
                                                <span class="error-message">Invalid Email</span>
                                            </div>
                                        </div>
                                        <div class="column-container margin-3">
                                            <div class="textfield-container margin-2">
                                                <input type="password" name="password" id="password" class="textfield-input" placeholder=" " />
                                                <label class="textfield-label">Password</label>
                                                <div class="textfield-underline"></div>
                                                <span class="error-message">Invalid Password</span>
                                            </div>
                                            <div class="textfield-container margin-2">
                                                <input type="text" name="contact_number" id="contact-number" class="textfield-input" placeholder=" " value="<?php echo $currentLoggedInUser->getPhoneNumber(); ?>" />
                                                <label class="textfield-label">Contact #</label>
                                                <div class="textfield-underline"></div>
                                                <span class="error-message">Invalid Number</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button id="save" class="button margin-2" style="--button-size: 0.65rem 3rem">Save Profile</button>
                                </div>
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
<script src="../../scripts/dialog.js"></script>
<script src="../../scripts/validators.js"></script>
<script src="../../scripts/edit-profile/index.js"></script>

</html>