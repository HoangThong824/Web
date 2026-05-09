<?php
session_start();
include("../../includes/db.php");

$category_id = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Define sorting logic
$order_by = "p.id DESC"; // Default
if ($sort == 'price_asc') {
    $order_by = "p.price ASC";
} elseif ($sort == 'price_desc') {
    $order_by = "p.price DESC";
} elseif ($sort == 'name_asc') {
    $order_by = "p.name ASC";
}

// Count total items for pagination
$count_query = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
if ($category_id) {
    $count_query .= " AND p.category_id = " . intval($category_id);
}
if ($search) {
    $count_query .= " AND (p.name LIKE '%" . $conn->real_escape_string($search) . "%' OR p.description LIKE '%" . $conn->real_escape_string($search) . "%')";
}
$total_res = $conn->query($count_query);
$total_items = $total_res ? $total_res->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_items / $limit);

$query = "SELECT p.*, c.name as category_name, 
          AVG(co.rating) as avg_rating, 
          COUNT(co.id) as review_count 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          LEFT JOIN comments co ON p.id = co.product_id AND co.status = 'approved'
          WHERE 1=1";
if ($category_id) {
    $query .= " AND p.category_id = " . intval($category_id);
}
if ($search) {
    $query .= " AND (p.name LIKE '%" . $conn->real_escape_string($search) . "%' OR p.description LIKE '%" . $conn->real_escape_string($search) . "%')";
}
$query .= " GROUP BY p.id ORDER BY $order_by LIMIT $limit OFFSET $offset";

$products = $conn->query($query);
if (!$products) {
    die("Lỗi truy vấn: " . $conn->error);
}

$page_title = "Sản phẩm";
include("../../includes/header.php");
?>

<!-- Shop Hero Banner -->
<div class="relative w-full overflow-hidden mt-6">
    <img src="../../uploads/banner.png" alt="Banner" class="w-full h-auto block">
    <!-- Fade-out Gradient Overlay -->
    <div class="absolute bottom-0 left-0 w-full h-1/3 bg-gradient-to-t from-slate-50 to-transparent"></div>
</div>

<!-- Shop Header -->
<section class="py-12 text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold text-secondary mb-2">Danh mục sản phẩm</h1>
        <p class="text-slate-500">Khám phá các loại khô đặc sản tinh hoa từ mọi vùng miền.</p>
    </div>
</section>

<!-- Shop Content -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar Filters -->
            <aside class="lg:w-1/4">
                <!-- Search -->
                <div class="bg-white p-6 rounded-2xl shadow-sm mb-8">
                    <h3 class="font-bold text-secondary mb-4">Tìm kiếm</h3>
                    <form method="GET" class="relative">
                        <input type="text" name="search" value="<?= $search ?>" placeholder="Tìm sản phẩm..." class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Categories -->
                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="font-bold text-secondary mb-4">Danh mục</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="products.php" class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition-all <?= !$category_id ? 'text-primary font-bold bg-orange-50' : 'text-slate-600' ?>">
                                <span>Tất cả</span>
                                <span class="text-xs bg-slate-100 px-2 py-1 rounded-full">
                                    <?php 
                                    $count_res = $conn->query("SELECT COUNT(*) FROM products");
                                    echo $count_res ? $count_res->fetch_row()[0] : 0; 
                                    ?>
                                </span>
                            </a>
                        </li>
                        <?php
                        $cats = $conn->query("SELECT * FROM categories");
                        while($c = $cats->fetch_assoc()):
                        ?>
                            <li>
                                <a href="products.php?category=<?= $c['id'] ?>" class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition-all <?= $category_id == $c['id'] ? 'text-primary font-bold bg-orange-50' : 'text-slate-600' ?>">
                                    <span><?= $c['name'] ?></span>
                                    <span class="text-xs bg-slate-100 px-2 py-1 rounded-full"><?= $conn->query("SELECT COUNT(*) FROM products WHERE category_id = ".$c['id'])->fetch_row()[0] ?></span>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="lg:w-3/4">
                <div class="flex justify-between items-center mb-8">
                    <p class="text-slate-500">Hiển thị <span class="font-bold text-secondary"><?= $products->num_rows ?></span> trên <span class="font-bold text-secondary"><?= $total_items ?></span> sản phẩm</p>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-500">Sắp xếp:</span>
                        <select id="sort-select" class="bg-white border border-slate-200 px-4 py-2 rounded-lg text-sm outline-none cursor-pointer hover:border-primary transition-all">
                            <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                        </select>

                        <script>
                        document.getElementById('sort-select').addEventListener('change', function() {
                            const url = new URL(window.location.href);
                            url.searchParams.set('sort', this.value);
                            window.location.href = url.href;
                        });
                        </script>
                    </div>
                </div>

                <?php if ($products->num_rows > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php while($row = $products->fetch_assoc()): ?>
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all group overflow-hidden border border-slate-100">
                                <a href="product_detail.php?id=<?= $row['id'] ?>" class="h-56 bg-slate-50 relative overflow-hidden flex items-center justify-center text-slate-400 text-4xl block">
                                    <?php if($row['image'] && file_exists("../../uploads/".$row['image'])): ?>
                                        <img src="../../uploads/<?= $row['image'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <?php else: ?>
                                        <i class="fas fa-fish group-hover:scale-125 transition-transform duration-500"></i>
                                    <?php endif; ?>
                                    <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </a>
                                <div class="p-6">
                                    <span class="text-primary text-[10px] font-bold uppercase tracking-widest mb-1 block"><?= $row['category_name'] ?></span>
                                    <a href="product_detail.php?id=<?= $row['id'] ?>">
                                        <h3 class="text-lg font-bold text-secondary mb-1 group-hover:text-primary transition-colors"><?= $row['name'] ?></h3>
                                    </a>
                                    <?php 
                                        $rating = round($row['avg_rating'] ?: 0);
                                        $review_count = $row['review_count'];
                                    ?>
                                    <div class="flex items-center gap-1 text-orange-400 text-[10px] mb-3">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="<?= $i <= $rating ? 'fas' : 'far' ?> fa-star"></i>
                                        <?php endfor; ?>
                                        <span class="text-slate-400 ml-1">(<?= $review_count ?>)</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xl font-bold text-primary"><?= number_format($row['price'], 0, ',', '.') ?>đ <span class="text-[10px] text-slate-400 font-normal">/ 500g</span></span>
                                        <button onclick="addToCart(<?= $row['id'] ?>)" class="bg-slate-50 hover:bg-primary hover:text-white text-secondary w-10 h-10 rounded-xl transition-all border border-slate-100 flex items-center justify-center">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="mt-12 flex justify-center items-center gap-2">
                            <?php 
                            // Build base URL for pagination
                            $params = $_GET;
                            unset($params['page']);
                            $base_url = "products.php?" . http_build_query($params);
                            if (!empty($params)) $base_url .= "&";
                            ?>

                            <?php if ($page > 1): ?>
                                <a href="<?= $base_url ?>page=<?= $page - 1 ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-primary hover:text-white hover:border-primary transition-all">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="<?= $base_url ?>page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all <?= $i == $page ? 'bg-primary text-white font-bold shadow-lg shadow-primary/30' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="<?= $base_url ?>page=<?= $page + 1 ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-primary hover:text-white hover:border-primary transition-all">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="bg-white p-20 rounded-3xl text-center border-2 border-dashed border-slate-200">
                        <div class="text-slate-300 text-6xl mb-6"><i class="fas fa-search"></i></div>
                        <h3 class="text-2xl font-bold text-secondary mb-2">Không tìm thấy sản phẩm</h3>
                        <p class="text-slate-500">Thử tìm kiếm với từ khóa khác hoặc chọn danh mục khác.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include("../../includes/footer.php"); ?>
