<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';

$query = $db->prepare('SELECT * FROM user');

$query->execute();

if (!isset($_GET['id'])) {
    header('Location: ../');
} else {
    $query = $db->prepare('DELETE FROM user WHERE user_id = :user_id');
    $query->bindParam(':user_id', $_GET['id']);

    $query->execute();
    if ($_SESSION['user']['avatar'] == 'avatarUser.png') {
        header("Location: ../");
        unset($_SESSION['user']);
    } else {
        header("Location: ../");
        $target_dir = "avatar/";
        unlink($target_dir . $_SESSION['user']['avatar']);
        unset($_SESSION['user']);
    }

}
?>