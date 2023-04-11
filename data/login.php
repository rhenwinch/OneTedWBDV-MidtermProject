<?php
session_start(); // Start the session

require_once './common/RoomType.php';
require_once './common/BookingStatus.php';
require_once './common/Response.php';
require_once './model/User.php';
require_once './repository/UserRepository.php';
require_once './repository/UserRepositoryJsonDataProvider.php';

// Set the response content type to JSON
header('Content-Type: application/json');

// Create a new user repository with the JSON data provider
$jsonFilePath = __DIR__ . '/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is already logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    // Redirect to home page or any other authorized page
    echo json_encode(["result" => Response::SUCCESS]);
    exit;
}

// Check if the form is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted form data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Create a new User object
    $user = new User();
    $user->setEmail($email);
    $user->setPassword($password);

    // Check if the user exists in the repository
    if ($userRepository->userExists($user)) {
        // User is valid, redirect to the homepage or other authenticated page
        $_SESSION["loggedIn"] = true;
        echo json_encode(["result" => Response::SUCCESS]);
    } else {
        // Invalid credentials, show an error message
        echo json_encode(["result" => Response::FAIL]);
    }
    
    exit;
}

header("HTTP/1.1 500 Internal Server Error");
echo json_encode(["result" => Response::ERROR]);
?>