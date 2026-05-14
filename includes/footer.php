</main>

<footer class="bg-secondary text-stone-200 pt-20 pb-10 border-t border-primary/20">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <div>
                <h4 class="text-xl font-bold mb-6 text-white">Về chúng tôi</h4>
                <p class="text-stone-400 leading-relaxed">Khô Đặc Sản chuyên cung cấp các loại đồ khô vùng miền, đảm bảo vệ sinh an toàn
                    thực phẩm và hương vị truyền thống tinh túy.</p>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6 text-white">Liên kết</h4>
                <ul class="space-y-4 text-stone-400">
                    <li><a href="<?= $base_path ?>index.php"
                            class="hover:text-primary hover:translate-x-1 inline-block transition-all">Trang chủ</a></li>
                    <li><a href="<?= $base_path ?>pages/products/products.php"
                            class="hover:text-primary hover:translate-x-1 inline-block transition-all">Sản phẩm</a></li>
                    <li><a href="<?= $base_path ?>pages/news/news.php" class="hover:text-primary hover:translate-x-1 inline-block transition-all">Tin
                            tức</a></li>
                    <li><a href="<?= $base_path ?>pages/info/contact.php"
                            class="hover:text-primary hover:translate-x-1 inline-block transition-all">Liên hệ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6 text-white">Thông tin liên hệ</h4>
                <ul class="space-y-4 text-stone-400">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt mt-1 text-primary"></i>
                        <?= getSetting($conn, 'address') ?>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-primary"></i>
                        <?= getSetting($conn, 'phone') ?>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-primary"></i>
                        <?= getSetting($conn, 'email') ?>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6">Theo dõi</h4>
                <div class="flex gap-4">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-all text-xl"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-all text-xl"><i
                            class="fab fa-instagram"></i></a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center hover:bg-primary transition-all text-xl"><i
                            class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/5 pt-8 text-center text-stone-500 text-sm">
            <p>&copy; 2026 Khô Đặc Sản. Tất cả quyền được bảo lưu. Thiết kế bởi Team.</p>
        </div>
    </div>
</footer>

<script src="<?= $base_path ?>assets/main.js"></script>
</body>

</html>