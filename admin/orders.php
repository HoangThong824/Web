<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$message = "";

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['action'];
    
    $allowed_status = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($status, $allowed_status)) {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            $message = "Cập nhật trạng thái đơn hàng #" . $id . " thành công!";
        }
    }
}

// --- Pagination Logic ---
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) as total FROM orders");
$total_items = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_items / $limit);

include("header.php");
?>

<div class="mb-8">
    <h2 class="text-3xl font-bold text-secondary">Quản lý đơn hàng</h2>
    <p class="text-slate-500">Xem và cập nhật trạng thái đơn hàng của khách hàng.</p>
</div>

<?php if($message): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm uppercase tracking-wider">
            <tr>
                <th class="px-6 py-5 font-bold">Mã ĐH</th>
                <th class="px-6 py-5 font-bold">Khách hàng</th>
                <th class="px-6 py-5 font-bold">Tổng tiền</th>
                <th class="px-6 py-5 font-bold">Trạng thái</th>
                <th class="px-6 py-5 font-bold">Ngày đặt</th>
                <th class="px-6 py-5 font-bold text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php
            $res = $conn->query("SELECT o.*, u.fullname, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT $limit OFFSET $offset");
            while($row = $res->fetch_assoc()):
            ?>
            <tr class="hover:bg-slate-50/50 transition-all">
                <td class="px-6 py-5 font-bold text-secondary">#<?= $row['id'] ?></td>
                <td class="px-6 py-5">
                    <div class="font-bold text-secondary"><?= $row['fullname'] ?: $row['username'] ?></div>
                    <div class="text-xs text-slate-400">@<?= $row['username'] ?></div>
                </td>
                <td class="px-6 py-5 font-bold text-primary"><?= number_format($row['total_amount'], 0, ',', '.') ?>đ</td>
                <td class="px-6 py-5">
                    <?php 
                    $status_colors = [
                        'pending' => 'bg-orange-100 text-orange-600',
                        'processing' => 'bg-blue-100 text-blue-600',
                        'shipped' => 'bg-purple-100 text-purple-600',
                        'delivered' => 'bg-green-100 text-green-600',
                        'cancelled' => 'bg-red-100 text-red-600'
                    ];
                    $status_labels = [
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'shipped' => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'cancelled' => 'Đã hủy'
                    ];
                    ?>
                    <span class="<?= $status_colors[$row['status']] ?> text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-tighter">
                        <?= $status_labels[$row['status']] ?>
                    </span>
                </td>
                <td class="px-6 py-5 text-sm text-slate-500"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                <td class="px-6 py-5 text-right">
                    <div class="flex justify-end gap-2">
                        <select onchange="location.href='orders.php?id=<?= $row['id'] ?>&action='+this.value" class="bg-slate-50 border border-slate-200 text-xs rounded-lg px-2 py-1 outline-none">
                            <option value="">Cập nhật...</option>
                            <?php foreach($status_labels as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $row['status'] == $val ? 'disabled' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <!-- Pagination Area -->
    <?php if($total_pages > 1): ?>
    <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
        <span class="text-sm text-slate-500">Hiển thị trang <?= $page ?> / <?= $total_pages ?></span>
        <div class="flex gap-2">
            <?php if($page > 1): ?>
                <a href="orders.php?page=<?= $page-1 ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-primary hover:text-white transition-all">Trước</a>
            <?php endif; ?>
            
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="orders.php?page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-lg text-sm transition-all <?= $i == $page ? 'bg-primary text-white font-bold' : 'bg-white border border-slate-200 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
                <a href="orders.php?page=<?= $page+1 ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-primary hover:text-white transition-all">Tiếp</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include("footer.php"); ?>
