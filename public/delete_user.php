<?php
session_start();
require_once '../partials/db_connect.php';
include '../partials/check_admin.php';

if (!isset($_GET['id'])) {
    header('Location: ../');
} else {
    $query = $db->prepare('SELECT * FROM user WHERE user_id = :user_id');
    $query->bindParam(':user_id', $_GET['id']);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $query = $db->prepare('DELETE FROM user WHERE user_id = :user_id');
        $query->bindParam(':user_id', $user['user_id']);
        $query->execute();

        if ($user['file_avatar'] == 'avatarUser.png') {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            $target_dir = "avatar/";
            unlink($target_dir . $user['file_avatar']);
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    }
}
?>