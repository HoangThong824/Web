<?php
session_start();
// Use absolute path for DB include
include_once(__DIR__ . "/../includes/db.php");

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['success' => false, 'message' => 'Invalid action'];

if ($action == 'add') {
    $id = intval($_GET['id'] ?? 0);
    $qty = intval($_GET['qty'] ?? 1);
    if ($qty < 1) $qty = 1;

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
        $response = [
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng',
            'cart_count' => count($_SESSION['cart'])
        ];
    } else {
        $response['message'] = 'Sản phẩm không tồn tại';
    }
} elseif ($action == 'update') {
    $id = intval($_GET['id'] ?? 0);
    $qty = intval($_GET['qty'] ?? 0);

    if (isset($_SESSION['cart'][$id])) {
        if ($qty < 1) {
            unset($_SESSION['cart'][$id]);
            $response['removed'] = true;
        } else {
            $_SESSION['cart'][$id]['qty'] = $qty;
        }
        
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $response = [
            'success' => true,
            'message' => 'Cập nhật giỏ hàng thành công',
            'subtotal' => number_format($_SESSION['cart'][$id]['price'] * ($_SESSION['cart'][$id]['qty'] ?? 0), 0, ',', '.') . 'đ',
            'total' => number_format($total, 0, ',', '.') . 'đ',
            'cart_count' => count($_SESSION['cart'])
        ];
    }
} elseif ($action == 'remove') {
    $id = intval($_GET['id'] ?? 0);
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $response = [
            'success' => true,
            'message' => 'Đã xóa sản phẩm',
            'total' => number_format($total, 0, ',', '.') . 'đ',
            'cart_count' => count($_SESSION['cart'])
        ];
    }
}

echo json_encode($response);
