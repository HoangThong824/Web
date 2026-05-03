<?php
require_once '../includes/auth.php';
include("../includes/db.php");

checkAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = "";

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM faqs WHERE id = $id");
    $message = "Xóa câu hỏi thành công!";
    $action = 'list';
}

// Handle Add/Edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE faqs SET question=?, answer=? WHERE id=?");
        $stmt->bind_param("ssi", $question, $answer, $id);
        $stmt->execute();
        $message = "Cập nhật câu hỏi thành công!";
    } else {
        $stmt = $conn->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)");
        $stmt->bind_param("ss", $question, $answer);
        $stmt->execute();
        $message = "Thêm câu hỏi mới thành công!";
    }
    $action = 'list';
}

include("header.php");
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold text-secondary">Quản lý Hỏi/đáp</h2>
        <p class="text-slate-500">Quản lý danh sách các câu hỏi thường gặp của khách hàng.</p>
    </div>
    <?php if($action == 'list'): ?>
        <a href="faqs.php?action=add" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Thêm câu hỏi
        </a>
    <?php else: ?>
        <a href="faqs.php" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    <?php endif; ?>
</div>

<?php if($message): ?>
    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<?php if($action == 'list'): ?>
    <div class="space-y-4">
        <?php
        $res = $conn->query("SELECT * FROM faqs ORDER BY id DESC");
        if($res->num_rows > 0):
            while($row = $res->fetch_assoc()):
        ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-start gap-6">
                <div>
                    <h4 class="font-bold text-secondary mb-2 text-lg"><?= $row['question'] ?></h4>
                    <p class="text-slate-500 text-sm line-clamp-2"><?= $row['answer'] ?></p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="faqs.php?action=edit&id=<?= $row['id'] ?>" class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="faqs.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa câu hỏi này?')" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        <?php endwhile; else: ?>
            <p class="text-center py-20 text-slate-400 bg-white rounded-2xl border border-dashed">Chưa có câu hỏi nào.</p>
        <?php endif; ?>
    </div>

<?php elseif($action == 'add' || $action == 'edit'): 
    $edit_data = ['id' => '', 'question' => '', 'answer' => ''];
    if($action == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $edit_data = $conn->query("SELECT * FROM faqs WHERE id = $id")->fetch_assoc();
    }
?>
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 max-w-2xl mx-auto">
        <h3 class="text-xl font-bold text-secondary mb-6"><?= $action == 'add' ? 'Thêm câu hỏi mới' : 'Chỉnh sửa câu hỏi' ?></h3>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Câu hỏi</label>
                <input type="text" name="question" value="<?= $edit_data['question'] ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Câu trả lời</label>
                <textarea name="answer" rows="8" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-primary transition-all" required><?= $edit_data['answer'] ?></textarea>
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                Lưu câu hỏi
            </button>
        </form>
    </div>
<?php endif; ?>

<?php include("footer.php"); ?>
