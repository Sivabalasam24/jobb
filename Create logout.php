<?php
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Clear remember me cookie
setcookie('remember_email', '', time() - 3600, '/');

// Redirect to login page
header("Location: login.php");
exit();
?>