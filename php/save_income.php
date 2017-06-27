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
        'UPDATE `income` SET `amount` = ?, `date` = ?, `comment` = ? WHERE `id` = ?'
    );
    $values = [$data['amount'], $data['date'], $data['comment'], $data['id']];
} else {
    $stmt = $db->prepare(
        'INSERT INTO `income` (`user_id`, `amount`, `date`, `comment`) VALUES (?, ?, ?, ?)'
    );
    $values = [$_SESSION['userId'], $data['amount'], $data['date'], $data['comment']];
}

if ($stmt->execute($values)) {
    echo json_encode([
        "status"  => "success",
        "message" => "Income was saved successfully.",
        "id" => $db->lastInsertId(),
    ]);
} else {
    // DB interaction was not successful. Inform user with message.
    echo json_encode([
        "status"  => "error",
        "message" => "Problem executing statement in DB. Try again later."
    ]);
}
