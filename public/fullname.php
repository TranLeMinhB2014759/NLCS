<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';
$username = $_SESSION['user']['username'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Lấy dữ liệu từ form cập nhật
    $fullname = $_POST['fullname'];

    // Cập nhật thông tin cá nhân vào database
    $stmt = $db->prepare("UPDATE user SET fullname=:fullname WHERE username=:username");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':fullname', $fullname);

    $stmt->execute();
    $_SESSION['user']['name'] = $fullname;
    $_SESSION['user']['success'] = "Successfully renamed!";
    unset($_SESSION['user']['error']);

    header("Location: " . $_SERVER['HTTP_REFERER']);

}
?>