<?php
session_start();
include("includes/db.php");

$page_title = "Hỏi/đáp";
include("includes/header.php");
?>

<section class="bg-secondary py-16 text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Câu hỏi thường gặp</h1>
        <p class="text-slate-300">Giải đáp các thắc mắc về sản phẩm và dịch vụ của chúng tôi.</p>
    </div>
</section>

<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto space-y-6">
            <?php
            $faqs = $conn->query("SELECT * FROM faqs ORDER BY id DESC");
            if ($faqs && $faqs->num_rows > 0):
                while($row = $faqs->fetch_assoc()):
            ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <button class="w-full px-8 py-6 text-left flex justify-between items-center group transition-all" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.fa-chevron-down').classList.toggle('rotate-180')">
                        <span class="text-lg font-bold text-secondary group-hover:text-primary transition-colors"><?= $row['question'] ?></span>
                        <i class="fas fa-chevron-down text-slate-400 group-hover:text-primary transition-all duration-300"></i>
                    </button>
                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 hidden">
                        <p class="text-slate-600 leading-relaxed"><?= nl2br($row['answer']) ?></p>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                    <p class="text-slate-400">Chưa có câu hỏi nào được cập nhật.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
