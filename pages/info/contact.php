<?php
session_start();
include("../../includes/db.php");

$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    if ($stmt->execute()) {
        $success = "Cảm ơn bạn! Chúng tôi sẽ liên hệ lại sớm nhất có thể.";
    }
}

$page_title = "Liên hệ";
include("../../includes/header.php");
?>

<section class="bg-secondary py-16 text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Trang liên hệ</h1>
        <p class="text-slate-300">Chúng tôi luôn sẵn sàng lắng nghe ý kiến từ bạn.</p>
    </div>
</section>

<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto flex flex-col lg:flex-row gap-16">
            <div class="lg:w-1/3">
                <h2 class="text-3xl font-bold text-secondary mb-8">Thông tin liên hệ</h2>
                <div class="space-y-8">
                    <div class="flex items-start gap-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-xl flex-shrink-0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-secondary mb-1">Địa chỉ</h4>
                            <p class="text-slate-500"><?= $settings['address'] ?? 'Chưa cập nhật' ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-xl flex-shrink-0">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-secondary mb-1">Điện thoại</h4>
                            <p class="text-slate-500"><?= $settings['phone'] ?? 'Chưa cập nhật' ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-xl flex-shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-secondary mb-1">Email</h4>
                            <p class="text-slate-500"><?= $settings['email'] ?? 'Chưa cập nhật' ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-2/3 bg-white p-10 rounded-3xl shadow-xl border border-slate-100">
                <?php if($success): ?>
                    <div class="bg-green-50 text-green-600 p-6 rounded-2xl mb-8 flex items-center gap-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <span class="font-bold"><?= $success ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Họ và tên</label>
                            <input type="text" name="name" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" placeholder="Nhập tên của bạn" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" placeholder="example@gmail.com" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Lời nhắn</label>
                        <textarea name="message" rows="6" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" placeholder="Bạn cần hỗ trợ gì?" required></textarea>
                    </div>
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-10 py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                        Gửi lời nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include("../../includes/footer.php"); ?>