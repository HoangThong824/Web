<?php
include("../includes/db.php");
include("../includes/auth.php");
checkAdmin();
include("header.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        $stmt = $conn->prepare("UPDATE settings SET value=? WHERE `key`=?");
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }
    echo "<p class='success'>Cập nhật thành công!</p>";
}

function getSetting($conn, $key) {
    $stmt = $conn->prepare("SELECT value FROM settings WHERE `key`=?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['value'] : '';
}
?>

<h2>Chỉnh nội dung website</h2>

<form method="POST" class="form-box">

    <label>Tên website</label>
    <input type="text" name="site_name" 
        value="<?= getSetting($conn,'site_name') ?>">

    <label>Số điện thoại</label>
    <input type="text" name="phone" 
        value="<?= getSetting($conn,'phone') ?>">

    <label>Email</label>
    <input type="text" name="email" 
        value="<?= getSetting($conn,'email') ?>">

    <label>Địa chỉ</label>
    <input type="text" name="address" 
        value="<?= getSetting($conn,'address') ?>">

    <label>Nội dung trang chủ</label>
    <textarea name="homepage_content"><?= getSetting($conn,'homepage_content') ?></textarea>

    <button type="submit">💾 Lưu thay đổi</button>
</form>

<?php include("footer.php"); ?>