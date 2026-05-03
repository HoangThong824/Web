<?php
session_start();
include("includes/db.php");

$category_id = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

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
$query .= " GROUP BY p.id ORDER BY p.id DESC";

$products = $conn->query($query);
if (!$products) {
    die("Lỗi truy vấn: " . $conn->error);
}

$page_title = "Sản phẩm";
include("includes/header.php");
?>

<!-- Shop Header -->
<section class="bg-secondary py-16 text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Danh mục sản phẩm</h1>
        <p class="text-slate-300">Khám phá các loại khô đặc sản tinh hoa từ mọi vùng miền.</p>
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
                    <p class="text-slate-500">Hiển thị <span class="font-bold text-secondary"><?= $products->num_rows ?></span> sản phẩm</p>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-500">Sắp xếp:</span>
                        <select class="bg-white border border-slate-200 px-4 py-2 rounded-lg text-sm outline-none">
                            <option>Mới nhất</option>
                            <option>Giá tăng dần</option>
                            <option>Giá giảm dần</option>
                        </select>
                    </div>
                </div>

                <?php if ($products->num_rows > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php while($row = $products->fetch_assoc()): ?>
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all group overflow-hidden border border-slate-100">
                                <a href="product_detail.php?id=<?= $row['id'] ?>" class="h-56 bg-slate-50 relative overflow-hidden flex items-center justify-center text-slate-400 text-4xl block">
                                    <?php if($row['image'] && file_exists("uploads/".$row['image'])): ?>
                                        <img src="uploads/<?= $row['image'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
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
                                        <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="bg-slate-50 hover:bg-primary hover:text-white text-secondary w-10 h-10 rounded-xl transition-all border border-slate-100 flex items-center justify-center">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
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

<?php include("includes/footer.php"); ?>
