<?php
session_start(); // Start the session

require_once '../common/RoomType.php';
require_once '../common/BookingStatus.php';
require_once '../common/Response.php';
require_once '../common/Sanitizer.php';
require_once '../model/User.php';
require_once '../repository/UserRepository.php';
require_once '../repository/UserRepositoryJsonDataProvider.php';

// Set the response content type to JSON
header('Content-Type: application/json');

// Create a new user repository with the JSON data provider
$jsonFilePath = __DIR__ . '/../users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is already logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    // Redirect to home page or any other authorized page
    header('Location: ../../ui/index.html');
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
        $error_message = 'Cannot sign up with an empty field!';
        header("Location: ../../ui/profile/sign_up.html?error=$error_message");
        exit;
    }

    // Create a new User object
    $user = new User();
    $user->setName($name);
    $user->setEmail($email);
    $user->setPassword($password);

    // Check if the user exists in the repository
    if (!$userRepository->createUser($user)) {
        // User is valid, redirect to the homepage or other authenticated page
        $error_message = 'User already exist!';
        header("Location: ../../ui/profile/sign_up.html?error=$error_message");
        exit;
    }

    $_SESSION["loggedIn"] = true;
    $_SESSION["user"] = serialize($user);
    header("Location: ../../ui/index.html");
    exit;
}

header("HTTP/1.1 500 Internal Server Error");
echo json_encode(["result" => Response::ERROR]);
?>