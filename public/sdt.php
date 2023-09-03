<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';
$username = $_SESSION['user']['username'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Lấy dữ liệu từ form cập nhật
    $sdt = $_POST['sdt'];

    // Cập nhật thông tin cá nhân vào database
    $stmt = $db->prepare("UPDATE user SET sdt=:sdt WHERE username=:username");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':sdt', $sdt);

    $stmt->execute();
    $_SESSION['user']['sdt'] = $sdt;
    $_SESSION['user']['success'] = "Change phone number successfully!";
    unset($_SESSION['user']['error']);

    header("Location: " . $_SERVER['HTTP_REFERER']);
}
?>