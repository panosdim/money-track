<?php

// Initialize the session.
session_start();
require_once 'database.php';

// Unset all of the session variables.
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

if (isset($_COOKIE['rememberme'])) {
    $selector = $_COOKIE['rememberme']['selector'];

    // Delete authentication token from DB
    $stmt = $db->prepare(
        'DELETE FROM `auth_tokens` WHERE `selector` = ?'
    );

    if ($stmt->execute([$selector])) {
        // Remove rememberme cookies
        setcookie("rememberme[series]", "", time() - 42000, '/', NULL, false, true);
        setcookie("rememberme[selector]", "", time() - 42000, '/', NULL, false, true);
        setcookie("rememberme[token]", "", time() - 42000, '/', NULL, false, true);

        // Unset all of the cookie variables.
        $_COOKIE = [];
    }
}

// Finally, destroy the session.
session_destroy();