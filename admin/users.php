<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$message = "";
$message_type = "success";
$action = $_GET['action'] ?? 'list';
$selected_user = null;

function getUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function canManageUser($targetUser, $currentUserId) {
    if (!$targetUser) {
        return false;
    }

    if ((int)$targetUser['id'] === (int)$currentUserId) {
        return false;
    }

    if (($targetUser['role'] ?? 'user') === 'admin') {
        return false;
    }

    return true;
}

$currentAdminId = (int) $_SESSION['user']['id'];

if (isset($_GET['action']) && isset($_GET['id'])) {
    $targetId = (int) $_GET['id'];
    $targetUser = getUserById($conn, $targetId);

    if (in_array($_GET['action'], ['view', 'lock', 'unlock', 'reset_password'], true) && !$targetUser) {
        $message = "Không tìm thấy người dùng!";
        $message_type = "error";
        $action = 'list';
    } elseif (in_array($_GET['action'], ['lock', 'unlock', 'reset_password'], true) && !canManageUser($targetUser, $currentAdminId)) {
        $message = "Bạn không thể thao tác với tài khoản này!";
        $message_type = "error";
        $action = 'list';
    } elseif ($_GET['action'] === 'lock') {
        $stmt = $conn->prepare("UPDATE users SET status = 'locked' WHERE id = ?");
        $stmt->bind_param("i", $targetId);
        if ($stmt->execute()) {
            $message = "Đã khóa tài khoản @" . $targetUser['username'];
        } else {
            $message = "Không thể khóa tài khoản!";
            $message_type = "error";
        }
        $action = 'list';
    } elseif ($_GET['action'] === 'unlock') {
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $targetId);
        if ($stmt->execute()) {
            $message = "Đã mở khóa tài khoản @" . $targetUser['username'];
        } else {
            $message = "Không thể mở khóa tài khoản!";
            $message_type = "error";
        }
        $action = 'list';
    } elseif ($_GET['action'] === 'reset_password') {
        $newPassword = md5('123456');
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newPassword, $targetId);
        if ($stmt->execute()) {
            $message = "Đã reset mật khẩu của @" . $targetUser['username'] . " về mặc định: 123456";
        } else {
            $message = "Không thể reset mật khẩu!";
            $message_type = "error";
        }
        $action = 'list';
    } elseif ($_GET['action'] === 'view') {
        $selected_user = $targetUser;
    }
}

$search = trim($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';
$allowed_roles = ['admin', 'user'];
$allowed_statuses = ['active', 'locked'];

if (!in_array($role_filter, $allowed_roles, true)) {
    $role_filter = '';
}

if (!in_array($status_filter, $allowed_statuses, true)) {
    $status_filter = '';
}

$whereClauses = [];
$types = '';
$params = [];

if ($search !== '') {
    $whereClauses[] = "(username LIKE ? OR fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchParam = '%' . $search . '%';
    $types .= 'ssss';
    array_push($params, $searchParam, $searchParam, $searchParam, $searchParam);
}

if ($role_filter !== '') {
    $whereClauses[] = "role = ?";
    $types .= 's';
    $params[] = $role_filter;
}

if ($status_filter !== '') {
    $whereClauses[] = "status = ?";
    $types .= 's';
    $params[] = $status_filter;
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = ' WHERE ' . implode(' AND ', $whereClauses);
}

$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) as total FROM users" . $whereSql;
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total_items = $countStmt->get_result()->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_items / $limit));

$listSql = "SELECT * FROM users" . $whereSql . " ORDER BY role ASC, id DESC LIMIT ? OFFSET ?";
$listStmt = $conn->prepare($listSql);
$listTypes = $types . 'ii';
$listParams = $params;
$listParams[] = $limit;
$listParams[] = $offset;
$listStmt->bind_param($listTypes, ...$listParams);
$listStmt->execute();
$res = $listStmt->get_result();

$queryParams = [];
if ($search !== '') {
    $queryParams['search'] = $search;
}
if ($role_filter !== '') {
    $queryParams['role'] = $role_filter;
}
if ($status_filter !== '') {
    $queryParams['status'] = $status_filter;
}

include("header.php");
?>

<div class="mb-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
    <div>
        <h2 class="text-3xl font-bold text-secondary">Quản lý thành viên</h2>
        <p class="text-slate-500">Xem thông tin, reset mật khẩu và khóa/mở khóa tài khoản người dùng.</p>
    </div>
</div>

<?php if($message): ?>
    <div class="<?= $message_type === 'success' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' ?> p-4 rounded-xl mb-6 flex items-center gap-3 border">
        <i class="fas <?= $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-end">
        <div class="xl:col-span-2">
            <label class="block text-sm font-bold text-slate-700 mb-2">Tìm kiếm</label>
            <div class="relative">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tên, username, email, số điện thoại..." class="w-full bg-slate-50 pl-11 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Vai trò</label>
            <select name="role" class="w-full bg-slate-50 px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                <option value="">Tất cả vai trò</option>
                <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>User</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Trạng thái</label>
            <select name="status" class="w-full bg-slate-50 px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                <option value="">Tất cả trạng thái</option>
                <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="locked" <?= $status_filter === 'locked' ? 'selected' : '' ?>>Locked</option>
            </select>
        </div>

        <div class="flex gap-3 md:col-span-2 xl:col-span-4">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold transition-all">
                <i class="fas fa-filter mr-2"></i>Lọc dữ liệu
            </button>
            <a href="users.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold transition-all">
                Xóa bộ lọc
            </a>
        </div>
    </form>
</div>

<?php if($selected_user): ?>
    <?php
    $avatarPath = "../uploads/" . ($selected_user['avatar'] ?? '');
    $hasAvatar = !empty($selected_user['avatar']) && $selected_user['avatar'] !== 'default_avatar.png' && file_exists($avatarPath);

    $orderCountStmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
    $orderCountStmt->bind_param("i", $selected_user['id']);
    $orderCountStmt->execute();
    $orderCount = $orderCountStmt->get_result()->fetch_assoc()['total'] ?? 0;

    $commentCountStmt = $conn->prepare("SELECT COUNT(*) as total FROM comments WHERE user_id = ?");
    $commentCountStmt->bind_param("i", $selected_user['id']);
    $commentCountStmt->execute();
    $commentCount = $commentCountStmt->get_result()->fetch_assoc()['total'] ?? 0;
    ?>
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div class="flex items-center gap-5">
                <div class="w-24 h-24 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center text-slate-400 text-3xl font-bold border border-slate-200">
                    <?php if($hasAvatar): ?>
                        <img src="<?= $avatarPath ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($selected_user['username'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-secondary"><?= ($selected_user['fullname'] ?: $selected_user['username']) ?></h3>
                    <p class="text-slate-400">@<?= $selected_user['username'] ?></p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="users.php<?= !empty($queryParams) ? '?' . http_build_query($queryParams) : '' ?>" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">Quay lại danh sách</a>
                <?php if(canManageUser($selected_user, $currentAdminId)): ?>
                    <a href="users.php?action=reset_password&id=<?= $selected_user['id'] ?>" onclick="return confirm('Reset mật khẩu user này về 123456?')" class="px-5 py-3 rounded-xl bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 font-bold transition-all">Reset mật khẩu</a>
                    <?php if(($selected_user['status'] ?? 'active') === 'locked'): ?>
                        <a href="users.php?action=unlock&id=<?= $selected_user['id'] ?>" class="px-5 py-3 rounded-xl bg-green-50 hover:bg-green-600 hover:text-white text-green-600 font-bold transition-all">Mở khóa</a>
                    <?php else: ?>
                        <a href="users.php?action=lock&id=<?= $selected_user['id'] ?>" onclick="return confirm('Khóa tài khoản này?')" class="px-5 py-3 rounded-xl bg-red-50 hover:bg-red-600 hover:text-white text-red-600 font-bold transition-all">Khóa tài khoản</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Vai trò</p>
                <p class="font-bold text-secondary"><?= strtoupper($selected_user['role']) ?></p>
            </div>
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Trạng thái</p>
                <p class="font-bold <?= ($selected_user['status'] ?? 'active') === 'locked' ? 'text-red-600' : 'text-green-600' ?>">
                    <?= ($selected_user['status'] ?? 'active') === 'locked' ? 'Đã khóa' : 'Hoạt động' ?>
                </p>
            </div>
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Đơn hàng</p>
                <p class="font-bold text-secondary"><?= $orderCount ?></p>
            </div>
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Bình luận</p>
                <p class="font-bold text-secondary"><?= $commentCount ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Họ và tên</p>
                <p class="font-bold text-secondary"><?= htmlspecialchars($selected_user['fullname'] ?: 'Chưa cập nhật') ?></p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Email</p>
                <p class="font-bold text-secondary"><?= htmlspecialchars($selected_user['email'] ?: 'Chưa cập nhật') ?></p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Số điện thoại</p>
                <p class="font-bold text-secondary"><?= htmlspecialchars($selected_user['phone'] ?: 'Chưa cập nhật') ?></p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Ngày tham gia</p>
                <p class="font-bold text-secondary"><?= date('d/m/Y H:i', strtotime($selected_user['created_at'])) ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm uppercase tracking-wider">
            <tr>
                <th class="px-6 py-4 font-bold">Thành viên</th>
                <th class="px-6 py-4 font-bold">Email / SĐT</th>
                <th class="px-6 py-4 font-bold">Vai trò</th>
                <th class="px-6 py-4 font-bold">Trạng thái</th>
                <th class="px-6 py-4 font-bold">Ngày tham gia</th>
                <th class="px-6 py-4 font-bold text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if($res->num_rows > 0): ?>
                <?php while($row = $res->fetch_assoc()):
                    $listAvatarPath = "../uploads/" . ($row['avatar'] ?? '');
                    $listHasAvatar = !empty($row['avatar']) && $row['avatar'] !== 'default_avatar.png' && file_exists($listAvatarPath);
                    $isManageable = canManageUser($row, $currentAdminId);
                    $isLocked = ($row['status'] ?? 'active') === 'locked';
                ?>
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold overflow-hidden border border-slate-200">
                                <?php if($listHasAvatar): ?>
                                    <img src="<?= $listAvatarPath ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="font-bold text-secondary"><?= ($row['fullname'] ?? '') ?: ($row['username'] ?? 'User') ?></div>
                                <div class="text-xs text-slate-400">@<?= $row['username'] ?? '' ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-600"><?= htmlspecialchars(($row['email'] ?? '') ?: 'Chưa cập nhật') ?></div>
                        <div class="text-xs text-slate-400"><?= htmlspecialchars($row['phone'] ?? '') ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($row['role'] == 'admin'): ?>
                            <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">ADMIN</span>
                        <?php else: ?>
                            <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-full">USER</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="<?= $isLocked ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' ?> text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                            <?= $isLocked ? 'Locked' : 'Active' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 flex-wrap">
                            <a href="users.php?action=view&id=<?= $row['id'] ?>" class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-700 hover:text-white transition-all" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if($isManageable): ?>
                                <a href="users.php?action=reset_password&id=<?= $row['id'] ?>" onclick="return confirm('Reset mật khẩu user này về 123456?')" class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all" title="Reset mật khẩu">
                                    <i class="fas fa-key"></i>
                                </a>
                                <?php if($isLocked): ?>
                                    <a href="users.php?action=unlock&id=<?= $row['id'] ?>" class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition-all" title="Mở khóa tài khoản">
                                        <i class="fas fa-lock-open"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="users.php?action=lock&id=<?= $row['id'] ?>" onclick="return confirm('Khóa tài khoản này?')" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all" title="Khóa tài khoản">
                                        <i class="fas fa-lock"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">
                        Không tìm thấy người dùng phù hợp.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($total_pages > 1): ?>
    <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
        <span class="text-sm text-slate-500">Hiển thị trang <?= $page ?> / <?= $total_pages ?></span>
        <div class="flex gap-2 flex-wrap">
            <?php if($page > 1): ?>
                <a href="users.php?<?= http_build_query(array_merge($queryParams, ['page' => $page - 1])) ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-primary hover:text-white transition-all">Trước</a>
            <?php endif; ?>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="users.php?<?= http_build_query(array_merge($queryParams, ['page' => $i])) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg text-sm transition-all <?= $i == $page ? 'bg-primary text-white font-bold' : 'bg-white border border-slate-200 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
                <a href="users.php?<?= http_build_query(array_merge($queryParams, ['page' => $page + 1])) ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-primary hover:text-white transition-all">Tiếp</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include("footer.php"); ?>
