<?php
session_start();
include("../../includes/db.php");

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
    if ($qty < 1) $qty = 1;
    
    // Check if product exists
    $res = $conn->query("SELECT id, name, price, image FROM products WHERE id = $id");
    if ($product = $res->fetch_assoc()) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'qty' => $qty
            ];
        }
    }
}

header("Location: cart.php");
exit();
?>
