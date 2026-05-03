<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = "";
$error = "";

// --- Pagination Logic ---
$limit = 5; // Items per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) as total FROM products");
$total_items = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_items / $limit);

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Delete image file if exists
    $img_res = $conn->query("SELECT image FROM products WHERE id = $id");
    $img_row = $img_res->fetch_assoc();
    if ($img_row && $img_row['image'] && $img_row['image'] != 'placeholder.jpg') {
        @unlink("../uploads/" . $img_row['image']);
    }
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Xóa sản phẩm thành công!";
    }
    $action = 'list';
}

// Handle Add/Edit Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Server-side validation
    if (empty($name) || empty($price)) {
        $error = "Vui lòng nhập đầy đủ tên và giá sản phẩm!";
    } else {
        $image_name = $_POST['current_image'] ?? 'placeholder.jpg';

        // Image Upload Logic
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_name = time() . "_" . uniqid() . "." . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)) {
                    $image_name = $new_name;
                    // Delete old image if updating
                    if (isset($_POST['current_image']) && $_POST['current_image'] != 'placeholder.jpg') {
                        @unlink("../uploads/" . $_POST['current_image']);
                    }
                }
            } else {
                $error = "Định dạng ảnh không hợp lệ!";
            }
        }

        if (!$error) {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update
                $id = $_POST['id'];
                $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, price=?, description=?, is_featured=?, image=? WHERE id=?");
                $stmt->bind_param("sidsisi", $name, $category_id, $price, $description, $is_featured, $image_name, $id);
                $stmt->execute();
                $message = "Cập nhật sản phẩm thành công!";
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, description, is_featured, image) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sidsis", $name, $category_id, $price, $description, $is_featured, $image_name);
                $stmt->execute();
                $message = "Thêm sản phẩm mới thành công!";
            }
            $action = 'list';
        }
    }
}

include("header.php");
?>

<!-- Srtdash-style Breadcrumb Area -->
<div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
    <div>
        <h4 class="text-2xl font-bold text-secondary mb-1">Sản phẩm</h4>
        <nav class="text-sm text-slate-400">
            <a href="dashboard.php" class="hover:text-primary">Dashboard</a> / 
            <span class="text-slate-600">Quản lý sản phẩm</span>
        </nav>
    </div>
    <?php if($action == 'list'): ?>
        <a href="products.php?action=add" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    <?php else: ?>
        <a href="products.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Danh sách
        </a>
    <?php endif; ?>
</div>

<?php if($message): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<?php if($error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= $error ?></span>
    </div>
<?php endif; ?>

<?php if($action == 'list'): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-5 font-bold">Sản phẩm</th>
                    <th class="px-6 py-5 font-bold">Danh mục</th>
                    <th class="px-6 py-5 font-bold">Giá</th>
                    <th class="px-6 py-5 font-bold">Nổi bật</th>
                    <th class="px-6 py-5 font-bold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php
                $res = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT $limit OFFSET $offset");
                while($row = $res->fetch_assoc()):
                ?>
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                                <?php if($row['image'] && file_exists("../uploads/".$row['image'])): ?>
                                    <img src="../uploads/<?= $row['image'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-fish text-slate-400"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="font-bold text-secondary"><?= $row['name'] ?></div>
                                <div class="text-xs text-slate-400">ID: #<?= $row['id'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-sm text-slate-600"><?= $row['category_name'] ?></td>
                    <td class="px-6 py-5 font-bold text-primary"><?= number_format($row['price'], 0, ',', '.') ?>đ</td>
                    <td class="px-6 py-5">
                        <span class="<?= $row['is_featured'] ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-400' ?> text-[10px] font-bold px-3 py-1 rounded-full uppercase">
                            <?= $row['is_featured'] ? 'Featured' : 'Normal' ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="products.php?action=edit&id=<?= $row['id'] ?>" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="products.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?')" class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination Area -->
        <?php if($total_pages > 1): ?>
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
            <span class="text-sm text-slate-500">Hiển thị trang <?= $page ?> / <?= $total_pages ?></span>
            <div class="flex gap-2">
                <?php if($page > 1): ?>
                    <a href="products.php?page=<?= $page-1 ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-primary hover:text-white transition-all">Trước</a>
                <?php endif; ?>
                
                <?php for($i=1; $i<=$total_pages; $i++): ?>
                    <a href="products.php?page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-lg text-sm transition-all <?= $i == $page ? 'bg-primary text-white font-bold' : 'bg-white border border-slate-200 hover:bg-slate-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <a href="products.php?page=<?= $page+1 ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-primary hover:text-white transition-all">Tiếp</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php elseif($action == 'add' || $action == 'edit'): 
    $edit_data = ['id' => '', 'name' => '', 'category_id' => '', 'price' => '', 'description' => '', 'is_featured' => 0, 'image' => 'placeholder.jpg'];
    if($action == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $edit_data = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
    }
?>
    <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 max-w-4xl mx-auto">
        <h3 class="text-2xl font-bold text-secondary mb-8"><?= $action == 'add' ? 'Thêm sản phẩm mới' : 'Chỉnh sửa sản phẩm' ?></h3>
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8" id="productForm">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <input type="hidden" name="current_image" value="<?= $edit_data['image'] ?>">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tên sản phẩm</label>
                    <input type="text" name="name" value="<?= $edit_data['name'] ?>" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Danh mục</label>
                        <select name="category_id" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required>
                            <?php
                            $cats = $conn->query("SELECT * FROM categories");
                            while($c = $cats->fetch_assoc()):
                            ?>
                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $edit_data['category_id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Giá (VNĐ)</label>
                        <input type="number" name="price" value="<?= $edit_data['price'] ?>" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả chi tiết</label>
                    <textarea name="description" rows="6" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all"><?= $edit_data['description'] ?></textarea>
                </div>
                <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl">
                    <input type="checkbox" name="is_featured" id="is_featured" class="w-5 h-5 accent-primary" <?= $edit_data['is_featured'] ? 'checked' : '' ?>>
                    <label for="is_featured" class="text-sm font-bold text-secondary">Sản phẩm nổi bật (Trang chủ)</label>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Hình ảnh sản phẩm</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center bg-slate-50 hover:bg-slate-100 transition-all cursor-pointer relative" onclick="document.getElementById('imageInput').click()">
                        <div id="imagePreview" class="flex flex-col items-center">
                            <?php if($edit_data['image'] && file_exists("../uploads/".$edit_data['image'])): ?>
                                <img src="../uploads/<?= $edit_data['image'] ?>" class="w-48 h-48 object-cover rounded-2xl mb-4 shadow-md">
                                <span class="text-primary font-bold">Thay đổi ảnh</span>
                            <?php else: ?>
                                <i class="fas fa-cloud-upload-alt text-5xl text-slate-300 mb-4"></i>
                                <span class="text-slate-400 font-medium">Nhấn để tải lên hoặc kéo thả ảnh</span>
                                <span class="text-xs text-slate-300 mt-2">Định dạng: JPG, PNG, WEBP</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="image" id="imageInput" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                </div>
                
                <div class="pt-6">
                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-5 rounded-2xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                        <i class="fas fa-save mr-2"></i> <?= $action == 'add' ? 'Lưu sản phẩm' : 'Cập nhật thay đổi' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `<img src="${e.target.result}" class="w-48 h-48 object-cover rounded-2xl mb-4 shadow-md">
                                     <span class="text-primary font-bold text-sm">Ảnh đã chọn</span>`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Basic Client-side validation
    document.getElementById('productForm').onsubmit = function() {
        const name = this.name.value.trim();
        const price = this.price.value;
        if(!name || price <= 0) {
            alert('Vui lòng nhập đầy đủ thông tin hợp lệ!');
            return false;
        }
        return true;
    };
    </script>
<?php endif; ?>

<?php include("footer.php"); ?>
