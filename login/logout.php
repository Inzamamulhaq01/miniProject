<?php
session_start(); // Start the session

// Destroy all session data
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect the user to the login page or homepage
header("Location: auth.php"); // Or redirect to index.php, depending on your app
exit();
?>
