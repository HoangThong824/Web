<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = "";
$error = "";

// --- Pagination Logic ---
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) as total FROM news");
$total_items = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_items / $limit);

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Delete image
    $img_res = $conn->query("SELECT image FROM news WHERE id = $id");
    $img_row = $img_res->fetch_assoc();
    if ($img_row && $img_row['image'] && $img_row['image'] != 'placeholder.jpg') {
        @unlink("../uploads/" . $img_row['image']);
    }
    
    $conn->query("DELETE FROM news WHERE id = $id");
    $message = "Xóa bài viết thành công!";
    $action = 'list';
}

// Handle Add/Edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['user']['id'];

    if (empty($title) || empty($content)) {
        $error = "Vui lòng nhập tiêu đề và nội dung bài viết!";
    } else {
        $image_name = $_POST['current_image'] ?? 'placeholder.jpg';

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = "news_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $new_name)) {
                    $image_name = $new_name;
                    if (isset($_POST['current_image']) && $_POST['current_image'] != 'placeholder.jpg') {
                        @unlink("../uploads/" . $_POST['current_image']);
                    }
                }
            }
        }

        if (!$error) {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $id = $_POST['id'];
                $stmt = $conn->prepare("UPDATE news SET title=?, content=?, image=? WHERE id=?");
                $stmt->bind_param("sssi", $title, $content, $image_name, $id);
                $stmt->execute();
                $message = "Cập nhật bài viết thành công!";
            } else {
                $stmt = $conn->prepare("INSERT INTO news (title, content, author_id, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssis", $title, $content, $author_id, $image_name);
                $stmt->execute();
                $message = "Đăng bài viết mới thành công!";
            }
            $action = 'list';
        }
    }
}

include("header.php");
?>

<div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
    <div>
        <h4 class="text-2xl font-bold text-secondary mb-1">Tin tức</h4>
        <nav class="text-sm text-slate-400">
            <a href="dashboard.php" class="hover:text-primary">Dashboard</a> / 
            <span class="text-slate-600">Quản lý tin tức</span>
        </nav>
    </div>
    <?php if($action == 'list'): ?>
        <a href="news.php?action=add" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
            <i class="fas fa-pen"></i> Viết bài mới
        </a>
    <?php else: ?>
        <a href="news.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
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
    <div class="grid grid-cols-1 gap-6">
        <?php
        $res = $conn->query("SELECT n.*, u.fullname as author_name FROM news n LEFT JOIN users u ON n.author_id = u.id ORDER BY n.id DESC LIMIT $limit OFFSET $offset");
        if($res->num_rows > 0):
            while($row = $res->fetch_assoc()):
        ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-center group transition-all hover:border-primary/20">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 rounded-xl bg-slate-50 flex items-center justify-center overflow-hidden border border-slate-100">
                        <?php if($row['image'] && file_exists("../uploads/".$row['image'])): ?>
                            <img src="../uploads/<?= $row['image'] ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-newspaper text-slate-300 text-3xl"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="font-bold text-secondary text-lg mb-2 group-hover:text-primary transition-colors"><?= $row['title'] ?></h4>
                        <div class="flex items-center gap-4 text-xs text-slate-400">
                            <span class="flex items-center gap-1"><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($row['created_at'])) ?></span>
                            <span class="flex items-center gap-1"><i class="fas fa-user"></i> <?= $row['author_name'] ?: 'Admin' ?></span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="news.php?action=edit&id=<?= $row['id'] ?>" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="news.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa bài viết này?')" class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="mt-8 flex justify-center gap-2">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="news.php?page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all <?= $i == $page ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white border border-slate-100 hover:bg-slate-50 text-slate-600' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
            <p class="text-center py-20 text-slate-400 bg-white rounded-3xl border border-dashed">Chưa có bài viết nào.</p>
        <?php endif; ?>
    </div>

<?php elseif($action == 'add' || $action == 'edit'): 
    $edit_data = ['id' => '', 'title' => '', 'content' => '', 'image' => 'placeholder.jpg'];
    if($action == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $edit_data = $conn->query("SELECT * FROM news WHERE id = $id")->fetch_assoc();
    }
?>
    <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 max-w-5xl mx-auto">
        <h3 class="text-2xl font-bold text-secondary mb-8"><?= $action == 'add' ? 'Viết bài mới' : 'Chỉnh sửa bài viết' ?></h3>
        <form method="POST" enctype="multipart/form-data" class="space-y-8" id="newsForm">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <input type="hidden" name="current_image" value="<?= $edit_data['image'] ?>">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tiêu đề bài viết</label>
                        <input type="text" name="title" value="<?= $edit_data['title'] ?>" class="w-full px-5 py-4 rounded-xl border border-slate-200 outline-none focus:border-primary text-xl font-bold" placeholder="Nhập tiêu đề..." required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nội dung chi tiết</label>
                        <textarea name="content" rows="15" class="w-full px-5 py-4 rounded-xl border border-slate-200 outline-none focus:border-primary leading-relaxed" placeholder="Viết nội dung tại đây..." required><?= $edit_data['content'] ?></textarea>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Hình đại diện</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center bg-slate-50 relative cursor-pointer" onclick="document.getElementById('imageInput').click()">
                            <div id="imagePreview">
                                <?php if($edit_data['image'] && file_exists("../uploads/".$edit_data['image'])): ?>
                                    <img src="../uploads/<?= $edit_data['image'] ?>" class="w-full h-48 object-cover rounded-xl mb-4 shadow-sm">
                                <?php else: ?>
                                    <i class="fas fa-image text-4xl text-slate-200 mb-4"></i>
                                    <p class="text-xs text-slate-400">Chọn ảnh tiêu đề</p>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="image" id="imageInput" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-5 rounded-2xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                        <i class="fas fa-paper-plane mr-2"></i> <?= $action == 'add' ? 'Đăng bài viết' : 'Cập nhật bài viết' ?>
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
                document.getElementById('imagePreview').innerHTML = `<img src="${e.target.result}" class="w-full h-48 object-cover rounded-xl mb-4 shadow-sm">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
<?php endif; ?>

<?php include("footer.php"); ?>
