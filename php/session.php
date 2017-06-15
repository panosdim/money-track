<?php

session_start();

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