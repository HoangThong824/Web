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
                        primary: '#D35400',
                        'primary-dark': '#A04000',
                        secondary: '#2C3E50',
                        accent: '#E67E22',
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
        <nav class="flex justify-between items-center">
            <div class="flex items-center gap-12">
                <a href="index.php" class="text-2xl font-bold text-primary flex items-center gap-2">
                    <i class="fas fa-fish"></i> KHÔ ĐẶC SẢN
                </a>
                <ul class="hidden lg:flex gap-6 font-bold text-secondary text-[11px] uppercase tracking-wider">
                    <li><a href="index.php" class="hover:text-primary transition-colors">Trang chủ</a></li>
                    <li><a href="about.php" class="hover:text-primary transition-colors">Giới thiệu</a></li>
                    <li><a href="products.php" class="hover:text-primary transition-colors">Sản phẩm</a></li>
                    <li><a href="news.php" class="hover:text-primary transition-colors">Tin tức</a></li>
                    <li><a href="faq.php" class="hover:text-primary transition-colors">Hỏi/đáp</a></li>
                    <li><a href="contact.php" class="hover:text-primary transition-colors">Liên hệ</a></li>
                </ul>
            </div>
            <div class="hidden lg:flex flex-1 max-w-md mx-8">
                <form action="products.php" method="GET" class="w-full relative">
                    <input type="text" name="search" placeholder="Tìm sản phẩm..." class="w-full bg-slate-100 border-none rounded-full py-2 px-6 focus:ring-2 focus:ring-primary/20 outline-none transition-all">
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
                    if ($cart_count > 0): 
                    ?>
                        <span class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>

                <?php if(isset($_SESSION['user'])): ?>
                    <a href="<?php echo $_SESSION['user']['role'] == 'admin' ? 'admin/dashboard.php' : 'profile.php'; ?>" class="hidden sm:inline-block px-5 py-2 rounded-full bg-secondary text-white font-semibold hover:bg-slate-800 transition-all text-sm">
                        <?php echo $_SESSION['user']['username']; ?>
                    </a>
                    <a href="logout.php" class="bg-primary/10 text-primary px-4 py-2 rounded-full font-bold hover:bg-primary hover:text-white transition-all text-sm">Thoát</a>
                <?php else: ?>
                    <a href="login.php" class="hidden sm:inline-block px-4 py-2 rounded-full border-2 border-primary text-primary font-semibold hover:bg-primary hover:text-white transition-all text-sm">Đăng nhập</a>
                    <a href="register.php" class="bg-primary text-white px-5 py-2 rounded-full font-semibold hover:bg-primary-dark transition-all text-sm shadow-lg shadow-primary/20">Đăng ký</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main>