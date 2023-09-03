<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';

$username = $_SESSION['user']['username'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Lấy dữ liệu từ form cập nhật
        if (isset($_POST['btnAvatar'])) {
                $file = $_FILES['file_avatar'];
                $allowType = ['image/png', 'image/jpeg', 'image/gif', 'image/tiff'];
                if (!in_array($file['type'], $allowType)) {
                        $_SESSION['user']['error'] = "Hãy chọn đúng file hình ảnh!";
                        unset($_SESSION['user']['success']);
                        die(header("Location: " . $_SERVER['HTTP_REFERER']));
                }
                //Lưu ảnh vào floder avatar
                $target_dir = "avatar/";
                $target_file = $target_dir . basename($_FILES["file_avatar"]["name"]);
                if ($_SESSION['user']['avatar'] == 'avatarUser.png') {
                        move_uploaded_file($_FILES["file_avatar"]["tmp_name"], $target_file);
                } else {
                        unlink($target_dir . $_SESSION['user']['avatar']);
                        move_uploaded_file($_FILES["file_avatar"]["tmp_name"], $target_file);
                }
                $stmt = $db->prepare("UPDATE user SET file_avatar=:file_avatar WHERE username=:username");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':file_avatar', $_FILES['file_avatar']['name']);

                $stmt->execute();

                $_SESSION['user']['avatar'] = $_FILES['file_avatar']['name'];
                $_SESSION['user']['success'] = "Change avatar successfully!";
                unset($_SESSION['user']['error']);

                header("Location: " . $_SERVER['HTTP_REFERER']);
        }
}

?>