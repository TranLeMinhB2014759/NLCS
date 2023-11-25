<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
//Update trạng thái phiếu mượn và cập nhật trạng thái sách trong thư viện
if (isset($_POST['book_stt_string'])) {
    $book_stt_string = $_POST['book_stt_string'];
    $title_id_string = $_POST['title_id_string'];
    $book_status = $_POST['book_status'];
    $trangthai = $_POST['trangthai'];
    $pm_ngaymuon = $_POST['pm_ngaymuon'];
    $pm_ngayhentra = $_POST['pm_ngayhentra'];
    $pm_stt = $_POST['pm_stt'];

    // Chuyển chuỗi thành mảng
    $book_stts = explode(', ', $book_stt_string);
    $title_ids = explode(', ', $title_id_string);
    //Lấy title_id làm key và update theo book_stt
    foreach ($title_ids as $key => $title_id) {
        $book_stt = $book_stts[$key];
        $query_book = "UPDATE quyensach SET book_status = :book_status WHERE book_stt = :book_stt AND title_id = :title_id";
        $stmt_b = $db->prepare($query_book);
        $stmt_b->bindParam(':book_status', $book_status);
        $stmt_b->bindParam(':book_stt', $book_stt);
        $stmt_b->bindParam(':title_id', $title_id);
        $stmt_b->execute();
    }

    //Update trạng thái phiếu mượn
    $query_pm_update = "UPDATE phieumuon SET trangthai=:trangthai, pm_ngaymuon=:pm_ngaymuon, pm_ngayhentra=:pm_ngayhentra WHERE pm_stt=:pm_stt";
    $stmt_p = $db->prepare($query_pm_update);
    $stmt_p->bindParam(':trangthai', $trangthai);
    $stmt_p->bindParam(':pm_ngaymuon', $pm_ngaymuon);
    $stmt_p->bindParam(':pm_ngayhentra', $pm_ngayhentra);
    $stmt_p->bindParam(':pm_stt', $pm_stt);
    $stmt_p->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
}

//--===============================================================================================--//
//Pagination
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

//--===============================================================================================--//

//Lấy dữ liệu phiếu mượn
$data = [];
if (isset($_POST['submit'])) {
    $keyword = $_POST['keyword'];
    if (!empty($keyword)) {
        $query = $db->prepare("SELECT * FROM phieumuon pm 
                               INNER join quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt
                               INNER join dausach ds on ds.title_id = qs.title_id
                               INNER join user u on u.user_id = pm.user_id 
                               WHERE pm.pm_stt = :keyword ORDER BY pm.pm_stt DESC");
        $query->bindValue(':keyword', $keyword);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT * FROM phieumuon pm 
                               INNER join quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt
                               INNER join dausach ds on ds.title_id = qs.title_id
                               INNER join user u on u.user_id = pm.user_id ORDER BY pm.pm_stt DESC LIMIT $startFrom, $recordsPerPage");
        $query->execute();
    }
} elseif (isset($_GET['phanloai'])) {
    $phanloai = $_GET['phanloai'];
    if ($phanloai != "all") {
        $query = $db->prepare("SELECT * FROM phieumuon pm 
                            INNER join quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt
                            INNER join dausach ds on ds.title_id = qs.title_id
                            INNER join user u on u.user_id = pm.user_id
                            WHERE pm.trangthai = :phanloai
                            ORDER BY pm.pm_stt ASC");
        $query->bindValue(':phanloai', $phanloai);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT * FROM phieumuon pm 
                           INNER join quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt
                           INNER join dausach ds on ds.title_id = qs.title_id
                           INNER join user u on u.user_id = pm.user_id ORDER BY pm.trangthai, pm.pm_stt DESC LIMIT $startFrom, $recordsPerPage");
        $query->execute();
    }
} else {
    $query = $db->prepare("SELECT * FROM phieumuon pm 
                           INNER join quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt
                           INNER join dausach ds on ds.title_id = qs.title_id
                           INNER join user u on u.user_id = pm.user_id ORDER BY pm.trangthai, pm.pm_stt DESC LIMIT $startFrom, $recordsPerPage");
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
        'role' => $row['role'],
        'pm_ngaymuon' => $row['pm_ngaymuon'],
        'pm_ngayhentra' => $row['pm_ngayhentra'],
        'trangthai' => $row['trangthai'],
        'book_stt' => $row['book_stt'],
        'title_id' => $row['title_id'],
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
    <title>Call Card</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="manage_titles.php">
                            <i class="fa-solid fa-book"></i>
                            <p>Manage Titles</p>
                        </a>
                    </li>
                    <li class="nav-item active">
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
                <h3 class="title-comm"><span class="title-holder">QUẢN LÝ PHIẾU MƯỢN SÁCH</span></h3>
                <div class="container form-search row" style="padding: 0 60px;">
                    <div class="col-3">
                        <form>
                            <div class="search input-group mb-3 mt-3">
                                <select class="form-select" name="phanloai" id="phanloai" onchange="this.form.submit()">
                                    <option value="all">Phân loại theo</option>
                                    <option <?php if (isset($_GET['phanloai']) && $_GET['phanloai'] == "all") {
                                        echo "selected";
                                    } ?> value="all">Tất cả</option>
                                    <option <?php if (isset($_GET['phanloai']) && $_GET['phanloai'] == "0") {
                                        echo "selected";
                                    } ?> value="0">Chờ xử lý</option>
                                    <option <?php if (isset($_GET['phanloai']) && $_GET['phanloai'] == "1") {
                                        echo "selected";
                                    } ?> value="1">Đang mượn</option>
                                    <option <?php if (isset($_GET['phanloai']) && $_GET['phanloai'] == "2") {
                                        echo "selected";
                                    } ?> value="2">Đã trả</option>
                                    <option <?php if (isset($_GET['phanloai']) && $_GET['phanloai'] == "3") {
                                        echo "selected";
                                    } ?> value="3">Đã hủy</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-5"></div>
                    <div class="col-4">
                        <form method="POST">
                            <div class="search input-group mb-3 mt-3">
                                <input type="text" class="form-control" placeholder="Nhập vào số phiếu mượn..."
                                    id="keyword" name="keyword" autocomplete="off">
                                <button class="btn btn-primary" type="submit" name="submit"><i
                                        class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($query->rowCount() > 0): ?>
                    <div class="container-m">
                        <table>
                            <thead>
                                <tr>
                                    <th>Số thứ tự</th>
                                    <th>Người mượn</th>
                                    <th>Ngày mượn</th>
                                    <th>Ngày trả</th>
                                    <th>Danh sách mượn</th>
                                    <th>Trạng thái</th>
                                    <th>Hủy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $pm): ?>
                                    <tr>
                                        <td>
                                            <?= $pm['pm_stt'] ?>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-light" onclick="check_user('<?= $pm['user_id'] ?>',
                                                                    '<?= $pm['fullname'] ?>',
                                                                    '<?= $pm['class'] ?>',
                                                                    '<?= $pm['course'] ?>',
                                                                    '0<?= $pm['sdt'] ?>',
                                                                    '<?= $pm['email'] ?>',)">
                                                <?= $pm['fullname'] ?>
                                            </a>

                                        </td>
                                        <td>
                                            <?= $pm['pm_ngaymuon'] ?>
                                        </td>
                                        <td>
                                            <?= $pm['pm_ngayhentra'] ?>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-light" onclick="check_list_book(<?= $pm['pm_stt'] ?>)">
                                                <i class="fa-solid fa-eye"></i> Xem chi tiết
                                            </a>
                                        </td>
                                        <?php
                                        // Lấy danh sách các quyển sách
                                        $query_pm = $db->prepare('SELECT * FROM phieumuon WHERE pm_stt =:pm_stt');
                                        $query_pm->bindValue(':pm_stt', $pm["pm_stt"]);
                                        $query_pm->execute();
                                        $results_pm = $query_pm->fetchAll();
                                        $book_stt_array = array();
                                        $title_id_array = array(); // Khởi tạo một mảng rỗng
                                
                                        foreach ($results_pm as $row) {
                                            $book_stt_array[] = $row['book_stt'];
                                            $title_id_array[] = $row['title_id']; // Thêm giá trị của book_stt vào mảng
                                        }
                                        $book_stt_string = implode(", ", $book_stt_array);
                                        $title_id_string = implode(", ", $title_id_array); // Ghép các giá trị thành chuỗi
                                        ?>
                                        <?php if ($pm['trangthai'] == 0): ?>
                                            <td>

                                                <form action="manage_callcard.php" method="POST">
                                                    <input id="book_status" name="book_status" hidden value="0"></input>
                                                    <input id="book_stt_string" name="book_stt_string" hidden
                                                        value="<?= $book_stt_string ?>"></input>
                                                    <input id="title_id_string" name="title_id_string" hidden
                                                        value="<?= $title_id_string ?>"></input>
                                                    <input id="trangthai" name="trangthai" hidden value="1"></input>
                                                    <input id="pm_ngaymuon" name="pm_ngaymuon" hidden
                                                        value="<?= date('d-m-Y') ?>"></input>
                                                    <?php if ($pm['role'] == "student"): ?>
                                                        <input id="pm_ngayhentra" name="pm_ngayhentra" hidden
                                                            value="<?= date('d-m-Y', strtotime("+14 days")) ?>"></input>
                                                    <?php elseif ($pm['role'] == "teacher"): ?>
                                                        <input id="pm_ngayhentra" name="pm_ngayhentra" hidden
                                                            value="<?= date('d-m-Y', strtotime("+56 days")) ?>"></input>
                                                    <?php endif; ?>
                                                    <input id="pm_stt" name="pm_stt" hidden value="<?= $pm['pm_stt'] ?>"></input>
                                                    <button type="submit" id="confirm" class="btn btn-primary">Xác nhận</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="manage_callcard.php" method="POST">
                                                    <input id="book_status" name="book_status" hidden value="1"></input>
                                                    <input id="book_stt_string" name="book_stt_string" hidden
                                                        value="<?= $book_stt_string ?>"></input>
                                                    <input id="title_id_string" name="title_id_string" hidden
                                                        value="<?= $title_id_string ?>"></input>
                                                    <input id="trangthai" name="trangthai" hidden value="3"></input>
                                                    <input id="pm_ngaymuon" name="pm_ngaymuon" hidden value=""></input>
                                                    <input id="pm_ngayhentra" name="pm_ngayhentra" hidden value=""></input>
                                                    <input id="pm_stt" name="pm_stt" hidden value="<?= $pm['pm_stt'] ?>"></input>
                                                    <button type="submit" id="cancel" class="btn btn-danger">Hủy</button>
                                                </form>
                                            </td>

                                        <?php elseif ($pm['trangthai'] == 1): ?>
                                            <td colspan="2">
                                                <form action="manage_callcard.php" method="POST">
                                                    <input id="book_status" name="book_status" hidden value="1"></input>
                                                    <input id="book_stt_string" name="book_stt_string" hidden
                                                        value="<?= $book_stt_string ?>"></input>
                                                    <input id="title_id_string" name="title_id_string" hidden
                                                        value="<?= $title_id_string ?>"></input>
                                                    <input id="trangthai" name="trangthai" hidden value="2"></input>
                                                    <input id="pm_ngaymuon" name="pm_ngaymuon" hidden
                                                        value="<?= $pm['pm_ngaymuon'] ?>"></input>
                                                    <input id="pm_ngayhentra" name="pm_ngayhentra" hidden
                                                        value="<?= date('d-m-Y') ?>"></input>
                                                    <input id="pm_stt" name="pm_stt" hidden value="<?= $pm['pm_stt'] ?>"></input>
                                                    <button type="submit" id="giveback" class="btn btn-success">Xác nhận
                                                        trả</button>
                                                </form>
                                            </td>
                                        <?php elseif ($pm['trangthai'] == 2): ?>
                                            <td colspan="2" style="color: green">Đã trả</td>
                                        <?php elseif ($pm['trangthai'] == 3): ?>
                                            <td colspan="2" style="color: red">Đã hủy</td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else:
                    echo "<div class='no-result'>Mã số phiếu mượn không tồn tại</div>"; ?>
                <?php endif; ?>
                <?php
                echo '<ul class="pagination ';
                if (!isset($_GET['phanloai'])) {
                    if (isset($_POST['submit']) && !empty($keyword)) {
                        echo 'disabled-ul';
                    } else {
                        echo '';
                    }
                } else {
                    if ((isset($_POST['submit']) && empty($keyword)) || $phanloai == "all") {
                        echo '';
                    } else {
                        echo 'disabled-ul';
                    }
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
            </div>
            <button onclick="topFunction()" id="myBtn" title="Go to top"><img src="image/toTop.png" alt=""></button>
        </div>
    </div>
    <script type="text/javascript" src="js/btnTotop.js"></script>
    <!--===============================================================================================-->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
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

        //Xem thông tin người mượn
        function check_user(value1, value2, value3, value4, value5, value6) {
            var overlay = document.createElement("div");
            overlay.classList.add("overlay");

            var modal_user = document.createElement("div");
            modal_user.classList.add("modal_user");
            modal_user.innerHTML = "<div class='text-center'><h3>Thông tin</h3></div>"
                + "<hr>"
                + "<div><strong>ID: " + value1 + "</strong></div>"
                + "<div><strong>Họ tên: " + value2 + "</strong></div>"
                + "<div><strong>Lớp: " + value3 + "</strong></div>"
                + "<div><strong>Khóa: " + value4 + "</strong></div>"
                + "<div><strong>Số điện thoại: " + value5 + "</strong></div>"
                + "<div><strong>Email: " + value6 + "</strong></div>"
                + "<hr>"
                + "</p><button class='btn btn-danger' id='hideModal' onclick='hideModal()'>Đóng</button>";
            overlay.appendChild(modal_user);
            document.body.appendChild(overlay);
        }

        async function check_list_book(value) {
            var overlay = document.createElement("div");
            overlay.classList.add("overlay");

            var modal_list_book = document.createElement("div");
            modal_list_book.classList.add("modal_list_book");
            modal_list_book.innerHTML = "<div class='text-center'><h3>Danh sách mượn</h3></div><hr>";

            // Sử dụng jQuery để gửi giá trị 'value' đến tệp PHP
            const data = await $.get('ajax.php', { value: value });
            modal_list_book.innerHTML += data;
            modal_list_book.innerHTML += "<hr><button class='btn btn-danger' id='hideModal' onclick='hideModal()'>Đóng</button>";
            overlay.appendChild(modal_list_book);
            document.body.appendChild(overlay);
        }

        function hideModal() {
            var overlay = document.querySelector(".overlay");
            overlay.parentNode.removeChild(overlay);
        }

    </script>
</body>

</html>