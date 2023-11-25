<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
//Lấy dữ liệu
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
                'title_id' => $row['title_id'],
                'book_status' => $row['book_status'],
                'book_added_day' => $row['book_added_day'],
                'broken' => $row['broken'],
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

//Add
if (isset($_POST["book_added_day"])) {
    $title_id = $_POST["title_id"];
    $book_added_day = $_POST['book_added_day'];
    $quantity = $_POST['quantity'];
    for ($i = 0; $i < $quantity; $i++) {
        $sql_max_stt = 'SELECT MAX(book_stt) as max_stt FROM quyensach WHERE title_id=:title_id';
        $stmt_max_stt = $db->prepare($sql_max_stt);
        $stmt_max_stt->bindParam(':title_id', $title_id, PDO::PARAM_INT);
        $stmt_max_stt->execute();
        $row = $stmt_max_stt->fetch(PDO::FETCH_ASSOC);

        if ($row["max_stt"] !== null) {
            $max_stt = $row["max_stt"];
        } else {
            // Không có dòng dữ liệu nào trả về
            $max_stt = 0;
        }

        $new_book_stt = $max_stt + 1;
        $sql_insert_book = "INSERT INTO quyensach (title_id, book_stt, book_added_day) VALUES (:title_id, :book_stt, :book_added_day)";
        $stmt_insert = $db->prepare($sql_insert_book);
        $stmt_insert->bindParam(':title_id', $title_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':book_stt', $new_book_stt, PDO::PARAM_INT);
        $stmt_insert->bindParam(':book_added_day', $book_added_day);
        $stmt_insert->execute();
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
}

//Broken
if (isset($_GET['broken'])) {
    $query = $db->prepare('SELECT * FROM quyensach WHERE title_id=:title_id AND book_stt =:book_stt');
    $query->bindParam(':title_id', $_SESSION['title']['title_id']);
    $query->bindParam(':book_stt', $_GET['book_stt']);
    $query->execute();
    $book_broken = $query->fetch(PDO::FETCH_ASSOC);
    if ($book_broken) {
        $broken = $_GET['broken'];
        $query = $db->prepare('UPDATE quyensach set broken=:broken WHERE title_id=:title_id AND book_stt =:book_stt');
        $query->bindParam(':broken', $broken);
        $query->bindParam(':title_id', $_SESSION['title']['title_id']);
        $query->bindParam(':book_stt', $_GET['book_stt']);
        $query->execute();
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}

//Delete
if (isset($_GET['delete_book_stt'])) {
    $book_stt = $_GET['delete_book_stt'];
    $query = $db->prepare('SELECT * FROM quyensach WHERE title_id=:title_id AND book_stt =:book_stt');
    $query->bindParam(':title_id', $_SESSION['title']['title_id']);
    $query->bindParam(':book_stt', $book_stt);
    $query->execute();
    $book_delete = $query->fetch(PDO::FETCH_ASSOC);
    if ($book_delete) {
        $query = $db->prepare('DELETE FROM quyensach WHERE title_id=:title_id AND book_stt =:book_stt');
        $query->bindParam(':title_id', $_SESSION['title']['title_id']);
        $query->bindParam(':book_stt', $book_stt);
        $query->execute();
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
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
    <link rel="stylesheet" href="css/manage.css">
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>
    <div class="wrapper">
        <div class="sidebar">
            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="dashboard.html" class="simple-text">
                        <img src="image/logo.png" alt="logoctu">
                        Quản lý thư viện
                    </a>
                </div>
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.html">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users_student.php">
                            <i class="fa-solid fa-user"></i>
                            <p>Manage Students</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users_teacher.php">
                            <i class="fa-solid fa-user"></i>
                            <p>Manage Teachers</p>
                        </a>
                    </li>
                    <li class="nav-item  active">
                        <a class="nav-link" href="manage_titles.php">
                            <i class="fa-solid fa-book"></i>
                            <p>Manage Titles</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_callcard.php">
                            <i class="fa-solid fa-ticket"></i>
                            <p>Manage Call Cards</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="statistic.php">
                            <i class="fa-solid fa-chart-pie"></i>
                            <p>Thống kê</p>
                        </a>
                    </li>
                    <li class="nav-item active active-pro">
                        <a class="nav-link active" href="logout.php">
                            <p>Log out</p>
                            <i class="fa fa-sign-out"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <div class="content">
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
                    <div class="quantity">
                        <?php echo 'Tổng số sách hiện tại: <strong>' . $count_qs . '</strong>'; ?>
                        <button type="button" class="btn btn-primary" style="margin-left: 3px; margin-bottom: 3px"
                            data-bs-toggle="modal" data-bs-target="#modal" title="Thêm quyển sách"><i
                                class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <!-- The Modal -->
                    <div class="modal" id="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h2 class="modal-title">Thêm quyển sách</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <!-- Modal body -->
                                <div class="modal-body">
                                    <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="title_name" class="form-label"><b>Tên sách:</b></label>
                                            <input class="form-control" id="title_name" name="title_name"
                                                value="<?= $title['title_name'] ?>" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label for="book_added_day" class="form-label"><b>Ngày thêm:</b></label>
                                            <input class="form-control" value="<?= date('d-m-Y') ?>" disabled>
                                        </div>
                                        <input id="book_added_day" name="book_added_day" hidden
                                            value="<?= date('d-m-Y') ?>">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label"><b>Số lượng:</b></label>
                                            <input type="number" min="1" max="20" class="form-control" id="quantity"
                                                name="quantity" value="1" required>
                                        </div>
                                        <input id="title_id" name="title_id" hidden value="<?= $title['title_id'] ?>">
                                </div>
                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="submit" name="submit1" class="btn btn-primary btn-block">
                                        OK
                                    </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-block" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-m">
                    <table>
                        <tr>
                            <th>Mã số quyển sách</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Bị mất / Hỏng</th>
                            <th>Xóa</th>
                        </tr>
                        <?php foreach ($data as $book): ?>
                            <tr>
                                <td>
                                    <?= "CNTT." . $book['title_id'] . str_pad($book['book_stt'], 3, '0', STR_PAD_LEFT) ?>
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
                                <td>
                                    <?= $book['book_added_day'] ?>
                                </td>
                                <?php if ($book['broken'] == '0'): ?>
                                    <td>
                                        <a href="book.php?book_stt=<?= $book['book_stt'] ?>&broken=1" class='btn btn-warning'
                                            id='btnBroken'>Tắt</a>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <a href="book.php?book_stt=<?= $book['book_stt'] ?>&broken=0" class='btn btn-success'
                                            id='btnBroken'>Mở</a>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <a href="book.php?delete_book_stt=<?= $book['book_stt'] ?>" class='btn btn-danger'
                                        id='btnDelete'>Delete</a>
                                </td>
                            <tr>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script>
        //Broken
        $("a#btnBroken").click(function () {
            if (confirm('Bạn chắc chắn muốn tắt quyển sách này không?')) {
                return true;
            }
            return false;
        });
        //Delete
        $("a#btnDelete").click(function () {
            if (confirm('Bạn chắc chắn muốn xóa quyển sách này không?')) {
                return true;
            }
            return false;
        });
    </script>
</body>

</html>