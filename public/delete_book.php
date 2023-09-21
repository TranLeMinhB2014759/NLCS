<?php
session_start();
require_once '../partials/db_connect.php';
include '../partials/check_admin.php';

if (!isset($_GET['id'])) {
    header('Location: ../');
} else {
    $query = $db->prepare('SELECT * FROM quyensach WHERE book_id = :book_id');
    $query->bindParam(':book_id', $_GET['id']);
    $query->execute();
    $book = $query->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        $query = $db->prepare('DELETE FROM quyensach WHERE book_id = :book_id');
        $query->bindParam(':book_id', $book['book_id']);
        $query->execute();

        $target_dir = "uploads/";
        unlink($target_dir . $book['book_img']);
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}
?>