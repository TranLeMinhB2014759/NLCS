<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
if (isset($_POST['book_stt'])) {
    $query_book = 'UPDATE quyensach SET book_status=:book_status WHERE book_stt=:book_stt';
    $stmt_b = $db->prepare($query_book);
    $stmt_b->bindParam(':book_status', $_POST['book_status']);
    $stmt_b->bindParam(':book_stt', $_POST['book_stt']);
    $stmt_b->execute();

    $query_pm = 'UPDATE phieumuon SET trangthai=:trangthai WHERE pm_stt=:pm_stt';
    $stmt_p = $db->prepare($query_pm);
    $stmt_p->bindParam(':trangthai', $_POST['trangthai']);
    $stmt_p->bindParam(':pm_stt', $_POST['pm_stt']);
    $stmt_p->execute();
}
// Lấy số lượng bản ghi trong cơ sở dữ liệu
$query_page = "SELECT COUNT(*) as total FROM phieumuon";
$result = $db->query($query_page);
$row_page = $result->fetch(PDO::FETCH_ASSOC);
$totalRecords = $row_page['total'];

// Số bản ghi hiển thị trên mỗi trang
$recordsPerPage = 5;

// Tính toán số trang
$totalPages = ceil($totalRecords / $recordsPerPage);

// Xác định trang hiện tại và kiểm tra giá trị
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, min($_GET['page'], $totalPages)) : 1;

// Xác định phạm vi hiển thị các trang
$range = 5; // Số trang hiển thị

$startRange = max(1, $currentPage - floor($range / 2));
$endRange = min($totalPages, $startRange + $range - 1);

$startFrom = ($currentPage - 1) * $recordsPerPage;
$query_s_e = "SELECT * FROM phieumuon LIMIT $startFrom, $recordsPerPage";
$result = $db->query($query_s_e);

$startFrom = ($currentPage - 1) * $recordsPerPage;

//Lấy dữ liệu phiếu mượn
$data = [];
if (isset($_POST['submit'])) {
    $keyword = $_POST['keyword'];
    if (!empty($keyword)) {
        $query = $db->prepare("SELECT * FROM phieumuon pm 
                               INNER join quyensach qs on pm.book_stt = qs.book_stt
                               INNER join dausach ds on ds.title_id = qs.title_id
                               INNER join user u on u.user_id = pm.user_id 
                               WHERE pm.pm_stt = :keyword");
        $query->bindValue(':keyword', $keyword);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT * FROM phieumuon pm 
                               INNER join quyensach qs on pm.book_stt = qs.book_stt
                               INNER join dausach ds on ds.title_id = qs.title_id
                               INNER join user u on u.user_id = pm.user_id LIMIT $startFrom, $recordsPerPage");
        $query->execute();
    }
} else {
    $query = $db->prepare("SELECT * FROM phieumuon pm 
                           INNER join quyensach qs on pm.book_stt = qs.book_stt
                           INNER join dausach ds on ds.title_id = qs.title_id
                           INNER join user u on u.user_id = pm.user_id LIMIT $startFrom, $recordsPerPage");
    $query->execute();
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $data[] = array(
        'pm_stt' => $row['pm_stt'],
        'user_id' => $row['user_id'],
        'fullname' => $row['fullname'],
        'class' => $row['class'],
        'course' => $row['course'],
        'sdt' => $row['sdt'],
        'email' => $row['email'],
        'title_name' => $row['title_name'],
        'title_author' => $row['title_author'],
        'pm_ngaymuon' => $row['pm_ngaymuon'],
        'pm_ngayhentra' => $row['pm_ngayhentra'],
        'trangthai' => $row['trangthai'],
        'book_stt' => $row['book_stt'],
        'book_status' => $row['book_status'],
    );
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
    <div class="title">
        QUẢN LÝ PHIẾU MƯỢN SÁCH
    </div>
    <div class="container form-search" style="padding: 0 60px;">
        <form method="POST">
            <div class="search input-group mb-3 mt-3">
                <input type="text" class="form-control" placeholder="Nhập vào số phiếu mượn..." id="keyword"
                    name="keyword">
                <button class="btn btn-primary" type="submit" name="submit"><i
                        class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>
    <?php if ($query->rowCount() > 0): ?>
        <div class="container-m">
            <table>
                <tr>
                    <th>Số thứ tự</th>
                    <th>Người mượn</th>
                    <th>Tên quyển sách</th>
                    <th>Tác giả</th>
                    <th>Ngày mượn</th>
                    <th>Ngày hẹn trả</th>
                    <th>Trạng thái</th>
                    <th>Hủy</th>
                </tr>
                <?php foreach ($data as $pm): ?>
                    <tr>
                        <td>
                            <?= $pm['pm_stt'] ?>
                        </td>
                        <td>
                            <a href="#" onclick="check_user('<?= $pm['user_id'] ?>',
                                                        '<?= $pm['fullname'] ?>',
                                                        '<?= $pm['class'] ?>',
                                                        '<?= $pm['course'] ?>',
                                                        '0<?= $pm['sdt'] ?>',
                                                        '<?= $pm['email'] ?>',)">
                                <?= $pm['fullname'] ?>
                            </a>

                        </td>
                        <td>
                            <?= $pm['title_name'] ?>
                        </td>
                        <td>
                            <?= $pm['title_author'] ?>
                        </td>
                        <td>
                            <?= $pm['pm_ngaymuon'] ?>
                        </td>
                        <td>
                            <?= $pm['pm_ngayhentra'] ?>
                        </td>
                        <?php if ($pm['trangthai'] == 0): ?>
                            <td>
                                <form action="manage_callcard.php" method="POST">
                                    <input id="book_status" name="book_status" hidden value="0"></input>
                                    <input id="book_stt" name="book_stt" hidden value="<?= $pm['book_stt'] ?>"></input>
                                    <input id="trangthai" name="trangthai" hidden value="1"></input>
                                    <input id="pm_stt" name="pm_stt" hidden value="<?= $pm['pm_stt'] ?>"></input>
                                    <button type="submit" id="confirm" class="btn btn-primary">Xác nhận</button>
                                </form>
                            </td>
                            <td>
                                <form action="manage_callcard.php" method="POST">
                                    <input id="book_status" name="book_status" hidden value="1"></input>
                                    <input id="book_stt" name="book_stt" hidden value="<?= $pm['book_stt'] ?>"></input>
                                    <input id="trangthai" name="trangthai" hidden value="3"></input>
                                    <input id="pm_stt" name="pm_stt" hidden value="<?= $pm['pm_stt'] ?>"></input>
                                    <button type="submit" id="cancel" class="btn btn-danger">Hủy</button>
                                </form>
                            </td>

                        <?php elseif ($pm['trangthai'] == 1): ?>
                            <td colspan="2">
                                <form action="manage_callcard.php" method="POST">
                                    <input id="book_status" name="book_status" hidden value="1"></input>
                                    <input id="book_stt" name="book_stt" hidden value="<?= $pm['book_stt'] ?>"></input>
                                    <input id="trangthai" name="trangthai" hidden value="2"></input>
                                    <input id="pm_stt" name="pm_stt" hidden value="<?= $pm['pm_stt'] ?>"></input>
                                    <button type="submit" id="giveback" class="btn btn-success">Xác nhận trả</button>
                                </form>
                            </td>
                        <?php elseif ($pm['trangthai'] == 2): ?>
                            <td colspan="2" style="color: green">Đã trả</td>
                        <?php elseif ($pm['trangthai'] == 3): ?>
                            <td colspan="2" style="color: red">Đã hủy</td>
                        <?php endif; ?>
                    <tr>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
    <?php else:
        echo "<div class='no-result'>Mã số phiếu mượn không tồn tại</div>"; ?>
    <?php endif; ?>
    <?php
    echo '<ul class="pagination ';
    if (isset($_POST['submit']) && !empty($keyword)) {
        echo 'disabled-ul';
    }
    echo '">';
    if ($currentPage > 1) {
        echo '<li class="page-item"><a class="page-link" href="?page=1"><i class="fa-solid fa-angles-left"></i></a></li>';
        echo '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage - 1) . '"><i class="fa-solid fa-angle-left"></i></a></li>';
    } else {
        echo '<li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-angles-left"></i></span></li>';
        echo '<li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-angle-left"></i></span></li>';
    }

    // Hiển thị các trang trong phạm vi
    for ($page = $startRange; $page <= $endRange; $page++) {
        echo '<li class="page-item';
        if ($page == $currentPage) {
            echo ' active';
        }
        echo '"><a class="page-link" href="?page=' . $page . '">' . $page . '</a></li>';
    }

    if ($currentPage < $totalPages) {
        echo '<li class="page-item"><a class="page-link" href="?page=' . ($currentPage + 1) . '"><i class="fa-solid fa-angle-right"></i></a></li>';
        echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '"><i class="fa-solid fa-angles-right"></i></a></li>';
    } else {
        echo '<li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-angle-right"></i></span></li>';
        echo '<li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-angles-right"></i></span></li>';
    }
    echo '</ul>';
    ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top"><img src="image/toTop.png" alt=""></button>
    <!--===============================================================================================-->
    <!-- <script type="text/javascript" src="js/index.js"></script> -->
    <!--===============================================================================================-->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <!--===============================================================================================-->
    <script>
        //Duyêt
        $("#confirm").click(function () {
            if (confirm('Bạn bạn chấp nhận phiếu mượn này?')) {
                alert("Xác nhận thành công");
                return true;
            }
            return false;
        });
        //Xác nhận trả
        $("#giveback").click(function () {
            if (confirm('Bạn xác nhận người mượn đã trả sách?')) {
                alert("Xác nhận thành công");
                return true;
            }
            return false;
        });
        //Hủy
        $("#cancel").click(function () {
            if (confirm('Bạn chắc chắn muốn hủy yêu cầu này không?')) {
                alert("Đã hủy thành công");
                return true;
            }
            return false;
        });

        // Button SrolltoTop
        window.onscroll = function () { scrollFunction() };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 300) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }

        //Xem thông tin người mượn
        function check_user(value1, value2, value3, value4, value5, value6) {
            var overlay = document.createElement("div");
            overlay.classList.add("overlay");

            var modal_user = document.createElement("div");
            modal_user.classList.add("modal_user");
            modal_user.innerHTML = "<div><h2>Thông tin</h2></div><p>"
                + "<div><strong>ID: " + value1 + "</strong></div>"
                + "<div><strong>Họ tên: " + value2 + "</strong></div>"
                + "<div><strong>Lớp: " + value3 + "</strong></div>"
                + "<div><strong>Khóa: " + value4 + "</strong></div>"
                + "<div><strong>Số điện thoại: " + value5 + "</strong></div>"
                + "<div><strong>Email: " + value6 + "</strong></div>"
                + "</p><button onclick='hideModal()'>Đóng</button>";
            overlay.appendChild(modal_user);
            document.body.appendChild(overlay);
        }

        function hideModal() {
            var overlay = document.querySelector(".overlay");
            overlay.parentNode.removeChild(overlay);
        }
    </script>
</body>

</html>