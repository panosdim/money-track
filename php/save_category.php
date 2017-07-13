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

// Check if category with the same name already exists
$stmt = $db->prepare(
    'SELECT * FROM `categories` WHERE `category` = ?'
);
$values = [$data['category']];
if ($stmt->execute($values)) {
    $query = $stmt->fetch();

    if ($query !== false) {
        echo json_encode([
            "status" => "error",
            "message" => "Category with same name already exists.",
        ]);

        exit();
    }
}

// Check if it is an update or new entry
if (isset($data['id'])) {
    $stmt = $db->prepare(
        'UPDATE `categories` SET `category` = ? WHERE `id` = ?'
    );
    $values = [$data['category'], $data['id']];
} else {
    $stmt = $db->prepare(
        'INSERT INTO `categories` (`user_id`, `category`, `count`) VALUES (?, ?, 0)'
    );
    $values = [$_SESSION['userId'], $data['category']];
}

if ($stmt->execute($values)) {
    echo json_encode([
        "status" => "success",
        "message" => "Category was saved successfully.",
        "id" => $db->lastInsertId(),
    ]);
} else {
    // DB interaction was not successful. Inform user with message.
    echo json_encode([
        "status" => "error",
        "message" => "Problem executing statement in DB. Try again later."
    ]);
}
