<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$page_title = "Cá nhân";
include("includes/header.php");
?>

<section class="py-20 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar -->
            <div class="lg:w-1/3">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center">
                    <div class="w-24 h-24 bg-primary rounded-full mx-auto mb-6 flex items-center justify-center text-white text-4xl font-bold">
                        <?= strtoupper(substr($_SESSION['user']['username'], 0, 1)) ?>
                    </div>
                    <h2 class="text-2xl font-bold text-secondary"><?= $_SESSION['user']['fullname'] ?: $_SESSION['user']['username'] ?></h2>
                    <p class="text-slate-400 mb-8">@<?= $_SESSION['user']['username'] ?></p>
                    
                    <div class="space-y-2 text-left">
                        <a href="profile.php" class="flex items-center gap-4 p-4 rounded-xl bg-primary text-white font-bold transition-all">
                            <i class="fas fa-shopping-bag w-6"></i> Đơn hàng của tôi
                        </a>
                        <a href="logout.php" class="flex items-center gap-4 p-4 rounded-xl text-red-500 hover:bg-red-50 font-bold transition-all">
                            <i class="fas fa-sign-out-alt w-6"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="lg:w-2/3">
                <h3 class="text-3xl font-bold text-secondary mb-8">Lịch sử mua hàng</h3>
                
                <div class="space-y-6">
                    <?php
                    $orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
                    if ($orders->num_rows > 0):
                        while($order = $orders->fetch_assoc()):
                    ?>
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                <div>
                                    <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Mã đơn hàng</span>
                                    <h4 class="font-bold text-secondary">#<?= $order['id'] ?></h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Ngày đặt</span>
                                    <p class="font-bold text-secondary"><?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="divide-y divide-slate-100">
                                    <?php
                                    $order_id = $order['id'];
                                    $items = $conn->query("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
                                    while($item = $items->fetch_assoc()):
                                    ?>
                                        <div class="py-4 flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden">
                                                    <?php if($item['image'] && file_exists("uploads/".$item['image'])): ?>
                                                        <img src="uploads/<?= $item['image'] ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-fish"></i></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <a href="product_detail.php?id=<?= $item['product_id'] ?>" class="font-bold text-secondary hover:text-primary transition-colors"><?= $item['name'] ?></a>
                                                    <p class="text-xs text-slate-400">Số lượng: <?= $item['quantity'] ?></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-secondary"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</p>
                                                <?php if($order['status'] == 'delivered'): ?>
                                                    <a href="product_detail.php?id=<?= $item['product_id'] ?>#reviews" class="text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded font-bold hover:bg-primary hover:text-white transition-all">Đánh giá ngay</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                
                                <div class="mt-6 pt-6 border-t border-slate-100 flex justify-between items-center">
                                    <div>
                                        <span class="text-sm font-bold text-secondary mr-2">Trạng thái:</span>
                                        <?php 
                                        $status_labels = [
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'shipped' => 'Đang giao',
                                            'delivered' => 'Đã giao hàng',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        $status_colors = [
                                            'pending' => 'text-orange-500',
                                            'processing' => 'text-blue-500',
                                            'shipped' => 'text-purple-500',
                                            'delivered' => 'text-green-500',
                                            'cancelled' => 'text-red-500'
                                        ];
                                        ?>
                                        <span class="font-bold <?= $status_colors[$order['status']] ?>"><?= $status_labels[$order['status']] ?></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-slate-400 mr-2 text-sm">Tổng thanh toán:</span>
                                        <span class="text-xl font-bold text-primary"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; else: ?>
                        <div class="bg-white p-12 rounded-3xl border border-dashed border-slate-200 text-center">
                            <i class="fas fa-shopping-basket text-5xl text-slate-200 mb-4 block"></i>
                            <p class="text-slate-400">Bạn chưa có đơn hàng nào.</p>
                            <a href="products.php" class="text-primary font-bold hover:underline mt-4 inline-block">Đi mua sắm ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
