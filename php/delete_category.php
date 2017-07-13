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

// Check if category is connected with expenses
$stmt = $db->prepare(
    'SELECT * FROM `expenses` WHERE `category` = ? AND `user_id` = ?'
);
$values = [$data['id'], $_SESSION['userId']];
if ($stmt->execute($values)) {
    $query = $stmt->fetch();

    if ($query !== false) {
        echo json_encode([
            "status" => "error",
            "message" => "Category is connected with expenses and can't be deleted.",
        ]);

        exit();
    }
}

// Delete category
if (isset($data['id'])) {
    $stmt = $db->prepare(
        'DELETE FROM `categories` WHERE `id` = ?'
    );

    if ($stmt->execute([$data['id']])) {
        echo json_encode([
            "status"  => "success",
            "message" => "Category was deleted successfully.",
            "item"    => $data
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
