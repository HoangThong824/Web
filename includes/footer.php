</main>

<footer class="bg-secondary text-stone-200 pt-20 pb-10 border-t border-primary/20">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <div>
                <h4 class="text-xl font-bold mb-6 text-white">Về chúng tôi</h4>
                <p class="text-stone-400 leading-relaxed">Khô Đặc Sản chuyên cung cấp các loại đồ khô vùng miền, đảm bảo
                    vệ sinh an toàn
                    thực phẩm và hương vị truyền thống tinh túy.</p>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6 text-white">Liên kết</h4>
                <ul class="space-y-4 text-stone-400">
                    <li><a href="<?= $base_path ?>index.php"
                            class="hover:text-primary hover:translate-x-1 inline-block transition-all">Trang chủ</a>
                    </li>
                    <li><a href="<?= $base_path ?>pages/products/products.php"
                            class="hover:text-primary hover:translate-x-1 inline-block transition-all">Sản phẩm</a></li>
                    <li><a href="<?= $base_path ?>pages/news/news.php"
                            class="hover:text-primary hover:translate-x-1 inline-block transition-all">Tin
                            tức</a></li>
                    <li><a href="<?= $base_path ?>pages/info/contact.php"
                            class="hover:text-primary hover:translate-x-1 inline-block transition-all">Liên hệ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6 text-white">Thông tin liên hệ</h4>
                <ul class="space-y-4 text-stone-400">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt mt-1 text-primary"></i>
                        <?= getSetting($conn, 'address') ?>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-primary"></i>
                        <?= getSetting($conn, 'phone') ?>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-primary"></i>
                        <?= getSetting($conn, 'email') ?>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6">Theo dõi</h4>
                <div class="flex gap-4">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-all text-xl"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-all text-xl"><i
                            class="fab fa-instagram"></i></a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-all text-xl"><i
                            class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/5 pt-8 text-center text-stone-500 text-sm">
            <p>&copy; 2026 Khô Đặc Sản. Tất cả quyền được bảo lưu. Thiết kế bởi Team.</p>
        </div>
    </div>
</footer>

<!-- Mobile Menu Drawer -->
<div id="mobile-menu-overlay" onclick="toggleMobileMenu(false)"
    class="fixed inset-0 bg-black/50 z-[60] opacity-0 pointer-events-none transition-opacity duration-300"></div>
<div id="mobile-menu"
    class="fixed top-0 right-0 h-full w-[80%] max-w-sm bg-white z-[70] translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
    <div class="p-6 flex justify-between items-center border-b border-stone-100">
        <span class="font-bold text-primary tracking-wider uppercase">Menu</span>
        <button id="mobile-menu-close" onclick="toggleMobileMenu(false)"
            class="text-stone-400 hover:text-primary transition-colors text-2xl cursor-pointer">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-6">
        <!-- Search in Mobile Menu -->
        <form action="<?= $base_path ?>pages/products/products.php" method="GET" class="mb-8 relative">
            <input type="text" name="search" placeholder="Tìm sản phẩm..."
                class="w-full bg-slate-100 border-none rounded-full py-3 px-6 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <ul class="flex flex-col gap-6 font-bold text-secondary text-sm uppercase tracking-widest">
            <li><a href="<?= $base_path ?>index.php" class="hover:text-primary transition-colors block py-2">Trang
                    chủ</a></li>
            <li><a href="<?= $base_path ?>pages/info/about.php"
                    class="hover:text-primary transition-colors block py-2">Giới thiệu</a></li>
            <li><a href="<?= $base_path ?>pages/products/products.php"
                    class="hover:text-primary transition-colors block py-2">Sản phẩm</a></li>
            <li><a href="<?= $base_path ?>pages/news/news.php"
                    class="hover:text-primary transition-colors block py-2">Tin tức</a></li>
            <li><a href="<?= $base_path ?>pages/info/faq.php"
                    class="hover:text-primary transition-colors block py-2">Hỏi/đáp</a></li>
            <li><a href="<?= $base_path ?>pages/info/contact.php"
                    class="hover:text-primary transition-colors block py-2">Liên hệ</a></li>
        </ul>

        <hr class="my-8 border-stone-100">

        <div class="flex flex-col gap-4">
            <?php if (isset($_SESSION['user'])):
                $user = $_SESSION['user'];
                $avatar_path = $base_path . "uploads/" . ($user['avatar'] ?? '');
                $has_avatar = !empty($user['avatar']) && $user['avatar'] != 'default_avatar.png' && file_exists($avatar_path);
                ?>
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 rounded-full overflow-hidden border-2 border-primary/20 flex items-center justify-center bg-primary text-white font-bold text-xl">
                        <?php if ($has_avatar): ?>
                            <img src="<?= $avatar_path ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="font-bold text-secondary"><?= $user['username'] ?></p>
                        <a href="<?= $base_path . ($user['role'] == 'admin' ? 'admin/dashboard.php' : 'pages/auth/profile.php'); ?>"
                            class="text-xs text-primary underline">Xem hồ sơ</a>
                    </div>
                </div>
                <a href="<?= $base_path ?>pages/auth/logout.php"
                    class="text-center bg-stone-100 text-stone-600 py-3 rounded-xl font-bold hover:bg-red-50 hover:text-red-500 transition-all">Đăng
                    xuất</a>
            <?php else: ?>
                <a href="<?= $base_path ?>pages/auth/login.php"
                    class="text-center border-2 border-primary text-primary py-3 rounded-xl font-bold hover:bg-primary hover:text-white transition-all">Đăng
                    nhập</a>
                <a href="<?= $base_path ?>pages/auth/register.php"
                    class="text-center bg-primary text-white py-3 rounded-xl font-bold shadow-lg shadow-primary/20">Đăng
                    ký</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleMobileMenu(show) {
        const menu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('mobile-menu-overlay');
        if (menu && overlay) {
            menu.style.transform = show ? 'translateX(0)' : 'translateX(100%)';
            overlay.style.opacity = show ? '1' : '0';
            overlay.style.pointerEvents = show ? 'auto' : 'none';
            document.body.style.overflow = show ? 'hidden' : '';
        }
    }
</script>
<script src="<?= $base_path ?>assets/main.js"></script>
</body>

</html>