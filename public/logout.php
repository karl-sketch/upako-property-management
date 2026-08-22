<?php
/**
 * UpaKo - Logout Handler
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Log the user out
logoutUser();

// Redirect to login page
header('Location: ' . SITE_URL . '/public/login.php?message=logged_out');
exit;
?>
