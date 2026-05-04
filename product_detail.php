<?php
session_start();
include("includes/db.php");

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT p.*, c.name as category_name, 
          AVG(co.rating) as avg_rating, 
          COUNT(co.id) as review_count 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          LEFT JOIN comments co ON p.id = co.product_id AND co.status = 'approved'
          WHERE p.id = $id
          GROUP BY p.id";
$res = $conn->query($query);
$product = $res->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Handle Comment Submission
$comment_msg = "";
$can_review = false;
if (isset($_SESSION['user'])) {
    $uid = $_SESSION['user']['id'];
    // Check if user has bought this product and order is delivered
    $check_purchase = $conn->query("SELECT oi.id FROM order_items oi 
                                    JOIN orders o ON oi.order_id = o.id 
                                    WHERE o.user_id = $uid AND oi.product_id = $id AND o.status = 'delivered'");
    if ($check_purchase->num_rows > 0) {
        $can_review = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    if (!isset($_SESSION['user'])) {
        $comment_msg = "Bạn cần đăng nhập để đánh giá!";
    } elseif (!$can_review) {
        $comment_msg = "Bạn chỉ có thể đánh giá sản phẩm sau khi đã mua và nhận hàng thành công!";
    } else {
        $user_id = $_SESSION['user']['id'];
        $content = $conn->real_escape_string($_POST['content']);
        $rating = intval($_POST['rating']);
        
        $stmt = $conn->prepare("INSERT INTO comments (user_id, product_id, content, rating, status) VALUES (?, ?, ?, ?, 'approved')");
        $stmt->bind_param("iisi", $user_id, $id, $content, $rating);
        if ($stmt->execute()) {
            $comment_msg = "Cảm ơn bạn đã đánh giá sản phẩm!";
        }
    }
}

$page_title = $product['name'];
include("includes/header.php");
?>

<!-- Product Detail Hero Banner -->
<div class="relative w-full overflow-hidden mt-6">
    <img src="image/banner.png" alt="Banner" class="w-full h-auto block">
    <!-- Fade-out Gradient Overlay -->
    <div class="absolute bottom-0 left-0 w-full h-1/3 bg-gradient-to-t from-slate-50 to-transparent"></div>
</div>

<section class="pb-12 pt-8 bg-slate-50">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-slate-500 mb-8">
            <a href="index.php" class="hover:text-primary">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="products.php" class="hover:text-primary">Sản phẩm</a>
            <span class="mx-2">/</span>
            <span class="text-slate-800 font-bold"><?= $product['name'] ?></span>
        </nav>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
            <div class="flex flex-col lg:flex-row">
                <!-- Product Image -->
                <div class="lg:w-1/2 bg-slate-50 flex items-center justify-center p-8 md:p-12 text-slate-300 text-9xl overflow-hidden">
                    <?php if($product['image'] && file_exists("uploads/".$product['image'])): ?>
                        <img src="uploads/<?= $product['image'] ?>" class="w-full h-full object-contain hover:scale-105 transition-transform duration-700">
                    <?php else: ?>
                        <i class="fas fa-fish"></i>
<?php endif; ?>
                </div>
                
                <!-- Product Info -->
                <div class="lg:w-1/2 p-8 md:p-16">
                    <span class="text-primary font-bold uppercase tracking-widest text-sm mb-4 block"><?= $product['category_name'] ?></span>
                    <h1 class="text-4xl md:text-5xl font-bold text-secondary mb-6"><?= $product['name'] ?></h1>
                    
                    <div class="flex items-center gap-4 mb-6">
                        <?php 
                            $rating = round($product['avg_rating'] ?: 0);
                            $review_count = $product['review_count'];
                        ?>
                        <div class="flex items-center gap-1 text-orange-400">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="<?= $i <= $rating ? 'fas' : 'far' ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="text-slate-400 text-sm">(<?= $review_count ?> đánh giá)</span>
                        <span class="text-green-500 text-sm font-bold ml-4"><i class="fas fa-check-circle"></i> Còn hàng</span>
                    </div>

                    <div class="text-3xl font-bold text-primary mb-8"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ <span class="text-lg text-slate-400 font-normal">/ 500g</span></div>

                    <p class="text-slate-600 text-lg mb-10 leading-relaxed">
                        <?= nl2br($product['description']) ?>
                    </p>

                    <div class="flex flex-col gap-6 mb-10">
                        <div class="flex items-center gap-6">
                            <span class="font-bold text-secondary">Số lượng:</span>
                            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-white">
                                <button type="button" onclick="changeQty(-1)" class="px-4 py-2 hover:bg-slate-50 transition-all border-r border-slate-200"><i class="fas fa-minus text-xs text-slate-400"></i></button>
                                <input type="number" id="buy-qty" value="1" min="1" class="w-16 text-center font-bold text-secondary outline-none py-2 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button type="button" onclick="changeQty(1)" class="px-4 py-2 hover:bg-slate-50 transition-all border-l border-slate-200"><i class="fas fa-plus text-xs text-slate-400"></i></button>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="javascript:void(0)" onclick="addToCart(<?= $product['id'] ?>, document.getElementById('buy-qty').value)" class="bg-primary hover:bg-primary-dark text-white px-10 py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1 flex-1 flex items-center justify-center gap-3">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                            </a>
                            <button class="bg-slate-100 hover:bg-slate-200 text-secondary px-8 py-4 rounded-xl font-bold transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-heart"></i> Yêu thích
                            </button>
                        </div>
                    </div>

                    <script>
                    function changeQty(amt) {
                        const input = document.getElementById('buy-qty');
                        let val = parseInt(input.value) + amt;
                        if (val < 1) val = 1;
                        input.value = val;
                    }
                    </script>

                    <div class="mt-12 pt-12 border-t border-slate-100 grid grid-cols-2 gap-8">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-truck text-primary text-xl"></i>
                            <div>
                                <h4 class="font-bold text-secondary text-sm">Giao hàng nhanh</h4>
                                <p class="text-xs text-slate-500">Toàn quốc trong 2-3 ngày</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <i class="fas fa-shield-alt text-primary text-xl"></i>
                            <div>
                                <h4 class="font-bold text-secondary text-sm">Bảo hành 12 tháng</h4>
                                <p class="text-xs text-slate-500">Đổi trả nếu không hài lòng</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div id="reviews" class="mt-16 max-w-4xl">
            <h2 class="text-3xl font-bold text-secondary mb-10">Đánh giá sản phẩm</h2>
            
            <!-- Comment Form -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 mb-12">
                <?php if($comment_msg): ?>
                    <div class="bg-blue-50 text-blue-600 p-4 rounded-xl mb-6 font-bold">
                        <?= $comment_msg ?>
                    </div>
                <?php endif; ?>

                <?php if(!isset($_SESSION['user'])): ?>
                    <div class="text-center py-6">
                        <p class="text-slate-500 mb-4">Bạn cần đăng nhập để đánh giá sản phẩm này.</p>
                        <a href="login.php" class="bg-primary text-white px-6 py-2 rounded-lg font-bold">Đăng nhập ngay</a>
                    </div>
                <?php elseif(!$can_review): ?>
                    <div class="flex items-center gap-4 text-orange-600 bg-orange-50 p-4 rounded-xl border border-orange-100">
                        <i class="fas fa-info-circle text-xl"></i>
                        <p class="text-sm font-medium">Bạn chưa mua sản phẩm này hoặc đơn hàng chưa hoàn tất. Chỉ những khách hàng đã mua mới có thể đánh giá.</p>
                    </div>
                <?php else: ?>
                    <form method="POST" class="space-y-6">
                        <div class="flex items-center gap-6 mb-4">
                            <span class="font-bold text-secondary">Chọn mức độ hài lòng:</span>
                            <div class="flex gap-2 text-2xl text-slate-300 cursor-pointer" id="star-rating">
                                <i class="fas fa-star hover:text-orange-400 transition-colors" data-value="1"></i>
                                <i class="fas fa-star hover:text-orange-400 transition-colors" data-value="2"></i>
                                <i class="fas fa-star hover:text-orange-400 transition-colors" data-value="3"></i>
                                <i class="fas fa-star hover:text-orange-400 transition-colors" data-value="4"></i>
                                <i class="fas fa-star hover:text-orange-400 transition-colors" data-value="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="5">
                        </div>
                        <textarea name="content" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                        <button type="submit" name="submit_comment" class="bg-secondary hover:bg-slate-800 text-white px-10 py-4 rounded-xl font-bold transition-all shadow-lg shadow-secondary/20">
                            Gửi đánh giá ngay
                        </button>
                    </form>
                    
                    <script>
                    const stars = document.querySelectorAll('#star-rating i');
                    const input = document.getElementById('rating-input');
                    
                    stars.forEach(star => {
                        star.addEventListener('click', () => {
                            const val = star.getAttribute('data-value');
                            input.value = val;
                            
                            stars.forEach(s => {
                                if (s.getAttribute('data-value') <= val) {
                                    s.classList.remove('text-slate-300');
                                    s.classList.add('text-orange-400');
                                } else {
                                    s.classList.remove('text-orange-400');
                                    s.classList.add('text-slate-300');
                                }
                            });
                        });
                    });
                    // Set default 5 stars
                    document.querySelector('#star-rating i[data-value="5"]').click();
                    </script>
                <?php endif; ?>
            </div>

            <!-- Comment List -->
            <div class="space-y-8">
                <?php
                $comments = $conn->query("SELECT c.*, u.fullname, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.product_id = $id AND c.status = 'approved' ORDER BY c.created_at DESC");
                if ($comments->num_rows > 0):
                    while($c = $comments->fetch_assoc()):
                ?>
                    <div class="flex gap-6">
                        <div class="w-12 h-12 rounded-full bg-slate-200 flex-shrink-0 flex items-center justify-center text-slate-400 font-bold">
                            <?= strtoupper(substr($c['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-bold text-secondary"><?= $c['fullname'] ?: $c['username'] ?></span>
                                <span class="text-xs text-slate-400"><?= date('d/m/Y', strtotime($c['created_at'])) ?></span>
                                <div class="flex text-orange-400 text-[10px] gap-0.5">
                                    <?php for($i=0; $i<$c['rating']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                                </div>
                            </div>
                            <p class="text-slate-600"><?= nl2br($c['content']) ?></p>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                    <p class="text-slate-400 italic">Chưa có đánh giá nào cho sản phẩm này.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
