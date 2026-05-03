<?php
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Dữ liệu không hợp lệ!");
    }

    $stmt = $conn->prepare("INSERT INTO contacts(name,email,message) VALUES(?,?,?)");
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();

    echo "Gửi thành công!";
}
?>