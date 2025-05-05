<?php
// filepath: c:\xampp\htdocs\grow\GrowSmart_Web\user profile\logout.php
session_start();

// Clear all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Finally, destroy the session
session_destroy();

// Redirect to home page in the root directory (fixed path)
header("Location: ../home new.html");
exit();
?>