<?php
session_start(); // Start the session

require_once '../../data/common/Response.php';
require_once '../../data/service/Sanitizer.php';
require_once '../../data/model/User.php';
require_once '../../data/repository/UserRepository.php';

// Create a new user repository with the JSON data provider
$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is already logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    // Redirect to home page or any other authorized page
    header('Location: ../index.html');
    exit;
}

// Check if the form is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted form data
    $name = Sanitizer::sanitizeString($_POST['name']) ?? '';
    $email = Sanitizer::sanitizeEmail($_POST['email']) ?? '';
    $password = Sanitizer::sanitizeString($_POST['password']) ?? '';
    
    if(empty($name) || empty($email) || empty($password)) {
        // Throw an error if inputs were empty
        $errorMessage = 'Cannot sign up with an empty field!';
        header("Location: $_SERVER[PHP_SELF]?error=$errorMessage");
        exit;
    }

    // Create a new User object
    $user = new User();
    $user->setName($name);
    $user->setEmail($email);
    $user->setPassword($password);

    // Check if the user exists in the repository
    if (!$userRepository->createUser($user)) {
        // User is invalid, throw an error
        $errorMessage = 'User already exist!';
        header("Location: $_SERVER[PHP_SELF]?error=$errorMessage");
        exit;
    }

    // User is valid, redirect to the homepage or other authenticated page
    $_SESSION["loggedIn"] = true;
    $_SESSION["user"] = serialize($userRepository->getUserByEmail($user->getEmail()));
    header("Location: ../index.html");
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
    <style>
        /* Override global variables */
        :root {
            --logo-width: 8.5rem;
            --logo-height: 8.5rem;
            --textfield-width: 15.625rem;
            --button-size: 1rem 2rem;
            --gradient-image-width: 100%;
            --gradient-image-height: 100vh;
            --divider-height: 75vh;
        }

        .signup-form {
            padding-left: 3.5rem;
        }

        .signup-header {
            margin-top: 3rem;
            margin-bottom: 1rem;
        }

        .signup-field {
            margin-top: 3rem;
        }

        .signup-button {
            margin-top: 6rem;
            margin-bottom: 4rem;
        }

        .signup-form-divider {
            margin-left: 3.5rem;
            margin-right: 3.5rem;
        }

        .signup-headliner-image {
            width: 100%;
            padding-left: 1rem;
        }
    </style>
    <title>Sign up</title>
</head>
<body>
    <!-- Dialog error -->
    <div id="dialog-container">
        <div id="dialog">
            <h2 class="title-large roboto-bold">Error</h2>
            <p id="dialog-message"><?php 
                echo $_GET['error'];
                unset($_GET['error']);
            ?></p>
            <button class="button" id="dismiss-dialog">OK</button>
        </div>
    </div>

    <div class="row-container">
        <div class="row-container center fill-parent">
            <div class="column-container signup-form">
                <h1 class="display-medium text-center signup-header">Sign up</h1>
                <div class="card center ">
                    <div class="circle">
                        <img src="../../images/person.png" alt="Person">
                    </div>

                    <form action="" method="post">
                        <div class="column-container">
                            <div class="textfield-container signup-field">
                                <input type="text" name="name" class="textfield-input" placeholder=" " />
                                <label class="textfield-label">Name</label>
                                <div class="textfield-underline"></div>
                            </div>

                            <div class="textfield-container signup-field">
                                <input type="text" name="email" id="email" class="textfield-input" placeholder=" " />
                                <label class="textfield-label">Email</label>
                                <div class="textfield-underline"></div>
                                <span class="error-message">Invalid Email</span>
                            </div>
                            
                            <div class="textfield-container signup-field">
                                <input type="password" name="password" id="password" class="textfield-input" placeholder=" " />
                                <label class="textfield-label">Password</label>
                                <div class="textfield-underline"></div>
                                <span class="error-message">Password must be greater than 8 characters</span>
                            </div>
    
                            <button type="submit" class="button signup-button">Sign up</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="vertical-divider signup-form-divider"></div>
        </div>
        <div class="gradient-image-container signup-headliner-image">
            <img src="../../images/image-placeholder.svg" alt="Sign up Landscape">
        </div>
    </div>
</body>
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
</html>