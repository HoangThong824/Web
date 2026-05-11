<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "assignment_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");
<<<<<<< HEAD
?>

<?php
if (!function_exists('getSetting')) {
    function getSetting($conn, $key) {
        $stmt = $conn->prepare("SELECT value FROM settings WHERE `key` = ?");
        $stmt->bind_param("s", $key);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['value'] ?? '';
    }
}
=======
>>>>>>> 1c21ba5d9022a28c136ad0da4664c1d80d4c894b
?>