<?php
session_start();
include("includes/db.php");

if (!isset($_GET['id'])) {
    header("Location: news.php");
    exit();
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT n.*, u.fullname as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.id = $id");
$news = $res->fetch_assoc();

if (!$news) {
    header("Location: news.php");
    exit();
}

$page_title = $news['title'];
include("includes/header.php");
?>

<article class="py-12 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-slate-500 mb-8 max-w-4xl mx-auto">
            <a href="index.php" class="hover:text-primary">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="news.php" class="hover:text-primary">Tin tức</a>
            <span class="mx-2">/</span>
            <span class="text-slate-800 font-bold line-clamp-1"><?= $news['title'] ?></span>
        </nav>

        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
            <!-- Featured Placeholder -->
            <div class="h-[400px] bg-slate-100 flex items-center justify-center text-slate-200 text-9xl">
                <i class="fas fa-newspaper"></i>
            </div>
            
            <div class="p-8 md:p-16">
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex items-center gap-2 text-slate-500 text-sm">
                        <i class="fas fa-calendar-alt text-primary"></i>
                        <?= date('d/m/Y', strtotime($news['created_at'])) ?>
                    </div>
                    <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                    <div class="flex items-center gap-2 text-slate-500 text-sm">
                        <i class="fas fa-user text-primary"></i>
                        <?= $news['author_name'] ?: 'Ban biên tập' ?>
                    </div>
                </div>

                <h1 class="text-4xl md:text-5xl font-bold text-secondary mb-10 leading-tight">
                    <?= $news['title'] ?>
                </h1>

                <div class="prose prose-lg prose-slate max-w-none text-slate-600 leading-relaxed space-y-8">
                    <?= nl2br($news['content']) ?>
                </div>

                <!-- Footer / Share -->
                <div class="mt-16 pt-8 border-t border-slate-100 flex justify-between items-center">
                    <div class="flex gap-4">
                        <span class="text-sm font-bold text-secondary">Chia sẻ:</span>
                        <div class="flex gap-3 text-slate-400">
                            <a href="#" class="hover:text-primary transition-colors"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="hover:text-primary transition-colors"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="hover:text-primary transition-colors"><i class="fas fa-link"></i></a>
                        </div>
                    </div>
                    <a href="news.php" class="text-primary font-bold hover:underline">
                        <i class="fas fa-arrow-left mr-2"></i> Trở lại tin tức
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>

<?php include("includes/footer.php"); ?>
