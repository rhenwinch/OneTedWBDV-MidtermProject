<?php
session_start(); // Start the session
require_once '../../data/model/User.php';
require_once '../../data/repository/UserRepository.php';

// Create a new user repository with the JSON data provider
$jsonFilePath = __DIR__ . '/../../data/users.json'; // path to the JSON file containing user data
$userRepository = new UserRepository($jsonFilePath);

// Check if user is not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] === false) {
    // Redirect to home page or any other authorized page
    header('Location: ../');
    exit;
}

unset($_SESSION['user']);
unset($_SESSION['loggedIn']);

header('Location: ../');
exit;
?>