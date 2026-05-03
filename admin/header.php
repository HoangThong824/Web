<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Srtdash Admin | Khô Đặc Sản</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8914fe', /* Srtdash default purple */
                        secondary: '#232a3e', /* Srtdash sidebar dark */
                        'bg-light': '#f3f8fb',
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f3f8fb; }
        .sidebar-link.active { background-color: rgba(255,255,255,0.05); border-left: 3px solid #8914fe; color: #fff; }
    </style>
</head>
<body class="min-h-screen flex">

<!-- Sidebar (Srtdash Style) -->
<aside class="w-72 bg-secondary text-slate-400 hidden lg:flex flex-col sticky top-0 h-screen shadow-2xl">
    <div class="p-8 text-2xl font-bold text-white flex items-center gap-3 border-b border-slate-700/50 mb-4">
        <span class="bg-primary w-10 h-10 rounded-xl flex items-center justify-center text-xl">S</span>
        srtdash
    </div>
    
    <div class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-500">Main Menu</div>
    
    <nav class="flex-1 px-4 space-y-1">
        <a href="dashboard.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-xl hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-th-large text-lg"></i> 
            <span class="font-medium">Dashboard</span>
        </a>
        
        <a href="products.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-xl hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
            <i class="fas fa-box-open text-lg"></i> 
            <span class="font-medium">Sản phẩm</span>
        </a>
        
        <a href="categories.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-xl hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
            <i class="fas fa-list-ul text-lg"></i> 
            <span class="font-medium">Danh mục</span>
        </a>

        <a href="news.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-xl hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper text-lg"></i> 
            <span class="font-medium">Tin tức</span>
        </a>

        <a href="comments.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-xl hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'comments.php' ? 'active' : ''; ?>">
            <i class="fas fa-comment-dots text-lg"></i> 
            <span class="font-medium">Bình luận</span>
        </a>

        <a href="contacts.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-xl hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope text-lg"></i> 
            <span class="font-medium">Liên hệ</span>
        </a>

        <a href="users.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-xl hover:text-white transition-all <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-friends text-lg"></i> 
            <span class="font-medium">Thành viên</span>
        </a>
    </nav>

    <div class="p-6 mt-auto border-t border-slate-700/50">
        <a href="../logout.php" class="flex items-center gap-4 px-6 py-4 rounded-xl text-red-400 hover:bg-red-500/10 transition-all">
            <i class="fas fa-power-off"></i> 
            <span class="font-bold uppercase text-xs tracking-widest">Logout</span>
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="flex-1 flex flex-col min-w-0">
    <!-- Topbar (Srtdash Style) -->
    <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center sticky top-0 z-40">
        <div class="flex items-center gap-8">
            <button class="text-slate-400 lg:hidden"><i class="fas fa-bars text-xl"></i></button>
            <div class="relative hidden sm:block">
                <input type="text" placeholder="Search..." class="bg-slate-50 pl-12 pr-4 py-2 rounded-full border-none focus:ring-2 focus:ring-primary/20 text-sm w-64 outline-none">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
            </div>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 border-r border-slate-100 pr-6 mr-6">
                <div class="text-right hidden md:block">
                    <p class="text-xs font-bold text-secondary">Administrator</p>
                    <p class="text-[10px] text-slate-400">Online</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold shadow-lg shadow-primary/30">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <a href="../index.php" target="_blank" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-primary transition-all">
                <i class="fas fa-globe"></i>
            </a>
        </div>
    </header>

    <main class="p-4 md:p-8">