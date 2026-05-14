<?php
// Enhanced dynamic path logic for nested folders
$current_path = $_SERVER['PHP_SELF'];
if (strpos($current_path, '/pages/') !== false) {
    $parts = explode('/pages/', $current_path);
    $depth = substr_count($parts[1], '/') + 1;
    $base_path = str_repeat('../', $depth);
} elseif (strpos($current_path, '/admin/') !== false) {
    $base_path = '../';
} else {
    $base_path = '';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " | " : ""; ?>Khô Đặc Sản</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#A04000', // Burnt Sienna / Rust
                        'primary-dark': '#78350F',
                        secondary: '#1C1917', // Stone 900 (Warm Charcoal)
                        accent: '#D35400',
                        bg: '#FAF7F5', // Warm White
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#FAF7F5] text-stone-800">

    <!-- Top Accent Bar -->
    <div class="h-1 bg-primary"></div>

    <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-stone-100">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex justify-between items-center">
                <div class="flex items-center gap-4 lg:gap-12">
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" onclick="toggleMobileMenu()" class="lg:hidden text-secondary hover:text-primary transition-colors p-2 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>

                    <a href="<?= $base_path ?>index.php" class="flex items-center">
                        <img src="<?= $base_path ?>uploads/logo.png" alt="KHÔ ĐẶC SẢN" class="h-12 md:h-20 w-auto object-contain">
                    </a>
                    <ul class="hidden lg:flex gap-8 font-bold text-secondary text-[12px] uppercase tracking-widest">
                        <li><a href="<?= $base_path ?>index.php" class="relative group py-2">Trang chủ<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="<?= $base_path ?>pages/info/about.php" class="relative group py-2">Giới thiệu<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="<?= $base_path ?>pages/products/products.php" class="relative group py-2">Sản phẩm<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="<?= $base_path ?>pages/news/news.php" class="relative group py-2">Tin tức<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="<?= $base_path ?>pages/info/faq.php" class="relative group py-2">Hỏi/đáp<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="<?= $base_path ?>pages/info/contact.php" class="relative group py-2">Liên hệ<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                    </ul>
                </div>
                <div class="hidden lg:flex flex-1 max-w-md mx-8">
                    <form action="<?= $base_path ?>pages/products/products.php" method="GET" class="w-full relative">
                        <input type="text" name="search" placeholder="Tìm sản phẩm..."
                            class="w-full bg-slate-100 border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="flex gap-4 items-center">
                    <a href="<?= $base_path ?>pages/cart/cart.php" class="relative text-secondary hover:text-primary transition-colors p-2">
                        <i class="fas fa-shopping-basket text-2xl"></i>
                        <?php
                        $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                        ?>
                        <span id="cart-badge"
                            class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white <?= $cart_count > 0 ? '' : 'hidden' ?>">
                            <?= $cart_count ?>
                        </span>
                    </a>

                    <?php if (isset($_SESSION['user'])):
                        $user = $_SESSION['user'];
                        $avatar_path = $base_path . "uploads/" . ($user['avatar'] ?? '');
                        $has_avatar = !empty($user['avatar']) && $user['avatar'] != 'default_avatar.png' && file_exists($avatar_path);
                        ?>
                        <div class="flex items-center gap-3">
                            <a href="<?= $base_path . ($user['role'] == 'admin' ? 'admin/dashboard.php' : 'pages/auth/profile.php'); ?>"
                                class="flex items-center bg-secondary/5 hover:bg-secondary/10 p-1 rounded-full transition-all group">
                                <div
                                    class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm flex items-center justify-center bg-primary text-white font-bold text-lg transition-transform group-hover:scale-105">
                                    <?php if ($has_avatar): ?>
                                        <img src="<?= $avatar_path ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <a href="<?= $base_path ?>pages/auth/logout.php"
                                class="bg-primary/10 text-primary px-4 py-2 rounded-full font-bold hover:bg-primary hover:text-white transition-all text-sm">Thoát</a>
                        </div>
                    <?php else: ?>
                        <a href="<?= $base_path ?>pages/auth/login.php"
                            class="hidden sm:inline-block px-4 py-2 rounded-full border-2 border-primary text-primary font-semibold hover:bg-primary hover:text-white transition-all text-sm">Đăng
                            nhập</a>
                        <a href="<?= $base_path ?>pages/auth/register.php"
                            class="bg-primary text-white px-5 py-2 rounded-full font-semibold hover:bg-primary-dark transition-all text-sm shadow-lg shadow-primary/20">Đăng
                            ký</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black/60 z-[60] hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm"></div>

    <!-- Mobile Menu Drawer -->
    <div id="mobile-menu" class="fixed top-0 left-0 h-full w-[85%] max-w-[400px] bg-white z-[70] -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
        <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50">
            <div class="flex items-center gap-3">
                <img src="<?= $base_path ?>uploads/logo.png" class="h-10 w-auto">
                <span class="font-bold text-lg text-primary uppercase tracking-wider">Menu</span>
            </div>
            <button id="close-menu-btn" onclick="toggleMobileMenu()" class="text-secondary hover:text-primary transition-colors p-2">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Search in Mobile -->
            <div class="mb-8">
                <form action="<?= $base_path ?>pages/products/products.php" method="GET" class="w-full relative">
                    <input type="text" name="search" placeholder="Tìm sản phẩm..."
                        class="w-full bg-stone-100 border-none rounded-2xl py-3 px-6 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <nav>
                <ul class="flex flex-col gap-2 font-bold text-secondary text-base uppercase tracking-wide">
                    <li>
                        <a href="<?= $base_path ?>index.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-primary/5 hover:text-primary transition-all">
                            <i class="fas fa-home w-6"></i> Trang chủ
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_path ?>pages/info/about.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-primary/5 hover:text-primary transition-all">
                            <i class="fas fa-info-circle w-6"></i> Giới thiệu
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_path ?>pages/products/products.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-primary/5 hover:text-primary transition-all">
                            <i class="fas fa-box-open w-6"></i> Sản phẩm
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_path ?>pages/news/news.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-primary/5 hover:text-primary transition-all">
                            <i class="fas fa-newspaper w-6"></i> Tin tức
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_path ?>pages/info/faq.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-primary/5 hover:text-primary transition-all">
                            <i class="fas fa-question-circle w-6"></i> Hỏi/đáp
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_path ?>pages/info/contact.php" class="flex items-center gap-4 p-4 rounded-xl hover:bg-primary/5 hover:text-primary transition-all">
                            <i class="fas fa-envelope w-6"></i> Liên hệ
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="mt-8 pt-8 border-t border-stone-100">
                <?php if (!isset($_SESSION['user'])): ?>
                    <div class="flex flex-col gap-4">
                        <a href="<?= $base_path ?>pages/auth/login.php" class="w-full text-center py-4 rounded-2xl border-2 border-primary text-primary font-bold hover:bg-primary hover:text-white transition-all">
                            <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
                        </a>
                        <a href="<?= $base_path ?>pages/auth/register.php" class="w-full text-center py-4 rounded-2xl bg-primary text-white font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all">
                            <i class="fas fa-user-plus mr-2"></i> Đăng ký
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-4 p-4 bg-stone-50 rounded-2xl mb-4">
                        <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xl overflow-hidden">
                            <?php if ($has_avatar): ?>
                                <img src="<?= $avatar_path ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="font-bold text-secondary"><?= $user['username'] ?></p>
                            <a href="<?= $base_path ?>pages/auth/profile.php" class="text-sm text-primary">Xem hồ sơ</a>
                        </div>
                    </div>
                    <a href="<?= $base_path ?>pages/auth/logout.php" class="flex items-center gap-4 p-4 rounded-xl text-red-500 hover:bg-red-50 transition-all font-bold">
                        <i class="fas fa-sign-out-alt w-6"></i> Đăng xuất
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="p-6 bg-stone-50 text-center text-xs text-stone-400">
            &copy; 2026 Khô Đặc Sản. All rights reserved.
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-container" class="fixed bottom-10 right-10 z-[100] flex flex-col gap-4"></div>
    <script>
        function showToast(msg, type = 'success') {
            const t = document.createElement('div');
            t.className = `px-6 py-4 rounded-2xl shadow-2xl text-white font-bold transition-all duration-500 transform translate-y-20 opacity-0 flex items-center gap-3 ${type === 'success' ? 'bg-secondary' : 'bg-red-500'}`;
            t.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle text-primary' : 'fa-exclamation-circle'}"></i><span>${msg}</span>`;
            document.getElementById('toast-container').appendChild(t);
            setTimeout(() => t.classList.remove('translate-y-20', 'opacity-0'), 100);
            setTimeout(() => { t.classList.add('translate-y-20', 'opacity-0'); setTimeout(() => t.remove(), 500) }, 3000);
        }

        // Fail-safe Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-menu-overlay');
            if (!menu || !overlay) return;

            const isOpen = !menu.classList.contains('-translate-x-full');
            
            if (isOpen) {
                menu.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
                document.body.style.overflow = '';
            } else {
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    menu.classList.remove('-translate-x-full');
                }, 10);
            }
        }
        function updateCartBadge(count) {
            const b = document.getElementById('cart-badge');
            if (b) { b.innerText = count; if (count > 0) b.classList.remove('hidden'); else b.classList.add('hidden'); }
        }
        function addToCart(id, qty = 1) {
            // Handle path if in admin subdirectory or pages subdirectory
            let path = '<?= $base_path ?>api/cart_handler.php';
            // If we are in admin, it's already handled by $base_path which would be '../'
            fetch(`${path}?action=add&id=${id}&qty=${qty}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        updateCartBadge(data.cart_count);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(err => {
                    showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
                    console.error(err);
                });
        }
    </script>

    <main>