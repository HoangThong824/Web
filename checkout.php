<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['qty'];
}

// 1. Create Order
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("id", $user_id, $total_amount);

if ($stmt->execute()) {
    $order_id = $conn->insert_id;
    
    // 2. Create Order Items
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $item_stmt->bind_param("iiid", $order_id, $product_id, $item['qty'], $item['price']);
        $item_stmt->execute();
    }
    
    // 3. Clear Cart
    unset($_SESSION['cart']);
    
    $success_msg = "Đặt hàng thành công! Mã đơn hàng của bạn là #" . $order_id;
} else {
    $error_msg = "Có lỗi xảy ra trong quá trình đặt hàng. Vui lòng thử lại.";
}

$page_title = "Thanh toán";
include("includes/header.php");
?>

<section class="py-20">
    <div class="container mx-auto px-4 max-w-2xl text-center">
        <?php if (isset($success_msg)): ?>
            <div class="bg-white p-12 rounded-3xl shadow-xl border border-slate-100">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-8">
                    <i class="fas fa-check"></i>
                </div>
                <h2 class="text-3xl font-bold text-secondary mb-4">Cảm ơn bạn!</h2>
                <p class="text-slate-600 mb-10"><?= $success_msg ?></p>
                <div class="flex flex-col gap-4">
                    <a href="index.php" class="bg-primary hover:bg-primary-dark text-white px-8 py-4 rounded-xl font-bold transition-all">Quay lại trang chủ</a>
                    <a href="products.php" class="text-slate-400 hover:text-secondary font-bold">Tiếp tục mua sắm</a>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white p-12 rounded-3xl shadow-xl border border-slate-100">
                <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-8">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="text-3xl font-bold text-secondary mb-4">Lỗi đặt hàng</h2>
                <p class="text-slate-600 mb-10"><?= $error_msg ?></p>
                <a href="cart.php" class="bg-primary hover:bg-primary-dark text-white px-8 py-4 rounded-xl font-bold transition-all">Quay lại giỏ hàng</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include("includes/footer.php"); ?>
