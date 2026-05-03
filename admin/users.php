<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$message = "";

// Handle Actions (Ban/Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'delete') {
        $conn->query("DELETE FROM users WHERE id=$id AND role != 'admin'");
        $message = "Đã xóa người dùng.";
    }
}

include("header.php");
?>

<div class="mb-8">
    <h2 class="text-3xl font-bold text-secondary">Quản lý thành viên</h2>
    <p class="text-slate-500">Danh sách người dùng đăng ký trên hệ thống.</p>
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
                <th class="px-6 py-4 font-bold">Thành viên</th>
                <th class="px-6 py-4 font-bold">Email / SĐT</th>
                <th class="px-6 py-4 font-bold">Vai trò</th>
                <th class="px-6 py-4 font-bold">Ngày tham gia</th>
                <th class="px-6 py-4 font-bold text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php
            $res = $conn->query("SELECT * FROM users ORDER BY role ASC, id DESC");
            while($row = $res->fetch_assoc()):
            ?>
            <tr class="hover:bg-slate-50/50 transition-all">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold">
                            <?= strtoupper(substr($row['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="font-bold text-secondary"><?= ($row['fullname'] ?? '') ?: ($row['username'] ?? 'User') ?></div>
                            <div class="text-xs text-slate-400">@<?= $row['username'] ?? '' ?></div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-slate-600"><?= ($row['email'] ?? '') ?: 'Chưa cập nhật' ?></div>
                    <div class="text-xs text-slate-400"><?= $row['phone'] ?? '' ?></div>
                </td>
                <td class="px-6 py-4">
                    <?php if($row['role'] == 'admin'): ?>
                        <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">ADMIN</span>
                    <?php else: ?>
                        <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-full">USER</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-xs text-slate-500">
                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                </td>
                <td class="px-6 py-4 text-right">
                    <?php if($row['role'] != 'admin'): ?>
                        <div class="flex justify-end gap-2">
                            <a href="users.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa thành viên này?')" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include("footer.php"); ?>
