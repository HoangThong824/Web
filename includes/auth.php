<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function checkLogin() {
    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }
}

function checkAdmin() {
    if ($_SESSION['user']['role'] !== 'admin') {
        die("Access denied");
    }
}