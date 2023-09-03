<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';
$username = $_SESSION['user']['username'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Lấy dữ liệu từ form cập nhật
    $email = $_POST['email'];

        // Cập nhật thông tin cá nhân vào database
        $stmt = $db->prepare("UPDATE user SET email=:email WHERE username=:username");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);

        $stmt->execute();
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['success'] = "Change email successfully!";
        unset($_SESSION['user']['error']);

        header("Location: " . $_SERVER['HTTP_REFERER']);
}
?>