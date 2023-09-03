<?php

try {
	$db = new PDO('mysql:host=localhost;dbname=library', 'root', '');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$error_message = 'Không thể kết nối đến CSDL';
	$reason = $e->getMessage();
	include 'show_error.php';

	include 'footer.php';
	exit();
}
