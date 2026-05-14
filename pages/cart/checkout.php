<?php
session_start();
include("../../includes/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php?redirect=checkout.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$user_info = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipping_name = trim($_POST['shipping_name']);
    $shipping_phone = trim($_POST['shipping_phone']);
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    
    if (empty($shipping_name) || empty($shipping_phone) || empty($shipping_address)) {
        $error_msg = "Vui lòng nhập đầy đủ thông tin giao hàng!";
    } else {
        $total_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['qty'];
        }

        // 1. Create Order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, shipping_name, shipping_phone, shipping_address, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("idssss", $user_id, $total_amount, $payment_method, $shipping_name, $shipping_phone, $shipping_address);

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
    }
}

$page_title = "Thanh toán";
include("../../includes/header.php");
?>

<section class="py-20 bg-slate-50">
    <div class="container mx-auto px-4 max-w-5xl">
        <?php if ($success_msg): ?>
            <div class="bg-white p-12 rounded-3xl shadow-xl border border-slate-100 text-center max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-8">
                    <i class="fas fa-check"></i>
                </div>
                <h2 class="text-3xl font-bold text-secondary mb-4">Cảm ơn bạn!</h2>
                <p class="text-slate-600 mb-10"><?= $success_msg ?></p>
                <div class="flex flex-col gap-4">
                    <a href="../../index.php" class="bg-primary hover:bg-primary-dark text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/30">Quay lại trang chủ</a>
                    <a href="../auth/profile.php" class="text-slate-400 hover:text-secondary font-bold">Xem lịch sử đơn hàng</a>
                </div>
            </div>
        <?php else: ?>
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Checkout Form -->
                <div class="lg:w-2/3">
                    <div class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-slate-100">
                        <h2 class="text-3xl font-bold text-secondary mb-8">Thông tin giao hàng</h2>
                        
                        <?php if($error_msg): ?>
                            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-8 flex items-center gap-3">
                                <i class="fas fa-exclamation-circle"></i>
                                <span><?= $error_msg ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6" id="checkoutForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Họ và tên người nhận</label>
                                    <input type="text" name="shipping_name" value="<?= $user_info['fullname'] ?: '' ?>" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Số điện thoại</label>
                                    <input type="text" name="shipping_phone" value="<?= $user_info['phone'] ?: '' ?>" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Địa chỉ nhận hàng</label>
                                <textarea name="shipping_address" rows="3" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." required></textarea>
                            </div>

                            <div class="pt-8">
                                <h3 class="text-xl font-bold text-secondary mb-6">Phương thức thanh toán</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="relative flex items-center p-4 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all peer-checked:border-primary peer-checked:bg-orange-50">
                                        <input type="radio" name="payment_method" value="cod" class="w-5 h-5 accent-primary mr-4" checked>
                                        <div>
                                            <span class="block font-bold text-secondary">Thanh toán khi nhận hàng (COD)</span>
                                            <span class="text-xs text-slate-400">Trả tiền mặt khi Shipper giao hàng đến.</span>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center p-4 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all">
                                        <input type="radio" name="payment_method" value="bank" class="w-5 h-5 accent-primary mr-4">
                                        <div>
                                            <span class="block font-bold text-secondary">Chuyển khoản ngân hàng</span>
                                            <span class="text-xs text-slate-400">Chuyển khoản qua số tài khoản của shop.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-10">
                                <button type="submit" class="w-full bg-secondary hover:bg-slate-800 text-white font-bold py-5 rounded-2xl transition-all shadow-xl shadow-secondary/20 transform hover:-translate-y-1">
                                    Xác nhận đặt hàng ngay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Sidebar -->
                <div class="lg:w-1/3">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 sticky top-24">
                        <h3 class="text-xl font-bold text-secondary mb-6">Đơn hàng của bạn</h3>
                        <div class="space-y-4 max-h-80 overflow-y-auto mb-6 pr-2">
                            <?php 
                            $total = 0;
                            foreach ($_SESSION['cart'] as $item): 
                                $subtotal = $item['price'] * $item['qty'];
                                $total += $subtotal;
                            ?>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-slate-100">
                                        <?php if($item['image'] && file_exists("../../uploads/".$item['image'])): ?>
                                            <img src="../../uploads/<?= $item['image'] ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i class="fas fa-fish text-xs text-slate-300"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-secondary line-clamp-1"><?= $item['name'] ?></h4>
                                        <p class="text-xs text-slate-400">Số lượng: <?= $item['qty'] ?></p>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="space-y-3 pt-6 border-t border-slate-100">
                            <div class="flex justify-between text-sm text-slate-500">
                                <span>Tạm tính</span>
                                <span><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-500">
                                <span>Phí vận chuyển</span>
                                <span class="text-green-500">Miễn phí</span>
                            </div>
                            <div class="flex justify-between text-xl font-bold text-secondary pt-2">
                                <span>Tổng cộng</span>
                                <span class="text-primary"><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include("../../includes/footer.php"); ?>
