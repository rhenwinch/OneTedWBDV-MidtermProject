<?php
session_start();

echo $_SESSION["loggedIn"]."<br>";
echo $_SESSION["user"];
header("Location: /ui/");
exit;

?>