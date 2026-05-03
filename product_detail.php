<?php
session_start();
include("includes/db.php");

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = $id");
$product = $res->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Handle Comment Submission
$comment_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    if (!isset($_SESSION['user'])) {
        $comment_msg = "Bạn cần đăng nhập để bình luận!";
    } else {
        $user_id = $_SESSION['user']['id'];
        $content = $_POST['content'];
        $rating = intval($_POST['rating']);
        
        $stmt = $conn->prepare("INSERT INTO comments (user_id, product_id, content, rating) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $user_id, $id, $content, $rating);
        if ($stmt->execute()) {
            $comment_msg = "Bình luận của bạn đã được gửi và đang chờ duyệt!";
        }
    }
}

$page_title = $product['name'];
include("includes/header.php");
?>

<section class="py-12 bg-slate-50">
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
                <div class="lg:w-1/2 bg-slate-100 flex items-center justify-center p-12 text-slate-300 text-9xl">
                    <i class="fas fa-fish"></i>
                </div>
                
                <!-- Product Info -->
                <div class="lg:w-1/2 p-8 md:p-16">
                    <span class="text-primary font-bold uppercase tracking-widest text-sm mb-4 block"><?= $product['category_name'] ?></span>
                    <h1 class="text-4xl md:text-5xl font-bold text-secondary mb-6"><?= $product['name'] ?></h1>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <span class="text-3xl font-bold text-primary"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                        <div class="h-6 w-px bg-slate-200"></div>
                        <div class="flex items-center gap-1 text-orange-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="text-slate-400 text-sm ml-2">(12 đánh giá)</span>
                        </div>
                    </div>

                    <p class="text-slate-600 text-lg mb-10 leading-relaxed">
                        <?= nl2br($product['description']) ?>
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="bg-primary hover:bg-primary-dark text-white px-10 py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1 flex-1 flex items-center justify-center gap-3">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                        </a>
                        <button class="bg-slate-100 hover:bg-slate-200 text-secondary px-8 py-4 rounded-xl font-bold transition-all flex items-center justify-center gap-3">
                            <i class="fas fa-heart"></i> Yêu thích
                        </button>
                    </div>

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
        <div class="mt-16 max-w-4xl">
            <h2 class="text-3xl font-bold text-secondary mb-10">Đánh giá sản phẩm</h2>
            
            <!-- Comment Form -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 mb-12">
                <?php if($comment_msg): ?>
                    <div class="bg-blue-50 text-blue-600 p-4 rounded-xl mb-6 font-bold">
                        <?= $comment_msg ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="font-bold text-secondary">Đánh giá của bạn:</span>
                        <div class="flex gap-2">
                            <select name="rating" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-1 outline-none focus:border-primary">
                                <option value="5">5 Sao (Rất tốt)</option>
                                <option value="4">4 Sao</option>
                                <option value="3">3 Sao</option>
                                <option value="2">2 Sao</option>
                                <option value="1">1 Sao</option>
                            </select>
                        </div>
                    </div>
                    <textarea name="content" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                    <button type="submit" name="submit_comment" class="bg-secondary hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold transition-all">
                        Gửi đánh giá
                    </button>
                </form>
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
