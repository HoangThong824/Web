<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$message = "";
$error = "";

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    $message = "Xóa danh mục thành công!";
}

// Handle Add/Edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (empty($name)) {
        $error = "Tên danh mục không được để trống!";
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $description, $id);
            $stmt->execute();
            $message = "Cập nhật danh mục thành công!";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            $stmt->execute();
            $message = "Thêm danh mục mới thành công!";
        }
    }
}

include("header.php");
?>

<div class="mb-8">
    <h2 class="text-3xl font-bold text-secondary">Quản lý danh mục</h2>
    <p class="text-slate-500">Phân loại sản phẩm để khách hàng dễ tìm kiếm.</p>
</div>

<?php if($message): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Category Form -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 h-fit">
        <?php
        $edit_data = ['id' => '', 'name' => '', 'description' => ''];
        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $edit_data = $conn->query("SELECT * FROM categories WHERE id = $id")->fetch_assoc();
            echo '<h3 class="text-xl font-bold text-secondary mb-6">Sửa danh mục</h3>';
        } else {
            echo '<h3 class="text-xl font-bold text-secondary mb-6">Thêm danh mục</h3>';
        }
        ?>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tên danh mục</label>
                <input type="text" name="name" value="<?= $edit_data['name'] ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Mô tả</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all"><?= $edit_data['description'] ?></textarea>
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                Lưu danh mục
            </button>
            <?php if ($edit_data['id']): ?>
                <a href="categories.php" class="block text-center text-sm text-slate-400 hover:text-secondary">Hủy chỉnh sửa</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Category List -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 font-bold">Danh mục</th>
                    <th class="px-6 py-4 font-bold">Mô tả</th>
                    <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php
                $res = $conn->query("SELECT * FROM categories ORDER BY id DESC");
                while($row = $res->fetch_assoc()):
                ?>
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="px-6 py-4 font-bold text-secondary"><?= $row['name'] ?></td>
                    <td class="px-6 py-4 text-sm text-slate-500"><?= $row['description'] ?></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="categories.php?action=edit&id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="categories.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa danh mục này?')" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
