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
                <div class="flex items-center gap-12">
                    <a href="index.php" class="flex items-center">
                        <img src="image/logo.png" alt="KHÔ ĐẶC SẢN" class="h-16 md:h-20 w-auto object-contain">
                    </a>
                    <ul class="hidden lg:flex gap-8 font-bold text-secondary text-[12px] uppercase tracking-widest">
                        <li><a href="index.php" class="relative group py-2">Trang chủ<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="about.php" class="relative group py-2">Giới thiệu<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="products.php" class="relative group py-2">Sản phẩm<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="news.php" class="relative group py-2">Tin tức<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="faq.php" class="relative group py-2">Hỏi/đáp<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                        <li><a href="contact.php" class="relative group py-2">Liên hệ<span
                                     class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all group-hover:w-full"></span></a>
                        </li>
                    </ul>
                </div>
                <div class="hidden lg:flex flex-1 max-w-md mx-8">
                    <form action="products.php" method="GET" class="w-full relative">
                        <input type="text" name="search" placeholder="Tìm sản phẩm..."
                            class="w-full bg-slate-100 border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="flex gap-4 items-center">
                    <a href="cart.php" class="relative text-secondary hover:text-primary transition-colors p-2">
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
                        $avatar_path = "uploads/" . ($user['avatar'] ?? '');
                        $has_avatar = !empty($user['avatar']) && $user['avatar'] != 'default_avatar.png' && file_exists($avatar_path);
                    ?>
                        <div class="flex items-center gap-3">
                            <a href="<?php echo $user['role'] == 'admin' ? 'admin/dashboard.php' : 'profile.php'; ?>" 
                               class="flex items-center bg-secondary/5 hover:bg-secondary/10 p-1 rounded-full transition-all group">
                                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm flex items-center justify-center bg-primary text-white font-bold text-lg transition-transform group-hover:scale-105">
                                    <?php if ($has_avatar): ?>
                                        <img src="<?= $avatar_path ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <a href="logout.php" class="bg-primary/10 text-primary px-4 py-2 rounded-full font-bold hover:bg-primary hover:text-white transition-all text-sm">Thoát</a>
                        </div>
                    <?php else: ?>
                        <a href="login.php"
                            class="hidden sm:inline-block px-4 py-2 rounded-full border-2 border-primary text-primary font-semibold hover:bg-primary hover:text-white transition-all text-sm">Đăng
                            nhập</a>
                        <a href="register.php"
                            class="bg-primary text-white px-5 py-2 rounded-full font-semibold hover:bg-primary-dark transition-all text-sm shadow-lg shadow-primary/20">Đăng
                            ký</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Toast Notification -->
    <div id="toast-container" class="fixed bottom-10 right-10 z-[100] flex flex-col gap-4"></div>
    <script>
    function showToast(msg, type='success') {
        const t = document.createElement('div');
        t.className = `px-6 py-4 rounded-2xl shadow-2xl text-white font-bold transition-all duration-500 transform translate-y-20 opacity-0 flex items-center gap-3 ${type==='success'?'bg-secondary':'bg-red-500'}`;
        t.innerHTML = `<i class="fas ${type==='success'?'fa-check-circle text-primary':'fa-exclamation-circle'}"></i><span>${msg}</span>`;
        document.getElementById('toast-container').appendChild(t);
        setTimeout(()=>t.classList.remove('translate-y-20','opacity-0'),100);
        setTimeout(()=>{t.classList.add('translate-y-20','opacity-0');setTimeout(()=>t.remove(),500)},3000);
    }
    function updateCartBadge(count) {
        const b = document.getElementById('cart-badge');
        if(b) { b.innerText = count; if(count>0) b.classList.remove('hidden'); else b.classList.add('hidden'); }
    }
    function addToCart(id, qty = 1) {
        // Handle path if in admin subdirectory
        const path = window.location.pathname.includes('/admin/') ? '../api/cart_handler.php' : 'api/cart_handler.php';
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