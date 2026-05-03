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

$page_title = "Giới thiệu";
include("includes/header.php");
?>

<section class="bg-secondary py-16 text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Giới thiệu về chúng tôi</h1>
        <p class="text-slate-300">Hành trình mang tinh hoa đồ khô đến mọi gia đình Việt.</p>
    </div>
</section>

<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="prose prose-lg prose-slate max-w-none">
                <div class="bg-white p-10 md:p-16 rounded-3xl shadow-xl border border-slate-100">
                    <h2 class="text-3xl font-bold text-secondary mb-8">Câu chuyện của Khô Đặc Sản</h2>
                    <div class="text-slate-600 leading-relaxed space-y-6">
                        <?= nl2br(getSetting($conn, 'about_page_full')) ?: 'Nội dung đang được cập nhật...' ?>
                    </div>
                    
                    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-center pt-12 border-t border-slate-100">
                        <div>
                            <h3 class="text-4xl font-bold text-primary mb-2">10+</h3>
                            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Năm kinh nghiệm</p>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold text-primary mb-2">500+</h3>
                            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Sản phẩm chất lượng</p>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold text-primary mb-2">10k+</h3>
                            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Khách hàng tin dùng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
