<?php
session_start(); // Start the session

require_once '../../data/service/Sanitizer.php';
require_once '../../data/model/User.php';
require_once '../../data/repository/UserRepository.php';

// Create a new user repository with the JSON data provider
$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is already logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    // Redirect to home page or any other authorized page
    header('Location: ../');
    exit;
}

// Check if the form is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted form data
    $email = Sanitizer::sanitizeEmail($_POST['email']) ?? '';
    $password = Sanitizer::sanitizeString($_POST['password']) ?? '';

    if (empty($email) || empty($password)) {
        // Throw an error if inputs were empty
        $errorMessage = 'Cannot login with an empty field!';
        header("Location: $_SERVER[PHP_SELF]?error=$errorMessage");
        exit;
    }

    // Create a new User object
    $user = new User($email, $password);

    // Check if the user exists in the repository
    if ($userRepository->userExists($user)) {
        // User is valid, redirect to the homepage or other authenticated page
        $_SESSION["loggedIn"] = true;
        $_SESSION["user"] = serialize($userRepository->getUserByEmail($user->getEmail()));
        header("Location: ../../");
        exit;
    }

    // User is invalid, throw an error
    $errorMessage = 'Invalid account credentials!';
    header("Location: $_SERVER[PHP_SELF]?error=$errorMessage");
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
    <link rel="stylesheet" href="../../css/login.css">
    <link rel="stylesheet" href="../../css/login-mobile.css">
    <title>Login</title>
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

    <nav class="navbar sticky-navbar" id="navbar">
        <div class="navbar-content">
            <div class="navbar-start">
                <a href="../" class="navbar-navigation-icon hidden">
                    <img src="../../res/images/arrow_back.svg" alt="Logo">
                </a>
                <a href="../" class="navbar-logo">
                    <img src="../../res/images/site_logo.svg" alt="Logo">
                </a>
            </div>
            <div class="navbar-center"></div>
            <div class="navbar-end"></div>
        </div>
    </nav>

    <div class="row-container">
        <div class="row-container center main-content fill-parent">
            <div class="column-container login-form">
                <h1 class="display-medium text-center login-header">Log in</h1>
                <div class="card center ">
                    <div class="circle">
                        <img src="../../res/images/person.png" alt="Person">
                    </div>

                    <form action="" method="post" id="login">
                        <div class="column-container">
                            <div class="textfield-container login-field">
                                <input type="text" name="email" id="email" class="textfield-input" placeholder=" " />
                                <label class="textfield-label">Email</label>
                                <div class="textfield-underline"></div>
                                <span class="error-message">Invalid Email</span>
                            </div>

                            <div class="textfield-container login-field">
                                <input type="password" name="password" id="password" class="textfield-input" placeholder=" " />
                                <label class="textfield-label">Password</label>
                                <div class="textfield-underline"></div>
                                <span class="error-message">Invalid Password</span>
                            </div>

                            <button type="submit" class="button login-button">Log in</button>
                            <a class="clickable text-center label-large" id="forgot-password">Forgot password</a>
                            <p class="login-footer">Don't have an account?
                                <a href="sign_up.php">Sign up here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="vertical-divider login-form-divider"></div>
        </div>
        <a href="../" class="gradient-image-container login-headliner-image">
        <img src="../../res/images/content/LOG IN LOG OUT/<?php echo rand(1, 3) ?>.jpeg" class="headliner-image" alt="Signup Landscape">
        </a>
    </div>
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        emailInput.addEventListener('input', (e) => {
            const parentContainer = e.target.parentElement;
            const isValid = validateEmail(e.target.value) || isEmpty(e.target.value);

            if (isValid) {
                parentContainer.classList.remove('error-container');
            } else {
                parentContainer.classList.add('error-container');
            }
        });

        passwordInput.addEventListener('input', (e) => {
            const parentContainer = e.target.parentElement;
            const isValid = validatePassword(e.target.value) || isEmpty(e.target.value);

            if (isValid) {
                parentContainer.classList.remove('error-container');
            } else {
                parentContainer.classList.add('error-container');
            }
        });
    });
</script>
<script>
    const dialogTitle = document.querySelector("#dialog h2");
    const dialogMsg = document.getElementById("dialog-message");
    const forgotPasswordButton = document.getElementById("forgot-password");
    
    forgotPasswordButton.addEventListener('click', () => {
        dialogTitle.textContent = 'Forgot your password?';
        dialogMsg.textContent = 'Relax and try to remember it!';
        
        dialogContainer.style.visibility = 'visible';
        dialogContainer.style.opacity = 1;

        document.getElementById('dismiss-dialog').addEventListener('click', () => {
            dialogTitle.textContent = 'Error';
        });
    });
</script>

</html>