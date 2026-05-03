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
            <a href="index.php" class="text-2xl font-bold text-primary flex items-center gap-2">
                <i class="fas fa-fish"></i> KHÔ ĐẶC SẢN
            </a>
            <ul class="hidden md:flex gap-8 font-semibold text-secondary">
                <li><a href="index.php" class="hover:text-primary transition-colors">Trang chủ</a></li>
                <li><a href="about.php" class="hover:text-primary transition-colors">Giới thiệu</a></li>
                <li><a href="products.php" class="hover:text-primary transition-colors">Sản phẩm</a></li>
                <li><a href="news.php" class="hover:text-primary transition-colors">Tin tức</a></li>
                <li><a href="faq.php" class="hover:text-primary transition-colors">Hỏi/đáp</a></li>
                <li><a href="contact.php" class="hover:text-primary transition-colors">Liên hệ</a></li>
            </ul>
            <div class="flex gap-4 items-center">
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="<?php echo $_SESSION['user']['role'] == 'admin' ? 'admin/dashboard.php' : 'profile.php'; ?>" class="hidden sm:inline-block px-4 py-2 rounded-full border-2 border-primary text-primary font-semibold hover:bg-primary hover:text-white transition-all">
                        Hi, <?php echo $_SESSION['user']['username']; ?>
                    </a>
                    <a href="logout.php" class="bg-primary text-white px-5 py-2 rounded-full font-semibold hover:bg-primary-dark transition-all">Đăng xuất</a>
                <?php else: ?>
                    <a href="login.php" class="hidden sm:inline-block px-4 py-2 rounded-full border-2 border-primary text-primary font-semibold hover:bg-primary hover:text-white transition-all">Đăng nhập</a>
                    <a href="register.php" class="bg-primary text-white px-5 py-2 rounded-full font-semibold hover:bg-primary-dark transition-all">Đăng ký</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main>