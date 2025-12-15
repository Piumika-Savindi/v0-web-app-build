<?php
session_start();

// Set timezone
date_default_timezone_set('UTC');

// Include database configuration
require_once __DIR__ . '/database.php';

// Get database connection
$db = Database::getInstance()->getConnection();

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to get current user
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return $_SESSION['user_data'] ?? null;
}

// Helper function to check user role
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

// Helper function to redirect
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Helper function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
