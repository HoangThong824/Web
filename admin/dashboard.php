<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();
include("header.php");

// Fetch statistics
$totalContacts = $conn->query("SELECT COUNT(*) as total FROM contacts")->fetch_assoc()['total'];
$unreadContacts = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE status='unread'")->fetch_assoc()['total'];
$totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
$totalNews = $conn->query("SELECT COUNT(*) as total FROM news")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pendingOrders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status='pending'")->fetch_assoc()['total'];
?>

<div class="mb-8">
    <h2 class="text-3xl font-bold text-secondary">Bảng điều khiển</h2>
    <p class="text-slate-500">Chào mừng bạn trở lại, hệ thống đang hoạt động ổn định.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-primary text-2xl">
            <i class="fas fa-box"></i>
        </div>
        <div>
            <span class="text-slate-500 text-sm font-medium">Sản phẩm</span>
            <h3 class="text-2xl font-bold text-secondary"><?= $totalProducts ?></h3>
        </div>
    </div>
    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 text-2xl">
            <i class="fas fa-shopping-basket"></i>
        </div>
        <div>
            <span class="text-slate-500 text-sm font-medium">Đơn hàng mới</span>
            <h3 class="text-2xl font-bold text-secondary"><?= $pendingOrders ?></h3>
        </div>
    </div>
    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 text-2xl">
            <i class="fas fa-envelope"></i>
        </div>
        <div>
            <span class="text-slate-500 text-sm font-medium">Liên hệ mới</span>
            <h3 class="text-2xl font-bold text-secondary"><?= $unreadContacts ?></h3>
        </div>
    </div>
    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 text-2xl">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <span class="text-slate-500 text-sm font-medium">Thành viên</span>
            <h3 class="text-2xl font-bold text-secondary"><?= $totalUsers ?></h3>
        </div>
    </div>
    <!-- Stat Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-6">
        <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 text-2xl">
            <i class="fas fa-box"></i>
        </div>
        <div>
            <span class="text-slate-500 text-sm font-medium">Tổng sản phẩm</span>
            <h3 class="text-2xl font-bold text-secondary"><?= $totalProducts ?></h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Quick Actions -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <h3 class="text-xl font-bold text-secondary mb-6 flex items-center gap-2">
            <i class="fas fa-bolt text-primary"></i> Thao tác nhanh
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="products.php?action=add" class="flex flex-col items-center justify-center p-6 rounded-xl border border-slate-100 bg-slate-50 hover:bg-primary hover:text-white transition-all group">
                <i class="fas fa-plus-circle text-2xl mb-2 text-primary group-hover:text-white"></i>
                <span class="font-bold">Thêm sản phẩm</span>
            </a>
            <a href="news.php?action=add" class="flex flex-col items-center justify-center p-6 rounded-xl border border-slate-100 bg-slate-50 hover:bg-primary hover:text-white transition-all group">
                <i class="fas fa-pen text-2xl mb-2 text-primary group-hover:text-white"></i>
                <span class="font-bold">Viết tin tức</span>
            </a>
            <a href="settings.php" class="flex flex-col items-center justify-center p-6 rounded-xl border border-slate-100 bg-slate-50 hover:bg-primary hover:text-white transition-all group">
                <i class="fas fa-cog text-2xl mb-2 text-primary group-hover:text-white"></i>
                <span class="font-bold">Cài đặt web</span>
            </a>
            <a href="contacts.php" class="flex flex-col items-center justify-center p-6 rounded-xl border border-slate-100 bg-slate-50 hover:bg-primary hover:text-white transition-all group">
                <i class="fas fa-reply text-2xl mb-2 text-primary group-hover:text-white"></i>
                <span class="font-bold">Phản hồi</span>
            </a>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-secondary flex items-center gap-2">
                <i class="fas fa-bell text-primary"></i> Liên hệ mới nhất
            </h3>
            <a href="contacts.php" class="text-sm text-primary font-bold hover:underline">Xem tất cả</a>
        </div>
        <div class="space-y-4">
            <?php
            $latest = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
            if ($latest->num_rows > 0):
                while($row = $latest->fetch_assoc()):
            ?>
                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-50 bg-slate-50/50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="font-bold text-secondary"><?= $row['name'] ?></p>
                            <p class="text-xs text-slate-500"><?= date('H:i d/m/Y', strtotime($row['created_at'])) ?></p>
                        </div>
                    </div>
                    <?php if($row['status'] == 'unread'): ?>
                        <span class="bg-orange-100 text-orange-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Mới</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; else: ?>
                <p class="text-slate-400 text-center py-10">Chưa có liên hệ nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>