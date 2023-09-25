<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
if (isset($_GET['title_id'])) {
    $_SESSION['title']['title_id'] = $_GET['title_id'];

    $query_check_id = "SELECT title_id, title_name FROM dausach WHERE title_id = :giaTri";
    $title_check_id = $db->prepare($query_check_id);
    $title_check_id->bindParam(':giaTri', $_SESSION['title']['title_id'], PDO::PARAM_STR);
    $title_check_id->execute();
    if ($title_check_id->rowCount() > 0) {
        //Quyen sach
        $title = $title_check_id->fetch(PDO::FETCH_ASSOC);
        $query_b = "SELECT * FROM quyensach WHERE title_id = :title_id";
        $book = $db->prepare($query_b);
        $book->bindParam(':title_id', $_GET['title_id']);
        $book->execute();
        $count_qs = $book->rowCount();
        $data = [];
        while ($row = $book->fetch(PDO::FETCH_ASSOC)) {
            $data[] = array(
                'book_stt' => $row['book_stt'],
                'book_status' => $row['book_status'],
            );
        }
    } else {
        echo '<script>
        var confirmation = confirm("Mã sách bạn tìm không có trong cơ sở dữ liệu");
        if (confirmation) {
            window.location.href = "manage_titles.php";
        }else{
            window.location.href = "manage_titles.php";
        }
        </script>';
    }
} else {
    header("Location: manage_titles.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Libary</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/partials.css">
    <link rel="stylesheet" href="css/loader.css">
    <link rel="stylesheet" href="css/manage.css">
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <div class="breadcrumb-title">Quản lý quyển sách
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="manage_titles.php">Đầu sách</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quyển sách</li>
                </ol>
            </nav>
        </div>
        <div class="title-book">
            <?= $title['title_name'] ?>
        </div>

        <?php echo '<div class="quantity">Tổng số sách hiện tại: <strong>' . $count_qs . '</strong></div>'; ?>

        <div class="container-m">
            <table>
                <tr>
                    <th>Mã số đầu sách</th>
                    <th>Mã số quyển sách</th>
                    <th>Trạng thái</th>
                </tr>
                <?php foreach ($data as $book): ?>
                    <tr>
                        <td>
                            <?= $title['title_id'] ?>
                        </td>
                        <td>
                            <?= $book['book_stt'] ?>
                        </td>
                        <?php if ($book['book_status'] == '0'): ?>
                            <td>
                                <span style="color: red">Đang mượn</span>
                            </td>
                        <?php elseif ($book['book_status'] == '1'): ?>
                            <td>
                                <span style="color: green">Chưa mượn</span>
                            </td>
                        <?php endif; ?>
                    <tr>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
    </div>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
</body>

</html>