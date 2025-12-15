<?php
// Protect page - require authentication
if (!isLoggedIn()) {
    redirect('/index.php');
}

// Check if specific role is required
if (isset($required_role) && !hasRole($required_role)) {
    redirect('/index.php');
}
?>
