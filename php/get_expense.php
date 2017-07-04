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

// Find the income of the specific user id
$stmt = $db->prepare(
    'SELECT expenses.`id`, `amount`, `comment`, `date`, categories.`category` 
               FROM expenses INNER JOIN categories ON expenses.`category` = categories.`id` 
               WHERE expenses.`user_id` = ? ORDER BY `date` DESC'
);

if ($stmt->execute([$_SESSION['userId']])) {
    $query = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($query !== false) {
        echo json_encode($query, JSON_NUMERIC_CHECK);
    }
}