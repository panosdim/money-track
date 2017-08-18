<?php

session_start();
require_once 'database.php';

function generateToken($length = 20)
{
    return bin2hex(random_bytes($length));
}

// after the page reloads, print them out
if (isset($_COOKIE['rememberme'])) {
    $selector = $_COOKIE['rememberme']['selector'];
    $series = $_COOKIE['rememberme']['series'];
    $token = $_COOKIE['rememberme']['token'];

    // Get hashed token for user
    $stmt = $db->prepare(
        'SELECT `id`, `username`, `user_id`, `expire`, `series`, `hashedToken`, `expire` FROM `auth_tokens` WHERE selector = ?'
    );

    if ($stmt->execute([$selector])) {
        $query = $stmt->fetch();

        if ($query !== false) {
            // Check if authentication token is expired
            if (time() < strtotime($query['expire'])) {
                // Check for valid series
                if (hash_equals($series, $query['series'])) {
                    $hashedToken = hash('sha256', $token);
                    // Check for valid token
                    if (hash_equals($hashedToken, $query['hashedToken'])) {
                        $_SESSION['userId'] = $query['user_id'];
                        $_SESSION['username'] = $query['username'];

                        // Generate a new token and update DB
                        $token = generateToken();
                        $hashedToken = hash('sha256', $token);

                        $stmt = $db->prepare(
                            'UPDATE `auth_tokens` SET `hashedToken` = ? WHERE `id` = ?'
                        );

                        if ($stmt->execute([$hashedToken, $query['id']])) {
                            setcookie("rememberme[token]", $token, strtotime($query['expire']), '/', NULL, false, true);
                        }
                    } else {
                        // Invalid token when series and selector are valid means cookie stolen attack
                        // Delete ALL authentication tokens for the user

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
                                'DELETE FROM `auth_tokens` WHERE `username` = ?'
                            );

                            if ($stmt->execute([$query['username']])) {
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
                    }
                }
            }
        }
    }
}

$sess = [];
if (isset($_SESSION['userId'])) {
    $sess["loggedIn"] = true;
    $sess["userId"] = $_SESSION['userId'];
    $sess["username"] = $_SESSION['username'];
} else {
    $sess["loggedIn"] = false;
    $sess["userId"] = '';
    $sess["username"] = '';
}

echo json_encode($sess);