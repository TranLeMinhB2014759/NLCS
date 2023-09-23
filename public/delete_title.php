<?php
session_start();
require_once '../partials/db_connect.php';
include '../partials/check_admin.php';

if (!isset($_GET['id'])) {
    header('Location: ../');
} else {
    $query = $db->prepare('SELECT * FROM dausach WHERE title_id = :title_id');
    $query->bindParam(':title_id', $_GET['id']);
    $query->execute();
    $title = $query->fetch(PDO::FETCH_ASSOC);

    if ($title) {
        $query = $db->prepare('DELETE FROM dausach WHERE title_id = :title_id');
        $query->bindParam(':title_id', $title['title_id']);
        $query->execute();

        $target_dir = "uploads/";
        unlink($target_dir . $title['title_img']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}
?>