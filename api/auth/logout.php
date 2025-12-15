<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

$currentPath = $_SERVER['SCRIPT_NAME'];
$depth = substr_count($currentPath, '/') - 1;
$redirectPath = str_repeat('../', $depth) . 'index.php';

// Redirect to login page
header('Location: ' . $redirectPath);
exit();
?>
