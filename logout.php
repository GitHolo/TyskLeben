<?php
// Start the session
session_start();

// Unset all of the session variables
session_unset();

// Destroy the session
session_destroy();

// Delete the cookie by setting its expiration date in the past
setcookie('user_ID', '', time() - 3600, "/");

// Redirect to the login page
header("Location: login.php");
exit();
?>