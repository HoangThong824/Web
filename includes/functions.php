<?php
function getSetting($conn, $key) {
    $stmt = $conn->prepare("SELECT value FROM settings WHERE `key`=?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn();
}