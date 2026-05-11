<?php 
session_start();
include("includes/db.php"); 

function getSetting($conn, $key) {
    $stmt = $conn->prepare("SELECT value FROM settings WHERE `key`=?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['value'] : '';
}

$page_title = "Trang chủ";
include("includes/header.php"); 
?>

<!-- Hero Section -->
<section class="relative h-[80vh] flex items-center justify-center bg-secondary overflow-hidden">
<<<<<<< HEAD
    <!-- Background Image -->
     <div class="absolute inset-0 z-0">
        <img src="uploads/banner.png" class="w-full h-full object-cover" alt="Banner">
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/30 z-10"></div>
=======
    <!-- Placeholder for Background Image -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/30 z-10"></div>
    <div class="absolute inset-0 bg-slate-800 flex items-center justify-center text-slate-700 text-6xl opacity-20">
        <i class="fas fa-image"></i>
    </div>
>>>>>>> 1c21ba5d9022a28c136ad0da4664c1d80d4c894b
    
    <div class="container mx-auto px-4 relative z-20 text-center text-white">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
            <?php echo getSetting($conn, 'site_name'); ?>
        </h1>
        <p class="text-xl md:text-2xl mb-10 max-w-2xl mx-auto text-slate-200">
            <?php echo getSetting($conn, 'homepage_content'); ?>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="pages/products/products.php" class="bg-primary hover:bg-primary-dark text-white px-8 py-4 rounded-full font-bold text-lg transition-all transform hover:scale-105">
                Mua ngay
            </a>
            <a href="pages/info/contact.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border-2 border-white/30 px-8 py-4 rounded-full font-bold text-lg transition-all">
                Liên hệ chúng tôi
            </a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-4xl font-bold text-secondary mb-2">Sản phẩm nổi bật</h2>
                <div class="w-20 h-1.5 bg-primary rounded-full"></div>
            </div>
            <a href="pages/products/products.php" class="text-primary font-bold hover:underline">Xem tất cả <i class="fas fa-arrow-right ml-1"></i></a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php
            $query = "SELECT p.*, c.name as category_name, 
                      AVG(co.rating) as avg_rating, 
                      COUNT(co.id) as review_count 
                      FROM products p 
                      JOIN categories c ON p.category_id = c.id 
                      LEFT JOIN comments co ON p.id = co.product_id AND co.status = 'approved'
                      WHERE p.is_featured = 1 
                      GROUP BY p.id 
                      LIMIT 4";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    $rating = round($row['avg_rating'] ?: 0);
                    $review_count = $row['review_count'];
            ?>
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all group overflow-hidden border border-slate-100">
                    <a href="pages/products/product_detail.php?id=<?php echo $row['id']; ?>" class="h-64 bg-slate-100 relative overflow-hidden flex items-center justify-center text-slate-400 text-4xl block">
                        <?php if($row['image'] && file_exists("uploads/".$row['image'])): ?>
                            <img src="uploads/<?= $row['image'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <i class="fas fa-fish group-hover:scale-125 transition-transform duration-500"></i>
                        <?php endif; ?>
                        <div class="absolute top-4 left-4 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full">
                            HOT
                        </div>
                    </a>
                    <div class="p-6">
                        <span class="text-primary text-xs font-bold uppercase tracking-wider mb-2 block"><?php echo $row['category_name']; ?></span>
                        <a href="pages/products/product_detail.php?id=<?php echo $row['id']; ?>">
                            <h3 class="text-lg font-bold text-secondary mb-1 hover:text-primary transition-colors"><?php echo $row['name']; ?></h3>
                        </a>
                        <div class="flex items-center gap-1 text-orange-400 text-xs mb-3">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="<?= $i <= $rating ? 'fas' : 'far' ?> fa-star"></i>
                            <?php endfor; ?>
                            <span class="text-slate-400 ml-1">(<?php echo $review_count; ?>)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-primary"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ <span class="text-[10px] text-slate-400 font-normal">/ 500g</span></span>
                            <button onclick="addToCart(<?= $row['id'] ?>)" class="bg-slate-100 hover:bg-primary hover:text-white text-secondary w-10 h-10 rounded-full transition-all">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-20 bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-16">
<<<<<<< HEAD
            <div class="lg:w-1/2 w-full h-[400px] rounded-3x1 overflow-hidden relative">
                <img src="uploads/combo.png" alt="" class="w-full h-full object-cover">
=======
            <div class="lg:w-1/2 bg-slate-200 h-[400px] w-full rounded-3xl flex items-center justify-center text-slate-400 text-6xl">
                <i class="fas fa-store"></i>
>>>>>>> 1c21ba5d9022a28c136ad0da4664c1d80d4c894b
            </div>
            <div class="lg:w-1/2">
                <span class="text-primary font-bold uppercase tracking-widest mb-4 block">Về chúng tôi</span>
                <h2 class="text-4xl md:text-5xl font-bold text-secondary mb-6 leading-tight">
                    Tinh hoa đặc sản khô Việt Nam
                </h2>
                <p class="text-slate-600 text-lg mb-8">
                    <?php echo getSetting($conn, 'about_us'); ?>
                </p>
                <div class="grid grid-cols-2 gap-6 mb-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary text-xl">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="font-bold text-secondary">Sạch & An toàn</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary text-xl">
                            <i class="fas fa-truck"></i>
                        </div>
                        <span class="font-bold text-secondary">Giao hàng nhanh</span>
                    </div>
                </div>
                <a href="pages/info/about.php" class="inline-block bg-secondary text-white px-8 py-3 rounded-xl font-bold hover:bg-slate-800 transition-all">
                    Tìm hiểu thêm
                </a>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>