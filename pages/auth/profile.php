<?php
session_start();
include("../../includes/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php?redirect=profile.php");
    exit();
}

$user_id = (int) $_SESSION['user']['id'];
$msg = "";
$msg_type = "";

function refreshCurrentUser($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$currentUser = refreshCurrentUser($conn, $user_id);
if (!$currentUser || (($currentUser['status'] ?? 'active') === 'locked')) {
    session_destroy();
    header("Location: ../auth/login.php?message=locked");
    exit();
}
$_SESSION['user'] = $currentUser;
$user = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    if ($fullname === '') {
        $msg = "Họ và tên không được để trống!";
        $msg_type = "error";
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Email không đúng định dạng!";
        $msg_type = "error";
    } else {
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $checkEmail->bind_param("si", $email, $user_id);
        $checkEmail->execute();
        $emailExists = $checkEmail->get_result()->num_rows > 0;

        if ($emailExists) {
            $msg = "Email đã được sử dụng bởi tài khoản khác!";
            $msg_type = "error";
        } else {
            $avatar_name = $user['avatar'] ?? 'default_avatar.png';

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['avatar']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $msg = "Định dạng ảnh đại diện không hợp lệ!";
                    $msg_type = "error";
                } else {
                    $new_name = "avatar_" . $user_id . "_" . time() . "_" . uniqid() . "." . $ext;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], "../../uploads/" . $new_name)) {
                        if (!empty($avatar_name) && $avatar_name !== 'default_avatar.png' && file_exists("../../uploads/" . $avatar_name)) {
                            @unlink("../../uploads/" . $avatar_name);
                        }
                        $avatar_name = $new_name;
                    } else {
                        $msg = "Không thể tải ảnh đại diện lên!";
                        $msg_type = "error";
                    }
                }
            }

            if ($msg_type !== 'error') {
                $update_query = "UPDATE users SET fullname = ?, email = ?, phone = ?, avatar = ? WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("ssssi", $fullname, $email, $phone, $avatar_name, $user_id);

                if ($stmt->execute()) {
                    $msg = "Cập nhật thông tin thành công!";
                    $msg_type = "success";
                    $_SESSION['user'] = refreshCurrentUser($conn, $user_id);
                    $user = $_SESSION['user'];
                } else {
                    $msg = "Có lỗi xảy ra, vui lòng thử lại!";
                    $msg_type = "error";
                }
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($current_password === '' || $new_password === '' || $confirm_password === '') {
        $msg = "Vui lòng nhập đầy đủ thông tin đổi mật khẩu!";
        $msg_type = "error";
    } elseif (md5($current_password) !== $user['password']) {
        $msg = "Mật khẩu hiện tại không chính xác!";
        $msg_type = "error";
    } elseif (strlen($new_password) < 6) {
        $msg = "Mật khẩu mới phải có ít nhất 6 ký tự!";
        $msg_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $msg = "Xác nhận mật khẩu mới không khớp!";
        $msg_type = "error";
    } else {
        $hashed_password = md5($new_password);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $msg = "Đổi mật khẩu thành công!";
            $msg_type = "success";
            $_SESSION['user'] = refreshCurrentUser($conn, $user_id);
            $user = $_SESSION['user'];
        } else {
            $msg = "Không thể đổi mật khẩu, vui lòng thử lại!";
            $msg_type = "error";
        }
    }
}

$page_title = "Cá nhân";
include("../../includes/header.php");
?>

<section class="py-20 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-12">
            <div class="lg:w-1/3">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center sticky top-24">
                    <div class="w-32 h-32 rounded-full mx-auto mb-6 border-4 border-white shadow-xl flex items-center justify-center bg-primary text-white text-5xl font-bold overflow-hidden relative group">
                        <?php
                        $avatar_path = "../../uploads/" . ($user['avatar'] ?? '');
                        $has_avatar = !empty($user['avatar']) && $user['avatar'] != 'default_avatar.png' && file_exists($avatar_path);
                        if ($has_avatar): ?>
                            <img src="<?= $avatar_path ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-secondary"><?= $user['fullname'] ?: $user['username'] ?></h2>
                    <p class="text-stone-400 mb-2">@<?= $user['username'] ?></p>
                    <span class="inline-flex bg-green-50 text-green-600 px-3 py-1 rounded-full text-xs font-bold mb-8 uppercase">Đang hoạt động</span>

                    <div class="space-y-2 text-left">
                        <a href="#orders" class="flex items-center gap-4 p-4 rounded-xl hover:bg-slate-50 text-stone-600 font-bold transition-all">
                            <i class="fas fa-shopping-bag w-6"></i> Đơn hàng của tôi
                        </a>
                        <a href="#settings" class="flex items-center gap-4 p-4 rounded-xl hover:bg-slate-50 text-stone-600 font-bold transition-all">
                            <i class="fas fa-user-cog w-6"></i> Cài đặt tài khoản
                        </a>
                        <a href="#security" class="flex items-center gap-4 p-4 rounded-xl hover:bg-slate-50 text-stone-600 font-bold transition-all">
                            <i class="fas fa-lock w-6"></i> Đổi mật khẩu
                        </a>
                        <a href="../auth/logout.php" class="flex items-center gap-4 p-4 rounded-xl text-red-500 hover:bg-red-50 font-bold transition-all">
                            <i class="fas fa-sign-out-alt w-6"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:w-2/3 space-y-12">
                <?php if($msg): ?>
                    <div class="<?= $msg_type == 'success' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' ?> p-4 rounded-2xl font-bold border <?= $msg_type == 'success' ? 'border-green-100' : 'border-red-100' ?>">
                        <i class="fas <?= $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                        <?= $msg ?>
                    </div>
                <?php endif; ?>

                <section id="settings" class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="text-2xl font-bold text-secondary mb-8 flex items-center gap-3">
                        <i class="fas fa-user-edit text-primary"></i> Cài đặt tài khoản
                    </h3>

                    <form action="profile.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2 flex flex-col items-center mb-4">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-stone-100 mb-4 bg-stone-50 flex items-center justify-center text-stone-300">
                                <?php if ($has_avatar): ?>
                                    <img src="<?= $avatar_path ?>" class="w-full h-full object-cover" id="preview-avatar">
                                <?php else: ?>
                                    <i class="fas fa-user text-4xl" id="placeholder-icon"></i>
                                <?php endif; ?>
                            </div>
                            <label class="cursor-pointer bg-stone-100 hover:bg-stone-200 text-stone-600 px-4 py-2 rounded-lg text-xs font-bold transition-all">
                                <span>Thay đổi ảnh đại diện</span>
                                <input type="file" name="avatar" class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp" onchange="previewImage(this)">
                            </label>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-stone-600 ml-1">Họ và tên</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" class="w-full px-5 py-3 rounded-xl border border-stone-200 focus:border-primary outline-none transition-all" placeholder="Nhập họ tên..." required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-stone-600 ml-1">Số điện thoại</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full px-5 py-3 rounded-xl border border-stone-200 focus:border-primary outline-none transition-all" placeholder="Nhập số điện thoại...">
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-bold text-stone-600 ml-1">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="w-full px-5 py-3 rounded-xl border border-stone-200 focus:border-primary outline-none transition-all" placeholder="Nhập địa chỉ email...">
                        </div>

                        <div class="md:col-span-2 pt-4">
                            <button type="submit" name="update_profile" class="bg-secondary hover:bg-stone-800 text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg shadow-secondary/20 w-full md:w-auto">
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </section>

                <section id="security" class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="text-2xl font-bold text-secondary mb-8 flex items-center gap-3">
                        <i class="fas fa-key text-primary"></i> Đổi mật khẩu
                    </h3>

                    <form action="profile.php#security" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-stone-600 ml-1">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="w-full px-5 py-3 rounded-xl border border-stone-200 focus:border-primary outline-none transition-all" placeholder="Nhập mật khẩu hiện tại" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-stone-600 ml-1">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="w-full px-5 py-3 rounded-xl border border-stone-200 focus:border-primary outline-none transition-all" placeholder="Ít nhất 6 ký tự" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-stone-600 ml-1">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="w-full px-5 py-3 rounded-xl border border-stone-200 focus:border-primary outline-none transition-all" placeholder="Nhập lại mật khẩu mới" required>
                        </div>

                        <div class="md:col-span-2 pt-4">
                            <button type="submit" name="change_password" class="bg-primary hover:bg-primary-dark text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 w-full md:w-auto">
                                Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>
                </section>

                <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            let preview = document.getElementById('preview-avatar');
                            if (!preview) {
                                let container = document.getElementById('placeholder-icon').parentElement;
                                container.innerHTML = '<img id="preview-avatar" class="w-full h-full object-cover">';
                                preview = document.getElementById('preview-avatar');
                            }
                            preview.src = e.target.result;
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
                </script>

                <div id="orders">
                    <h3 class="text-2xl font-bold text-secondary mb-8 flex items-center gap-3">
                        <i class="fas fa-history text-primary"></i> Lịch sử mua hàng
                    </h3>

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
                                                        <?php if($item['image'] && file_exists("../../uploads/".$item['image'])): ?>
                                                            <img src="../../uploads/<?= $item['image'] ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-fish"></i></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <a href="../products/product_detail.php?id=<?= $item['product_id'] ?>" class="font-bold text-secondary hover:text-primary transition-colors"><?= $item['name'] ?></a>
                                                        <p class="text-xs text-slate-400">Số lượng: <?= $item['quantity'] ?></p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-bold text-secondary"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</p>
                                                    <?php if($order['status'] == 'delivered'): ?>
                                                        <a href="../products/product_detail.php?id=<?= $item['product_id'] ?>#reviews" class="text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded font-bold hover:bg-primary hover:text-white transition-all">Đánh giá ngay</a>
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
                                <a href="../products/products.php" class="text-primary font-bold hover:underline mt-4 inline-block">Đi mua sắm ngay</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("../../includes/footer.php"); ?>
