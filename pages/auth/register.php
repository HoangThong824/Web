<?php
session_start();
include("../../includes/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($pass !== $confirm_pass) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại!";
        } else {
            $hashed_pass = md5($pass);
            $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, email) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $user, $hashed_pass, $fullname, $email);
            
            if ($stmt->execute()) {
                $success = "Đăng ký thành công! Đang chuyển hướng...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
}

$page_title = "Đăng ký";
include("../../includes/header.php");
?>

<div class="py-20 flex items-center justify-center min-h-[80vh]">
    <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-lg border border-slate-100">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-secondary mb-2">Đăng ký thành viên</h2>
            <p class="text-slate-500">Tham gia để nhận nhiều ưu đãi đặc biệt</p>
        </div>
        
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <span class="font-medium"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                <span class="font-medium"><?php echo $success; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6" data-validate>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Họ và tên</label>
                <input type="text" name="fullname" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" placeholder="Nhập họ và tên" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tên đăng nhập</label>
                <input type="text" name="username" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" placeholder="Username" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" placeholder="example@gmail.com" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Mật khẩu</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" placeholder="••••••••" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" placeholder="••••••••" required>
            </div>
            <div class="md:col-span-2 mt-4">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                    Đăng ký ngay
                </button>
            </div>
        </form>
        
        <div class="mt-8 text-center text-slate-600">
            <span>Đã có tài khoản?</span>
            <a href="login.php" class="text-primary font-bold hover:underline ml-1">Đăng nhập</a>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>
