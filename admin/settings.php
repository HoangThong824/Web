<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
    }
    $message = "Cập nhật cài đặt thành công!";
}

// Fetch all current settings
$settings_res = $conn->query("SELECT * FROM settings");
$settings = [];
while ($row = $settings_res->fetch_assoc()) {
    $settings[$row['key']] = $row['value'];
}

include("header.php");
?>

<div class="mb-8">
    <h2 class="text-3xl font-bold text-secondary">Cài đặt website</h2>
    <p class="text-slate-500">Quản lý thông tin chung, liên hệ và nội dung trang chủ.</p>
</div>

<?php if($message): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 max-w-4xl">
    <form method="POST" class="space-y-8">
        <!-- General Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h4 class="font-bold text-secondary mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i> Thông tin cơ bản
                </h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tên Website</label>
                        <input type="text" name="settings[site_name]" value="<?= $settings['site_name'] ?? '' ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Số điện thoại</label>
                        <input type="text" name="settings[phone]" value="<?= $settings['phone'] ?? '' ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email liên hệ</label>
                        <input type="email" name="settings[email]" value="<?= $settings['email'] ?? '' ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all">
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-secondary mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-primary"></i> Địa chỉ & Logo
                </h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Địa chỉ công ty</label>
                        <input type="text" name="settings[address]" value="<?= $settings['address'] ?? '' ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Logo Text / URL</label>
                        <input type="text" name="settings[logo]" value="<?= $settings['logo'] ?? 'KHÔ ĐẶC SẢN' ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Info -->
        <hr class="border-slate-100">
        <div>
            <h4 class="font-bold text-secondary mb-4 flex items-center gap-2">
                <i class="fas fa-file-alt text-primary"></i> Nội dung trang chủ & Giới thiệu
            </h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tiêu đề Trang chủ (Hero Headline)</label>
                    <input type="text" name="settings[homepage_content]" value="<?= $settings['homepage_content'] ?? '' ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Đoạn văn giới thiệu ngắn (Home Intro)</label>
                    <textarea name="settings[about_us]" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all"><?= $settings['about_us'] ?? '' ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nội dung chi tiết trang Giới thiệu (About Page)</label>
                    <textarea name="settings[about_page_full]" rows="8" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all"><?= $settings['about_page_full'] ?? '' ?></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold px-10 py-4 rounded-xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
            Lưu tất cả thay đổi
        </button>
    </form>
</div>

<?php include("footer.php"); ?>
