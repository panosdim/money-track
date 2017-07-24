<?php

session_start();
require_once 'database.php';

// Define variables and set to empty values
$username = $password = "";

// Define Helper functions
function check_password($hash, $password)
{
    $full_salt = substr($hash, 0, 29);
    $new_hash = crypt($password, $full_salt);
    return ($hash == $new_hash);
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data["username"];
$password = $data["password"];

// Get hashed password for user
$stmt = $db->prepare(
    'SELECT * FROM users WHERE username = ?'
);
if ($stmt->execute([$username])) {
    $query = $stmt->fetch();

    if ($query !== false) {
        // Hashing the DB password with the salt returns the same hash
        if (check_password($query['password'], $password)) {
            // Authentication successful - Set session
            echo json_encode([
                "status" => "info",
                "message" => "Login was successful.",
                "username" => $username
            ]);
            $_SESSION['userId'] = $query['id'];
            $_SESSION['username'] = $username;
        } else {
            // Authentication was not successful. Inform user with message.
            echo json_encode([
                "status" => "error",
                "message" => "Login was not successful."
            ]);
        }
    } else {
        // Authentication was not successful. Inform user with message.
        echo json_encode([
            "status" => "error",
            "message" => "Login was not successful."
        ]);
    }
} else {
    // DB interaction was not successful. Inform user with message.
    echo json_encode([
        "status" => "error",
        "message" => "Problem executing statement in DB. Try again later."
    ]);
}


