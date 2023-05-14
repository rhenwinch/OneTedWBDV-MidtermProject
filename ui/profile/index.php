<?php
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
        // Redirect to home page or any other authorized page
        header('Location: ../../ui/index.html');
        exit;
    }    
?>