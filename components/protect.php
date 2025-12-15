<?php
// Protect page - require authentication
if (!isLoggedIn()) {
    $currentScript = $_SERVER['SCRIPT_NAME'];
    $depth = substr_count(dirname($currentScript), '/') - 1; // Subtract 1 for root level
    $backPath = str_repeat('../', $depth);
    redirect($backPath . 'index.php');
}

// Check if specific role is required
if (isset($required_role) && !hasRole($required_role)) {
    $currentScript = $_SERVER['SCRIPT_NAME'];
    $depth = substr_count(dirname($currentScript), '/') - 1; // Subtract 1 for root level
    $backPath = str_repeat('../', $depth);
    redirect($backPath . 'index.php');
}
?>
