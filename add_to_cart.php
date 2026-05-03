<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Check if product exists
    $res = $conn->query("SELECT id, name, price FROM products WHERE id = $id");
    if ($product = $res->fetch_assoc()) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'qty' => 1
            ];
        }
    }
}

header("Location: cart.php");
exit();
?>
