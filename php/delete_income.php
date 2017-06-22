<?php

session_start();
// If not Login exit
if (!isset($_SESSION['userId'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You are not logged in.'
    ]);
    exit();
}

require_once 'database.php';

$data = json_decode(file_get_contents('php://input'), true);

// Check if it is an update or new entry
if (isset($data['id'])) {
    $stmt = $db->prepare(
        'DELETE FROM `income` WHERE `id` = ?'
    );

    if ($stmt->execute([$data['id']])) {
        echo json_encode([
            "status"  => "success",
            "message" => "Income was deleted successfully.",
        ]);
    } else {
        // DB interaction was not successful. Inform user with message.
        echo json_encode([
            "status"  => "error",
            "message" => "Problem executing statement in DB. Try again later."
        ]);
    }
} else {
    // DB interaction was not successful. Inform user with message.
    echo json_encode([
        "status"  => "error",
        "message" => "Problem executing statement in DB. Try again later."
    ]);
}
