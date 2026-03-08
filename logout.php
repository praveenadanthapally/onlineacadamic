<?php
require_once 'includes/functions.php';

// Clear all session data
$_SESSION = [];

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Destroy session
session_destroy();

// Start new session for flash message
session_start();
$_SESSION['flash'] = [
    'type' => 'info',
    'message' => 'You have been successfully logged out.'
];

// Redirect to login page
header("Location: /login.php");
exit();
