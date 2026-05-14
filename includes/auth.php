<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function redirectToLogin($path = '../login.php') {
    header("Location: " . $path);
    exit();
}

function checkLogin($path = '../login.php') {
    if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
        redirectToLogin($path);
    }
}

function checkAdmin() {
    if (!isset($_SESSION['user']) || empty($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
        die("Access denied");
    }
}

function isUserLocked($user) {
    return isset($user['status']) && $user['status'] === 'locked';
}
