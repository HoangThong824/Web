<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$message = "";

// --- Pagination Logic ---
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) as total FROM contacts");
$total_items = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_items / $limit);

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'mark_read') {
        $conn->query("UPDATE contacts SET status='read' WHERE id=$id");
        $message = "Đã đánh dấu là đã đọc.";
    } elseif ($action == 'delete') {
        $conn->query("DELETE FROM contacts WHERE id=$id");
        $message = "Đã xóa liên hệ.";
    } elseif ($action == 'mark_replied') {
        $conn->query("UPDATE contacts SET status='replied' WHERE id=$id");
        $message = "Đã đánh dấu là đã phản hồi.";
    }
}

include("header.php");
?>

<div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
    <div>
        <h4 class="text-2xl font-bold text-secondary mb-1">Liên hệ</h4>
        <nav class="text-sm text-slate-400">
            <a href="dashboard.php" class="hover:text-primary">Dashboard</a> / 
            <span class="text-slate-600">Quản lý liên hệ khách hàng</span>
        </nav>
    </div>
</div>

<?php if($message): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3 font-medium">
        <i class="fas fa-check-circle"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
            <tr>
                <th class="px-6 py-5 font-bold">Khách hàng</th>
                <th class="px-6 py-5 font-bold">Nội dung liên hệ</th>
                <th class="px-6 py-5 font-bold">Ngày gửi</th>
                <th class="px-6 py-5 font-bold">Trạng thái</th>
                <th class="px-6 py-5 font-bold text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php
            $res = $conn->query("SELECT * FROM contacts ORDER BY id DESC LIMIT $limit OFFSET $offset");
            while($row = $res->fetch_assoc()):
            ?>
            <tr class="hover:bg-slate-50/50 transition-all <?= $row['status'] == 'unread' ? 'bg-orange-50/30' : '' ?>">
                <td class="px-6 py-5">
                    <div class="font-bold text-secondary"><?= $row['name'] ?></div>
                    <div class="text-xs text-slate-400 font-medium"><?= $row['email'] ?></div>
                </td>
                <td class="px-6 py-5">
                    <p class="text-sm text-slate-600 line-clamp-2 italic" title="<?= $row['message'] ?>"><?= $row['message'] ?></p>
                </td>
                <td class="px-6 py-5 text-xs text-slate-400">
                    <?= date('H:i d/m/Y', strtotime($row['created_at'])) ?>
                </td>
                <td class="px-6 py-5">
                    <?php if($row['status'] == 'unread'): ?>
                        <span class="bg-orange-100 text-orange-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-tighter">Chưa đọc</span>
                    <?php elseif($row['status'] == 'read'): ?>
                        <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-tighter">Đã đọc</span>
                    <?php else: ?>
                        <span class="bg-green-100 text-green-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-tighter">Đã phản hồi</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex justify-end gap-2">
                        <?php if($row['status'] != 'replied'): ?>
                            <a href="contacts.php?action=mark_replied&id=<?= $row['id'] ?>" title="Đánh dấu đã phản hồi" class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-reply"></i>
                            </a>
                        <?php endif; ?>
                        <?php if($row['status'] == 'unread'): ?>
                            <a href="contacts.php?action=mark_read&id=<?= $row['id'] ?>" title="Đánh dấu đã đọc" class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-check"></i>
                            </a>
                        <?php endif; ?>
                        <a href="contacts.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa liên hệ này?')" title="Xóa" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
    <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-center gap-2">
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="contacts.php?page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all <?= $i == $page ? 'bg-primary text-white shadow-lg' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include("footer.php"); ?>