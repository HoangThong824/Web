<?php
session_start();
include("../../includes/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $_SESSION['user'] = $userData;
        
        if ($userData['role'] == 'admin') {
            header("Location: ../../admin/dashboard.php");
        } else {
            header("Location: ../../index.php");
        }
        exit();
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}

$page_title = "Đăng nhập";
include("../../includes/header.php");
?>

<div class="py-20 flex items-center justify-center min-h-[70vh]">
    <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-secondary mb-2">Chào mừng trở lại</h2>
            <p class="text-slate-500">Đăng nhập để quản lý tài khoản</p>
        </div>
        
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <span class="font-medium"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tên đăng nhập</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" placeholder="Nhập tên đăng nhập" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Mật khẩu</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" placeholder="Nhập mật khẩu" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                Đăng nhập
            </button>
        </form>
        
        <div class="mt-8 text-center text-slate-600">
            <span>Chưa có tài khoản?</span>
            <a href="register.php" class="text-primary font-bold hover:underline ml-1">Đăng ký tại đây</a>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>