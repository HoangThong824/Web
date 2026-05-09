<?php
session_start();
include("../../includes/db.php");

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Count total for pagination
$count_query = "SELECT COUNT(*) as total FROM news n WHERE 1=1";
if ($search) {
    $count_query .= " AND (n.title LIKE '%" . $conn->real_escape_string($search) . "%' OR n.content LIKE '%" . $conn->real_escape_string($search) . "%')";
}
$total_res = $conn->query($count_query);
$total_items = $total_res ? $total_res->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_items / $limit);

$query = "SELECT n.*, u.fullname as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE 1=1";
if ($search) {
    $query .= " AND (n.title LIKE '%" . $conn->real_escape_string($search) . "%' OR n.content LIKE '%" . $conn->real_escape_string($search) . "%')";
}
$query .= " ORDER BY n.id DESC LIMIT $limit OFFSET $offset";

$news_res = $conn->query($query);
if (!$news_res) {
    // Graceful error handling if table doesn't exist
    $news_res = (object)['num_rows' => 0];
}

$page_title = "Tin tức";
include("../../includes/header.php");
?>

<section class="bg-secondary py-16 text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Tin tức & Khám phá</h1>
        <p class="text-slate-300">Cập nhật những bài viết mới nhất về ẩm thực và văn hóa đồ khô.</p>
    </div>
</section>

<section class="py-20">
    <div class="container mx-auto px-4">
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-16">
            <form method="GET" class="relative">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Tìm kiếm bài viết..." class="w-full pl-6 pr-16 py-4 rounded-2xl border border-slate-200 outline-none focus:border-primary shadow-sm">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 bg-primary text-white w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <?php if ($news_res->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <?php while($row = $news_res->fetch_assoc()): ?>
                    <article class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all overflow-hidden border border-slate-100 group">
                        <div class="h-64 bg-slate-100 relative overflow-hidden flex items-center justify-center text-slate-300 text-6xl">
                            <i class="fas fa-newspaper group-hover:scale-110 transition-transform duration-500"></i>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        </div>
                        <div class="p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="bg-primary/10 text-primary text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Ẩm thực</span>
                                <span class="text-xs text-slate-400"><?= date('d/m/Y', strtotime($row['created_at'])) ?></span>
                            </div>
                            <h3 class="text-2xl font-bold text-secondary mb-4 line-clamp-2 group-hover:text-primary transition-colors">
                                <a href="news_detail.php?id=<?= $row['id'] ?>"><?= $row['title'] ?></a>
                            </h3>
                            <p class="text-slate-500 line-clamp-3 mb-6">
                                <?= strip_tags($row['content']) ?>
                            </p>
                            <a href="news_detail.php?id=<?= $row['id'] ?>" class="text-secondary font-bold flex items-center gap-2 hover:gap-4 transition-all">
                                Đọc thêm <i class="fas fa-arrow-right text-primary"></i>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-16 flex justify-center items-center gap-2">
                    <?php 
                    $params = $_GET;
                    unset($params['page']);
                    $base_url = "news.php?" . http_build_query($params);
                    if (!empty($params)) $base_url .= "&";
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="<?= $base_url ?>page=<?= $page - 1 ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-600 hover:bg-primary hover:text-white hover:border-primary transition-all">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= $base_url ?>page=<?= $i ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all <?= $i == $page ? 'bg-primary text-white font-bold shadow-lg shadow-primary/30' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?= $base_url ?>page=<?= $page + 1 ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-600 hover:bg-primary hover:text-white hover:border-primary transition-all">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-slate-400">Không tìm thấy bài viết nào phù hợp.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include("../../includes/footer.php"); ?>
