<?php
require_once __DIR__ . "/../../data/model/User.php";
require_once __DIR__ . '/../../data/service/UserService.php';
require_once '../../data/service/Sanitizer.php';

session_start(); // Start the session

if(!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}

// Check if user is not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] === false) {
    // Redirect to home page or any other authorized page
    header('Location: login.php');
    exit;
}

$userService = new UserService();
$currentLoggedInUser = unserialize($_SESSION['user']);

// Check if site has been hard refreshed
$isSiteHardRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] === 'no-cache');
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
    $currentLoggedInUser = $userUpdater->updateUser(
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

    <link rel="stylesheet" href="../../css/theme/theme.css">
    <title>Edit Profile</title>
    <style>
        :root {
            --navbar-logo-size: 8.5rem;
            --textfield-width: 15.8rem;
            --card-width: auto;
        }

        .profile-pic {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .profile-pic:hover img {
            filter: brightness(50%);
        }

        .overlay {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .profile-pic:hover .overlay {
            opacity: 1;
        }

        input[type="file"] {
            border: none;
            outline: none;
            font-size: inherit;
            color: inherit;
            background: transparent;
            cursor: pointer;
            opacity: 0;
        }

        .overlay .text {
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            text-align: center;
            transition: opacity 0.5s ease;
            opacity: 0;
            margin-top: 1.5rem;
        }

        .profile-pic:hover .text {
            opacity: 1;
        }

        .margin-1 {
            margin-top: 3rem;
            margin-bottom: 3rem;
        }

        .margin-2 {
            margin-top: 2rem;
        }

        .margin-3 {
            margin-right: 1.5rem;
            margin-left: 1.5rem;
        }

        .card-padding {
            padding: 3rem;
        }
    </style>
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
        <nav class="navbar">
            <div class="navbar-content">
                <div class="navbar-start">
                    <span class="material-icons md-36" id="menu-button">menu</span>
                </div>
                <div class="navbar-center">
                </div>
                <div class="navbar-end">
                    <div class="row-container">
                        <button class="button" style="--button-size: 0.75rem 2.5rem; margin-right: 2rem;">Find
                            Rooms</button>
                        <button class="navbar-profile-btn">
                            <img src="<?php echo $currentLoggedInUser->getProfilePicture(); ?>" alt="Profile" style="margin-right: 1rem">
                            <div class="column-container title-small text-left">
                                <span>Hi, <?php echo $currentLoggedInUser->getName(); ?>!</span>
                                <span class="font-bold">Membership: None</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        <div id="drawer" class="drawer">
            <nav class="drawer-nav">
                <ul>
                    <li>
                        <div class="row-container center">
                            <a href="#" class="navbar-logo navbar-dropdown-item">
                                <img src="../../res/images/image-placeholder.svg" alt="Logo" class="large-logo">
                            </a>
                        </div>
                    </li>
                    <li><a href="#" class="navbar-dropdown-item">Profile</a></li>
                    <li><a href="#" class="navbar-dropdown-item">My Bookings</a></li>
                    <li><a href="#" class="navbar-dropdown-item">Vouchers</a></li>
                    <li>
                        <a href="#" class="navbar-dropdown-item">
                            <div class="card" style="--card-width: auto">
                                <div class="card-content">
                                    <div class="column-container center">
                                        <span class="text-center">Log Out</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="drawer-backdrop" class="drawer-backdrop"></div>
        <div class="wrap-content">
            <div class="column-container center">
                <h6 class="title-large font-medium">Edit Profile</h6>
                <div class="card">
                    <div class="card-content card-padding">
                        <form method="post" enctype="multipart/form-data">
                            <div class="column-container center">
                                <div class="profile-pic">
                                    <img src="<?php echo $currentLoggedInUser->getProfilePicture(); ?>" alt="Profile Picture" id="preview">
                                    <div class="overlay column-container">
                                        <label for="profile-picture" class="text">Change Picture</label>
                                        <input type="file" id="profile-picture" name="profile_picture">
                                    </div>
                                </div>
                                <span class="margin-1">Profile Details:</span>
                                <div class="column-container center">
                                    <div class="row-container center-vertical">
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
</body>
<script src="../../scripts/dialog.js"></script>
<script src="../../scripts/validators.js"></script>
<script>
    // Profile picture previewer
    document.getElementById("profile-picture").addEventListener('change', (event) => {
        previewImage(event);
    });

    // Function for previewing the image
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function() {
            preview.src = reader.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "#";
        }
    }
</script>
<script>
    // Select DOM elements
    const menuButton = document.getElementById('menu-button');
    const drawer = document.getElementById('drawer');
    const drawerBackdrop = document.getElementById('drawer-backdrop');

    // Define functions
    function openDrawer() {
        drawer.classList.add('open');
        drawerBackdrop.classList.add('open');
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        drawerBackdrop.classList.remove('open');
    }

    // Attach event listeners
    menuButton.addEventListener('click', () => {
        if (drawer.classList.contains('open')) {
            closeDrawer();
        } else {
            openDrawer();
        }
    });

    drawerBackdrop.addEventListener('click', () => {
        closeDrawer();
    });

    drawer.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });

    drawer.addEventListener('touchmove', (e) => {
        endX = e.touches[0].clientX;
    });

    drawer.addEventListener('touchend', () => {
        if (drawer.classList.contains('open') && !drawer.contains(event.target) && endX < startX) {
            closeDrawer();
        }
    });

    window.addEventListener('resize', () => {
        if (drawer.classList.contains('open')) {
            closeDrawer();
        }
    });
</script>
<script>
    // Validate fields
    document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('name');
    const contactNumberInput = document.getElementById('contact-number');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    emailInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validateEmail(e.target.value) || isEmpty(e.target.value);
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    nameInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = isEmpty(e.target.value);
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    contactNumberInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validatePhilippinePhoneNumber(e.target.value) || isEmpty(e.target.value);
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });

    passwordInput.addEventListener('input', (e) => {
        const parentContainer = e.target.parentElement;
        const isValid = validatePassword(e.target.value) || isEmpty(e.target.value);
        
        if(isValid) {
            parentContainer.classList.remove('error-container');
        } else {
            parentContainer.classList.add('error-container');
        }
    });
});
</script>

</html>