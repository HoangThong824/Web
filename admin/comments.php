<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$message = "";

// Handle status updates
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $conn->query("UPDATE comments SET status='approved' WHERE id=$id");
        $message = "Đã duyệt bình luận.";
    } elseif ($action == 'hide') {
        $conn->query("UPDATE comments SET status='hidden' WHERE id=$id");
        $message = "Đã ẩn bình luận.";
    } elseif ($action == 'delete') {
        $conn->query("DELETE FROM comments WHERE id=$id");
        $message = "Đã xóa bình luận.";
    }
}

include("header.php");
?>

<div class="mb-8">
    <h2 class="text-3xl font-bold text-secondary">Quản lý bình luận</h2>
    <p class="text-slate-500">Kiểm duyệt và quản lý các đánh giá sản phẩm từ khách hàng.</p>
</div>

<?php if($message): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm uppercase tracking-wider">
            <tr>
                <th class="px-6 py-4 font-bold">Thành viên</th>
                <th class="px-6 py-4 font-bold">Sản phẩm</th>
                <th class="px-6 py-4 font-bold">Nội dung</th>
                <th class="px-6 py-4 font-bold">Đánh giá</th>
                <th class="px-6 py-4 font-bold">Trạng thái</th>
                <th class="px-6 py-4 font-bold text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php
            $res = $conn->query("SELECT c.*, u.username, p.name as product_name FROM comments c JOIN users u ON c.user_id = u.id JOIN products p ON c.product_id = p.id ORDER BY c.id DESC");
            while($row = $res->fetch_assoc()):
            ?>
            <tr class="hover:bg-slate-50/50 transition-all">
                <td class="px-6 py-4 font-bold text-secondary">@<?= $row['username'] ?></td>
                <td class="px-6 py-4 text-sm text-slate-600"><?= $row['product_name'] ?></td>
                <td class="px-6 py-4">
                    <p class="text-xs text-slate-500 line-clamp-2" title="<?= $row['content'] ?>"><?= $row['content'] ?></p>
                </td>
                <td class="px-6 py-4">
                    <div class="flex text-orange-400 text-xs">
                        <?php for($i=0; $i<$row['rating']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <?php if($row['status'] == 'pending'): ?>
                        <span class="bg-orange-100 text-orange-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Chờ duyệt</span>
                    <?php elseif($row['status'] == 'approved'): ?>
                        <span class="bg-green-100 text-green-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Đã duyệt</span>
                    <?php else: ?>
                        <span class="bg-slate-100 text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Đã ẩn</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <?php if($row['status'] != 'approved'): ?>
                            <a href="comments.php?action=approve&id=<?= $row['id'] ?>" title="Duyệt" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition-all">
                                <i class="fas fa-check"></i>
                            </a>
                        <?php endif; ?>
                        <?php if($row['status'] != 'hidden'): ?>
                            <a href="comments.php?action=hide&id=<?= $row['id'] ?>" title="Ẩn" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center hover:bg-slate-600 hover:text-white transition-all">
                                <i class="fas fa-eye-slash"></i>
                            </a>
                        <?php endif; ?>
                        <a href="comments.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa bình luận này?')" title="Xóa" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include("footer.php"); ?>
