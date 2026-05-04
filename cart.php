<?php
session_start();
include("includes/db.php");

// Handle Actions (Remove, Update)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'remove' && isset($_GET['id'])) {
        unset($_SESSION['cart'][$_GET['id']]);
    } elseif ($_GET['action'] == 'clear') {
        unset($_SESSION['cart']);
    } elseif ($_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
        $id = $_GET['id'];
        $qty = intval($_GET['qty']);
        if ($qty < 1) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['qty'] = $qty;
        }
    }
    header("Location: cart.php");
    exit();
}

$page_title = "Giỏ hàng";
include("includes/header.php");
?>

<section class="bg-secondary py-16 text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Giỏ hàng của bạn</h1>
        <p class="text-slate-300">Hoàn tất đơn hàng để thưởng thức ngay những món đặc sản.</p>
    </div>
</section>

<section class="py-20">
    <div class="container mx-auto px-4">
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Cart Items -->
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-8 py-4 font-bold">Sản phẩm</th>
                                    <th class="px-8 py-4 font-bold">Đơn giá</th>
                                    <th class="px-8 py-4 font-bold">Số lượng</th>
                                    <th class="px-8 py-4 font-bold">Thành tiền</th>
                                    <th class="px-8 py-4 font-bold"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php 
                                $total = 0;
                                foreach ($_SESSION['cart'] as $id => $item): 
                                    $subtotal = $item['price'] * $item['qty'];
                                    $total += $subtotal;
                                ?>
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-100">
                                                    <?php if(isset($item['image']) && file_exists("uploads/".$item['image'])): ?>
                                                        <img src="uploads/<?= $item['image'] ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <i class="fas fa-fish"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="font-bold text-secondary"><?= $item['name'] ?></span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-slate-600 font-medium"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden w-fit bg-white">
                                                <button type="button" onclick="updateQty(<?= $id ?>, -1)" class="px-3 py-1 hover:bg-slate-50 border-r border-slate-200"><i class="fas fa-minus text-[10px] text-slate-400"></i></button>
                                                <span class="w-10 text-center font-bold text-secondary text-sm"><?= $item['qty'] ?></span>
                                                <button type="button" onclick="updateQty(<?= $id ?>, 1)" class="px-3 py-1 hover:bg-slate-50 border-l border-slate-200"><i class="fas fa-plus text-[10px] text-slate-400"></i></button>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 font-bold text-primary"><?= number_format($subtotal, 0, ',', '.') ?>đ</td>
                                        <td class="px-8 py-6 text-right">
                                            <a href="cart.php?action=remove&id=<?= $id ?>" class="text-slate-300 hover:text-red-500 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="p-8 bg-slate-50 flex justify-between items-center border-t border-slate-100">
                            <a href="products.php" class="text-secondary font-bold hover:text-primary flex items-center gap-2">
                                <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                            </a>
                            <a href="cart.php?action=clear" class="text-red-500 font-bold hover:underline" onclick="return confirm('Xóa toàn bộ giỏ hàng?')">Xóa toàn bộ</a>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:w-1/3">
                    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 sticky top-24">
                        <h3 class="text-2xl font-bold text-secondary mb-8">Tổng đơn hàng</h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-slate-600">
                                <span>Tạm tính</span>
                                <span class="font-bold"><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Phí vận chuyển</span>
                                <span class="font-bold text-green-500">Miễn phí</span>
                            </div>
                            <hr class="border-slate-100">
                            <div class="flex justify-between text-secondary text-xl font-bold">
                                <span>Tổng cộng</span>
                                <span class="text-primary"><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                        
                        <a href="checkout.php" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1 mb-4 flex items-center justify-center">
                            Tiến hành thanh toán
                        </a>
                        <p class="text-center text-xs text-slate-400">Đảm bảo an toàn 100% với các phương thức thanh toán phổ biến.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white p-20 rounded-3xl text-center border-2 border-dashed border-slate-200">
                <div class="text-slate-200 text-9xl mb-8"><i class="fas fa-shopping-basket"></i></div>
                <h2 class="text-3xl font-bold text-secondary mb-4">Giỏ hàng trống</h2>
                <p class="text-slate-500 mb-10">Bạn chưa chọn sản phẩm nào để thưởng thức.</p>
                <a href="products.php" class="bg-primary hover:bg-primary-dark text-white px-10 py-4 rounded-xl font-bold transition-all inline-block shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                    Đi mua sắm ngay
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function updateQty(id, amt) {
    // Current qty is known in the loop but we need it here. 
    // For simplicity, we just redirect and let PHP handle it.
    // In a real app, we'd use AJAX.
    const items = <?= json_encode($_SESSION['cart']) ?>;
    let newQty = items[id].qty + amt;
    window.location.href = `cart.php?action=update&id=${id}&qty=${newQty}`;
}
</script>

<?php include("includes/footer.php"); ?>
