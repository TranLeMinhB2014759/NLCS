<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';
// Lấy thông tin filterBooks
$query_t = "SELECT * FROM dausach";
$title = $db->prepare($query_t);
$title->execute();

$data_t = [];
while ($row_id = $title->fetch(PDO::FETCH_ASSOC)) {
    $data_t[] = array(
        'title_id' => $row_id['title_id'],
        'title_name' => $row_id['title_name'],
    );
}

$query_b = "SELECT * FROM quyensach WHERE broken = '0'";
$book = $db->prepare($query_b);
$book->execute();

$data_b = [];
while ($row_stt = $book->fetch(PDO::FETCH_ASSOC)) {
    $data_b[] = array(
        'book_stt' => $row_stt['book_stt'],
        'book_status' => $row_stt['book_status'],
        'title_id' => $row_stt['title_id'],
    );
}
//--===============================================================================================--//
//Mượn sách
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    //Lấy danh sách đầu sách
    $i = 1;
    while ($i <= 5) {
        $title_id_key = "title_id_$i"; // Tạo tên khóa dựa trên $i
        if (isset($_POST[$title_id_key])) {
            $title_id_value = $_POST[$title_id_key];
            // Tiếp tục xử lý với $title_id_value
            $title_id[] = $title_id_value;
            $i++;
        } else {
            // Trường không tồn tại, thoát vòng lặp
            break;
        }
    }
    $title_id_string = implode(", ", $title_id); //Chuyển mảng thành chuỗi
    //Không được chọn trùng đầu sách
    $counts = array_count_values($title_id);
    $hasDuplicates = false;

    foreach ($counts as $value => $count) {
        if ($count > 1) {
            // Có giá trị trùng nhau, đặt biến $hasDuplicates thành true và thoát khỏi vòng lặp
            $hasDuplicates = true;
            break;
        }
    }
    
    // Không có giá trị trùng nhau
    if (!$hasDuplicates) {
    //Lấy danh sách quyển sách
    $book_stt = array();
    $i = 1;
    while ($i <= 5) {
        $book_stt_value = $_POST["book_stt_$i"];
        if ($book_stt_value == "") {
            break;
        }
        $book_stt[] = $book_stt_value;
        $i++;
    }
    $book_stt_string = implode(", ", $book_stt); //Chuyển mảng thành chuỗi

    $stmt = $db->prepare('
                INSERT INTO phieumuon (user_id, title_id, book_stt, trangthai)
                VALUES (:user_id, :title_id, :book_stt, :trangthai)
                ');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title_id', $title_id_string);
    $stmt->bindParam(':book_stt', $book_stt_string);
    $stmt->bindValue(':trangthai', "");

    $stmt->execute();
    echo '<script>
        alert("Mượn sách thành công");
    </script>';
    header("Location: " . $_SERVER['HTTP_REFERER']);

    // Chuyển chuỗi thành mảng
    $book_stts = explode(', ', $book_stt_string);
    $title_ids = explode(', ', $title_id_string);
    //Lấy title_id làm key và update theo book_stt
    foreach ($title_ids as  $key => $title_id) {
        $book_stt = $book_stts[$key];
        $query_book = "UPDATE quyensach SET book_status = '0' WHERE book_stt = :book_stt AND title_id = :title_id";
        $stmt_b = $db->prepare($query_book);
        $stmt_b->bindParam(':book_stt', $book_stt);
        $stmt_b->bindParam(':title_id', $title_id);
        $stmt_b->execute();
    }
    } else {
        // Có giá trị trùng nhau, in ra lỗi
        echo '<script>
            alert("Lỗi: Có giá trị trùng nhau trong danh sách đầu sách.");
        </script>';
    }
}
//--===============================================================================================--//
//Chờ xử lý
$query_pending = $db->prepare('SELECT * FROM phieumuon pm
                            INNER JOIN quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt 
                            INNER JOIN dausach ds on pm.title_id = ds.title_id 
                            WHERE user_id = :user_id AND trangthai = :trangthai
                            ORDER BY pm.pm_stt DESC');
$query_pending->bindValue(':user_id', $_SESSION['user']['id']);
$query_pending->bindValue(':trangthai', 0);
$query_pending->execute();
$results_pending = $query_pending->fetchAll();
$rows_w = $query_pending->rowCount();

//Đang mượn
$query_borrowing = $db->prepare('SELECT * FROM phieumuon pm 
                                INNER JOIN quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt
                                INNER JOIN dausach ds on pm.title_id = ds.title_id 
                                WHERE pm.user_id = :user_id AND pm.trangthai = :trangthai
                                ORDER BY pm.pm_stt DESC');
$query_borrowing->bindValue(':user_id', $_SESSION['user']['id']);
$query_borrowing->bindValue(':trangthai', 1);
$query_borrowing->execute();
$results_borrowing = $query_borrowing->fetchAll();
$rows_b = $query_borrowing->rowCount();

//Đã trả
$query_returned = $db->prepare('SELECT * FROM phieumuon pm 
                                INNER JOIN quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt 
                                INNER JOIN dausach ds on pm.title_id = ds.title_id 
                                WHERE pm.user_id = :user_id AND pm.trangthai = :trangthai
                                ORDER BY pm.pm_stt DESC');
$query_returned->bindValue(':user_id', $_SESSION['user']['id']);
$query_returned->bindValue(':trangthai', 2);
$query_returned->execute();
$results_returned = $query_returned->fetchAll();
$rows_r = $query_returned->rowCount();

//Đã bị hủy
$query_cancelled = $db->prepare('SELECT * FROM phieumuon pm 
                                INNER JOIN quyensach qs ON pm.title_id = qs.title_id AND pm.book_stt = qs.book_stt 
                                INNER JOIN dausach ds on pm.title_id = ds.title_id 
                                WHERE pm.user_id = :user_id AND pm.trangthai = :trangthai
                                ORDER BY pm.pm_stt DESC');
$query_cancelled->bindValue(':user_id', $_SESSION['user']['id']);
$query_cancelled->bindValue(':trangthai', 3);
$query_cancelled->execute();
$results_cancelled = $query_cancelled->fetchAll();
$rows_c = $query_cancelled->rowCount();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <!----- Bootstrap 4.6.2 ----->
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">
    <link rel="stylesheet" type="text/css" href="css/profile.css">
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-3" id="leftSide">
                <div class="group-box">
                    <div class="title text-center">Danh mục
                    </div>
                    <div class="leftMenu">
                        <ul>
                            <li id="tab1" class="active"><a href="#" onclick="active_profile()">Thông tin cá nhân</a>
                            </li>
                            <?php if ($_SESSION['user']['role'] == "student" || $_SESSION['user']['role'] == "teacher"):?>
                            <li id="tab2"><a href="#tab2" onclick="active_waiting()">Chở xử lý (
                                    <?= $rows_w ?>)
                                </a></li>
                            <li id="tab3"><a href="#tab3" onclick="active_borrow()">Sách đang mượn (
                                    <?= $rows_b ?>)
                                </a></li>
                            <li id="tab4"><a href="#tab4" onclick="active_giveback()">Sách đã trả (
                                    <?= $rows_r ?>)
                                </a></li>
                            <li id="tab5"><a href="#tab5" onclick="active_expired()">Đã bị hủy (
                                    <?= $rows_c ?>)
                                </a></li>
                            <?php endif?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mainContent col-sm-9">
                <div class="group-box min-height">
                    <div class="title text-center">Notification</div>
                    <?php if (isset($_SESSION['user']['success'])): ?>
                    <div class="alert alert-success text-center">
                        <?php echo $_SESSION['user']['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true"></button>
                    </div>
                    <?php endif ?>
                    <?php if (isset($_SESSION['user']['error'])): ?>
                    <div class="alert alert-danger text-center">
                        <strong>Error!</strong>
                        <?php echo $_SESSION['user']['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true"></button>
                    </div>
                    <?php endif ?>
                    <div class="container">
                        <br>
                        <!-- Avatar -->
                        <div class="tab1 main-content animate__animated">
                            <h3 class="text-center">Thông tin chung</h3>
                            <div class="row">
                                <div class="col"></div>
                                <div class="col media picture">
                                    <a class="btn" data-bs-toggle="modal" data-bs-target="#modal3"
                                        title="Thay đổi ảnh đại diện"><img class="image img-fluid rounded-circle"
                                            src="avatar/<?php echo $_SESSION['user']['avatar'] ?>"
                                            alt="Ảnh người dùng"></a>
                                    <div class="middle">
                                        <a class="btn" data-bs-toggle="modal" data-bs-target="#modal3"
                                            title="Thay đổi ảnh đại diện">
                                            Sửa <i class="fas fa-pen"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col"></div>
                            </div>
                            <!-- The Modal -->
                            <div class="modal" id="modal3">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h2 class="modal-title"><i class="fas fa-user-edit">&nbsp</i>Chỉnh sửa</h2>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <!-- Modal body -->
                                        <div class="modal-body">
                                            <form action="form.php" method="POST" enctype="multipart/form-data">
                                                <div class="mb-3">
                                                    <label for="file_avatar" class="form-label">
                                                        <i class="fa-regular fa-image fa-beat">&nbsp</i>Avatar:
                                                    </label>
                                                    <div class="row">
                                                        <div class="col-5" style="margin-bottom: 10px;">
                                                            <img class="image img-fluid rounded-circle"
                                                                src="avatar/<?php echo $_SESSION['user']['avatar'] ?>"
                                                                alt="Ảnh người dùng">
                                                        </div>
                                                        <div class="col-2" style="margin: auto"><i
                                                                class="fa-sharp fa-solid fa-circle-chevron-right fa-2xl"></i>
                                                        </div>
                                                        <div class="col-1"></div>
                                                        <div class="col-4" style="left: 60px">
                                                            <img class="image image-after img-fluid rounded-circle">
                                                        </div>
                                                    </div>
                                                    <input type="file" class="form-control-file" id="file_avatar"
                                                        name="file_avatar"
                                                        accept="image/png, image/jpeg, image/gif, image/tiff">
                                                </div>
                                        </div>
                                        <!-- Modal footer -->
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" value="Avatar"
                                                name="btnAvatar">
                                                OK
                                            </button>
                                            </form>
                                            <button class="btn btn-danger" data-bs-dismiss="modal">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Người dùng -->
                            <div>
                                <div style="text-align: center; margin-left: 42px">
                                    <?php echo $_SESSION['user']['name'] ?>
                                    <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#modal0"
                                        title="Đổi tên của bạn">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>

                                <!-- The Modal -->
                                <div class="modal" id="modal0">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h2 class="modal-title"><i class="fas fa-user-edit">&nbsp</i>Chỉnh sửa
                                                </h2>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <form action="form.php" id="signupForm0" class="form-horizontal"
                                                    method="POST">
                                                    <div class="mb-3">
                                                        <label for="fullname" class="form-label">
                                                            <i class="fa-solid fa-user fa-beat">&nbsp</i>Your Name:
                                                        </label>
                                                        <input class="form-control" placeholder="Nhập tên người dùng"
                                                            id="fullname" name="fullname"
                                                            value="<?php echo $_SESSION['user']['name'] ?>">
                                                        </input>
                                                    </div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button id="btnuser" type="submit" name="btnFullname" class="btn btn-primary">
                                                    OK
                                                </button>
                                                </form>
                                                <button class="btn btn-danger" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username" class="form-label">Username:</label>
                                <?php echo $_SESSION['user']['username'] ?>
                            </div>

                            <?php if ($_SESSION['user']['role'] == "student"): ?>
                            <!-- Class -->
                            <div>
                                <label for="class" class="form-label">Class:</label>
                                <?php if ($_SESSION['user']['class'] == "") {
                                    echo "Thông tin chưa được cập nhật";
                                } else {
                                    echo $_SESSION['user']['class'];
                                }
                                ?>
                            </div>

                            <!-- Course -->
                            <div>
                                <label for="course" class="form-label">Course:</label>
                                <?php if ($_SESSION['user']['course'] == "") {
                                    echo "Thông tin chưa được cập nhật";
                                } else {
                                    echo $_SESSION['user']['course'];
                                }
                                ?>
                            </div>
                            <?php endif?>

                            <!-- Số điện thoại -->
                            <div>
                                <label for="sdt" class="form-label">Phone Number: </label>
                                <?php if ($_SESSION['user']['sdt'] == "") {
                                    echo "Hãy cập nhật số điện thoại";
                                } else {
                                    echo "(+84) ";
                                    echo $_SESSION['user']['sdt'];
                                }
                                ?>
                                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#modal1"
                                    title="Đổi số điện thoại">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- The Modal -->
                                <div class="modal" id="modal1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h2 class="modal-title"><i class="fas fa-user-edit">&nbsp</i>Chỉnh sửa
                                                </h2>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <form action="form.php" id="signupForm1" class="form-horizontal"
                                                    method="POST">
                                                    <label for="sdt" class="form-label">
                                                        <i class="fa-solid fa-phone fa-shake">&nbsp</i>Phone Number:
                                                    </label>
                                                    <div class="mb-3 input-group">
                                                        <span class="input-group-text">(+84)</span>
                                                        <input class="form-control" placeholder="Nhập vào số điện thoại"
                                                            name="sdt" value="<?php echo $_SESSION['user']['sdt'] ?>">
                                                        </input>
                                                    </div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="submit" name="btnSdt" class="btn btn-primary">
                                                    OK
                                                </button>
                                                </form>
                                                <button class="btn btn-danger" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="form-label">Email: </label>
                                <?php echo $_SESSION['user']['email'];?>
                                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#modal2"
                                    title="Đổi hộp thư điện tử">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- The Modal -->
                                <div class="modal fade" id="modal2" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h2 class="modal-title"><i class="fas fa-user-edit">&nbsp</i>Chỉnh sửa
                                                </h2>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <form action="form.php" id="signupForm2" class="form-horizontal"
                                                    method="POST">
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">
                                                            <i class="fa-solid fa-envelope fa-bounce">&nbsp</i>Email:
                                                        </label>
                                                        <input type="email" class="form-control"
                                                            placeholder="Nhập vào email" id="email" name="email"
                                                            value="<?php echo $_SESSION['user']['email'] ?>" autocomplete="off"></input>
                                                    </div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="submit" name="btnEmail" class="btn btn-primary">
                                                    OK
                                                </button>
                                                </form>
                                                <button class="btn btn-danger" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-7"><a href="password.php" title="Đổi mật khẩu">Change your password?</a>
                                </div>
                                <div class="col-5"><a href="../delete.php?id=<?php echo $_SESSION['user']['id'] ?>"
                                        id="delete" title="Xóa tài khoản">Delete your Account?</a></div>
                            </div>
                        </div>
                        <?php if ($_SESSION['user']['role'] == "student" || $_SESSION['user']['role'] == "teacher"):?>
                        <!-- Phiếu mượn sách -->
                        <div class="tab2 animate__animated" style="display:none">
                            <button id="btnCallCard" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCallCard" title="Đăng kí mượn sách" style="margin-bottom: 10px;">
                                <i class="fa-solid fa-plus"></i> Mượn Sách
                                    <div class="arrow-wrapper">
                                        <div class="arrow"></div>

                                    </div>
                                </button>
                                    <!-- The Modal -->
                                    <div  class="modal fade" id="modalCallCard" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h2 class="modal-title">Phiếu mượn sách</h2>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal">
                                                </button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <form id="callCard" id="pm" class="form-horizontal" method="POST">

                                                <div class="mb-3 mt-3">
                                                    <label for="user_id" class="form-label">
                                                        Tên:
                                                    </label>
                                                    <input class="form-control" disabled value="<?php echo $_SESSION['user']['name'] ?>" autocomplete="off">
                                                    <input id="user_id" name="user_id" hidden value="<?php echo $_SESSION['user']['id'] ?>" autocomplete="off">
                                                </div>


                                                <div class="note1">* Lưu ý không được chọn cùng một loại sách</div>
                                                <div class="row">
                                                    <div class="mb-3 col-6">
                                                        <label for="title_id" class="form-label">
                                                            Tên sách
                                                        </label>
                                                        <select class="form-select" name="title_id_1" id="title_id_1" required onchange="filterBooks(1)">
                                                            <option value="">-- Chọn tên sách --</option>
                                                            <?php foreach ($data_t as $title): ?>
                                                                    <option value="<?= $title['title_id']?>"><?= $title['title_name']?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3 col-6">
                                                        <label for="book_stt" class="form-label">
                                                            Mã số sách
                                                        </label>
                                                        </select>
                                                        <select class="form-select" name="book_stt_1" id="book_stt_1">
                                                            <option value="" disabled>-- Chọn mã số sách --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row input_add">
                                                    <a href="#" id="addNewRow" class="btn btn-primary"> <i class="fa-solid fa-plus"></i> Thêm dòng mới </a>
                                                </div>
                                                <div class="note2">* Số sách mượn hiện tại đã đạt mức tối đa</div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary" id="submit-callcard">
                                                    OK
                                                </button>
                                                </form>
                                                <button class="btn btn-danger" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            //Chờ xử lý
                            $count_pend = 0;
                            if ($rows_w != 0) {
                                echo '<div class="row">';
                                foreach ($results_pending as $r_p) {
                                    echo '
                                        <div class="col-12 col-md-6 phieumuon">
                                            <h4 class="sophieu text-center">' . "<b>Số phiếu: </b>" . htmlspecialchars($r_p["pm_stt"]) . '</h4>';
                                                $query_pending_list = $db->prepare('SELECT * FROM phieumuon WHERE trangthai = :trangthai AND pm_stt =:pm_stt');
                                                $query_pending_list->bindValue(':pm_stt', $r_p["pm_stt"]);
                                                $query_pending_list->bindValue(':trangthai', 0);
                                                $query_pending_list->execute();
                                                $results_pending_list = $query_pending_list->fetchAll();
                                                $results_count_p = 0;
                                                foreach ($results_pending_list as $pending_list) {
                                                    $book_stt_pending_list = explode(", ", $pending_list['book_stt']);
                                                    $title_id_pending_list = explode(", ", $pending_list['title_id']);
                                        echo '<h4><b>Danh sách mượn: </b></h4>';
                                        echo '<h4>';
                                                    foreach ( $title_id_pending_list as $key => $value_title_id_pending) {
                                                        $value_book_stt_pending = $book_stt_pending_list[$key];
                                                        echo "<a href='http://nlcs.localhost/title_detail.php?title_id=" . $value_title_id_pending . "' target='_blank'>CNTT." . $value_title_id_pending . str_pad($value_book_stt_pending, 3, '0', STR_PAD_LEFT) . "</a>" . " &emsp; ";
                                                        $results_count_p++;
                                                    }
                                        echo '</h4>';
                                                }
                                                $count_pend += $results_count_p;
                                    echo '
                                            <h4><b>Trạng thái: </b style="font-weight: bold;"><span>Chờ xử lý</span></h4>
                                        </div>';
                                }
                                echo '   </div>';
                            } else {
                                echo '<div class="text-center">Không có thông tin</div>';
                            }
                            ?>
                        </div>

                        <?php
                        //Đang mượn
                            $count_borrow = 0;
                            if ($rows_b != 0) {
                                echo '<div class="tab3 animate__animated" style="display:none">
                                    <div class="row">';
                                foreach ($results_borrowing as $r_b) {
                                    echo '
                                        <div class="col-12 col-md-6 phieumuon">
                                            <h4 class="sophieu text-center">' . "<b>Số phiếu: </b>" . htmlspecialchars($r_b["pm_stt"]) . '</h4>';
                                                $query_borrowing_list = $db->prepare('SELECT * FROM phieumuon WHERE trangthai = :trangthai AND pm_stt =:pm_stt');
                                                $query_borrowing_list->bindValue(':pm_stt', $r_b["pm_stt"]);
                                                $query_borrowing_list->bindValue(':trangthai', 1);
                                                $query_borrowing_list->execute();
                                                $results_borrowing_list = $query_borrowing_list->fetchAll();
                                                $results_count_b = 0;
                                                foreach ($results_borrowing_list as $borrowing_list) {
                                                    $book_stt_borrowing_list = explode(", ", $borrowing_list['book_stt']);
                                                    $title_id_borrowing_list = explode(", ", $borrowing_list['title_id']);
                                                    echo '<h4><b>Danh sách mượn: </b></h4>';
                                                    echo '<h4>';
                                                        foreach ($book_stt_borrowing_list as $key => $value_book_stt_borrowing) {
                                                            $value_title_id_borrowing = $title_id_borrowing_list[$key];
                                                            echo "<a href='http://nlcs.localhost/title_detail.php?title_id=" . $value_title_id_borrowing . "' target='_blank'>CNTT." . $value_title_id_borrowing . str_pad($value_book_stt_borrowing, 3, '0', STR_PAD_LEFT) . "</a>" . " &emsp; ";
                                                            $results_count_b++;
                                                        }
                                                        echo '</h4>';
                                                }
                                            $count_borrow += $results_count_b;
                                    echo '
                                            <h4>' . "<b>Ngày mượn: </b>" . htmlspecialchars($r_b["pm_ngaymuon"]) . '</h4>
                                            <h4>' . "<b>Ngày hẹn trả: </b>" . htmlspecialchars($r_b["pm_ngayhentra"]) . '</h4>';
                                    if(strtotime($r_b["pm_ngayhentra"]) >= strtotime(date('d-m-Y'))){
                                        echo'<h4><b>Trạng thái: </b><span style="color:green; font-weight: bold">Đang mượn</span></h4>';
                                    }else{
                                        echo'<h4><b>Trạng thái: </b><span style="color:red; font-weight: bold">Quá hạn</span></h4>';
                                    }
                                    echo '</div>';
                                }
                            echo '   </div>
                                    </div>';
                        } else {
                            echo '<div class="tab3 text-center animate__animated" style="display:none">Không có thông tin</div>';
                        }

                        //Đã trả
                            if ($rows_r != 0) {
                                echo '<div class="tab4 animate__animated" style="display:none">
                                                <div class="row">';
                                foreach ($results_returned as $r_r) {
                                    echo '
                                        <div class="col-12 col-md-6 phieumuon">
                                            <h4 class="sophieu text-center">' . "<b>Số phiếu: </b>" . htmlspecialchars($r_r["pm_stt"]) . '</h4>';
                                                $query_returned_list = $db->prepare('SELECT * FROM phieumuon WHERE trangthai = :trangthai AND pm_stt =:pm_stt');
                                                $query_returned_list->bindValue(':pm_stt', $r_r["pm_stt"]);
                                                $query_returned_list->bindValue(':trangthai', 2);
                                                $query_returned_list->execute();
                                                $results_returned_list = $query_returned_list->fetchAll();
                                                foreach ($results_returned_list as $returned_list) {
                                                    $book_stt_returned_list = explode(", ", $returned_list['book_stt']);
                                                    $title_id_returned_list = explode(", ", $returned_list['title_id']);
                                                    echo '<h4><b>Danh sách mượn: </b></h4>';
                                                    echo '<h4>';
                                                    foreach ($book_stt_returned_list as $key => $value_book_stt_returned) {
                                                        $value_title_id_returned = $title_id_returned_list[$key];
                                                        echo "<a href='http://nlcs.localhost/title_detail.php?title_id=" . $value_title_id_returned . "' target='_blank'>CNTT." . $value_title_id_returned . str_pad($value_book_stt_returned, 3, '0', STR_PAD_LEFT) . "</a>" . " &emsp; ";
                                                    }
                                                    echo '</h4>';
                                                }
                                    echo '
                                            <h4>' . "<b>Ngày mượn: </b>" . htmlspecialchars($r_r["pm_ngaymuon"]) . '</h4>
                                            <h4>' . "<b>Ngày trả: </b>" . htmlspecialchars($r_r["pm_ngayhentra"]) . '</h4>
                                            <h4><b>Trạng thái: </b><span style="color:green; font-weight: bold;">Đã trả</span></h4>
                                        </div>';
                            }
                                 echo '   </div>
                                        </div>';
                        } else {
                            echo '<div class="tab4 text-center animate__animated" style="display:none">Không có thông tin</div>';
                        }

                        //Đã bị hủy
                            if ($rows_c != 0) {
                                echo '<div class="tab5 animate__animated" style="display:none">
                                        <div class="row">';
                                foreach ($results_cancelled as $r_c) {
                                    echo '
                                        <div class="col-12 col-md-6 phieumuon">
                                            <h4 class="sophieu text-center">' . "<b>Số phiếu: </b>" . htmlspecialchars($r_c["pm_stt"]) . '</h4>';
                                            $query_cancelled_list = $db->prepare('SELECT * FROM phieumuon WHERE trangthai = :trangthai AND pm_stt =:pm_stt');
                                            $query_cancelled_list->bindValue(':pm_stt', $r_c["pm_stt"]);
                                            $query_cancelled_list->bindValue(':trangthai', 3);
                                            $query_cancelled_list->execute();
                                            $results_cancelled_list = $query_cancelled_list->fetchAll();
                                            foreach ($results_cancelled_list as $cancelled_list) {
                                                $book_stt_cancelled_list = explode(", ", $cancelled_list['book_stt']);
                                                $title_id_cancelled_list = explode(", ", $cancelled_list['title_id']);
                                                echo '<h4><b>Danh sách mượn: </b></h4>';
                                                echo '<h4>';
                                                foreach ($book_stt_cancelled_list as $key => $value_book_stt_cancelled) {
                                                    $value_title_id_cancelled = $title_id_cancelled_list[$key];
                                                    echo "<a href='http://nlcs.localhost/title_detail.php?title_id=" . $value_title_id_cancelled . "' target='_blank'>CNTT." . $value_title_id_cancelled . str_pad($value_book_stt_cancelled, 3, '0', STR_PAD_LEFT) . "</a>" . " &emsp; ";
                                                }
                                                echo '</h4>';
                                            }
                                    echo '
                                            <h4><b>Trạng thái: </b><span style="color:red; font-weight: bold">Đã bị hủy</span></h4>
                                        </div>';
                            }
                            echo '   </div>
                                            </div>';
                        } else {
                            echo '<div class="tab5 text-center animate__animated" style="display:none">Không có thông tin</div>';
                        }
                        ?>
                        <?php endif?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php include '../partials/footer.php'; ?>
    <script type="text/javascript" src="js/btnTotop.js"></script>
    <button onclick="topFunction()" id="myBtn" title="Go to top"><img src="image/toTop.png" alt=""></button>
    <!--===============================================================================================-->
    <script src="js/profile.js"></script>
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!--===============================================================================================-->
    <script type="text/javascript" src="js/jquery.validate.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#signupForm0").validate({
                rules: {
                    fullname: { required: true, minlength: 4, maxlength: 50 },
                },
                messages: {
                    fullname: {
                        required: "Bạn chưa nhập vào tên của bạn",
                        minlength: "Tên phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Tên phải có ít nhất 4 - 50 ký tự"
                    },
                },

                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-valid").removeClass("is-invalid");
                },
            });
            $("#signupForm1").validate({
                rules: {
                    sdt: { required: true, digits: true, minlength: 9, maxlength: 11 },
                },
                messages: {
                    sdt: {
                        required: "Bạn chưa nhập vào số điện thoại",
                        digits: "Số điện thoại phải là một dãy số",
                        minlength: "Số điện thoại không có thật",
                        maxlength: "Số điện thoại tối đa 11 chữ số"
                    },
                },
                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-valid").removeClass("is-invalid");
                },
            });
            $("#signupForm2").validate({
                rules: {
                    email: { required: true, minlength: 4, maxlength: 50 },
                },
                messages: {
                    email: "Email không hợp lệ",
                },
                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-valid").removeClass("is-invalid");
                },
            });
        });
    </script>
    <script>
        $('#submit-callcard').click(function () {
            if (confirm('Bạn chắc chắn về các thông tin trong phiếu của mình không?')) {
                return true;
            }
            return false;
        });
        $('#delete').click(function () {
            if (confirm('Bạn chắc chắn muốn xóa tài khoản của mình không?')) {
                return true;
            }
            return false;
        });
    </script>
    <script>
        const image = document.querySelector(".image-after")
        const input = document.querySelector("input")

        input.addEventListener("change", () => {
            image.src = URL.createObjectURL(input.files[0])
        })
    </script>
        
    <!--===============================================================================================-->
    <script>
        async function filterBooks(row) {
            var titleId = document.getElementById("title_id_" + row).value;
            var bookSelect = document.getElementById("book_stt_" + row);
            
            // Xóa các tùy chọn hiện có
            while (bookSelect.options.length > 0) {
                bookSelect.remove(0);
            }

            // Thêm tùy chọn mặc định
            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.text = "-- Chọn mã số sách --";
            bookSelect.add(defaultOption);
            
            // Lọc thông tin mã số sách theo đầu sách đã chọn
            <?php foreach ($data_b as $book): ?>
                <?php if($book['book_status'] == "1") :?>
                    if (titleId === "<?= $book['title_id']?>") {
                        var bookOption = document.createElement("option");
                        bookOption.value = "<?= $book['book_stt']?>";
                        bookOption.text = "CNTT.<?= $book['title_id'] . str_pad($book['book_stt'], 3, '0', STR_PAD_LEFT)?>";
                        bookSelect.add(bookOption);
                    }
                <?php endif; ?>
            <?php endforeach; ?>
        }
        $(document).ready(function () {
            $("#callCard").validate({
                rules: {
                    title_id_1: { digits: true },
                    title_id_2: { digits: true },
                    title_id_3: { digits: true },
                    title_id_4: { digits: true },
                    title_id_5: { digits: true },
                    book_stt_1: { digits: true },
                    book_stt_2: { digits: true },
                    book_stt_3: { digits: true },
                    book_stt_4: { digits: true },
                    book_stt_5: { digits: true },
                },
                messages: {
                    title_id_1: "Mã đầu sách không hợp lệ",
                    title_id_2: "Mã đầu sách không hợp lệ",
                    title_id_3: "Mã đầu sách không hợp lệ",
                    title_id_4: "Mã đầu sách không hợp lệ",
                    title_id_5: "Mã đầu sách không hợp lệ",
                    book_stt_1: "Mã số sách không hợp lệ",
                    book_stt_2: "Mã số sách không hợp lệ",
                    book_stt_3: "Mã số sách không hợp lệ",
                    book_stt_4: "Mã số sách không hợp lệ",
                    book_stt_5: "Mã số sách không hợp lệ",
                },
                errorElement: "div",
                errorPlacement: function (error, element) {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-valid").removeClass("is-invalid");
                },
            });
        });
    </script>
        
    <!--===============================================================================================-->
    <script>
        function toggleAddButton() {
            var addButtonRow = document.querySelector('.input_add');
            var note1 = document.querySelector('.note1');
            var note2 = document.querySelector('.note2');

            if (rowCount > maxRowCount) {
                addButtonRow.style.display = 'none';
                note1.style.display = 'none';
            } else if(rowCount === maxRowCount){
                addButtonRow.style.display = 'none';
                note2.style.display = 'block';
            }else{
                addButtonRow.style.display = 'block';
                note1.style.display = 'block';
            }
        }

        $('#addNewRow').click(function () {
            if (confirm('Bạn chắc chắn muốn thêm một dòng mới?')) {
                addNewRow()
            }
        });
        var rowCount = 1;
        var maxRowCount = <?php $totalCount = $count_pend + $count_borrow;
                                if($_SESSION['user']['role'] == "student"){$maxRowCount = 5 - $totalCount;}
                                if($_SESSION['user']['role'] == "teacher"){ if($totalCount > 5 && $totalCount <= 10){$maxRowCount = 10 - $totalCount;} else{$maxRowCount = 5;}}
                                echo $maxRowCount
                         ?>;
        if(maxRowCount <= 0){
            var buttonElement = document.getElementById("btnCallCard");
            var modalCallCard = document.getElementById("modalCallCard");
            buttonElement.disabled = true;
            modalCallCard.remove();
        }
        function addNewRow() {
            if (rowCount < maxRowCount) {
                rowCount++;
                var newRow = `
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="title_id_${rowCount}" class="form-label">
                                Mã số đầu sách
                            </label>
                            <select class="form-select" name="title_id_${rowCount}" id="title_id_${rowCount}" required onchange="filterBooks(${rowCount})">
                                <option value="">-- Chọn đầu sách --</option>
                                <?php foreach ($data_t as $title): ?>
                                    <option value="<?= $title['title_id']?>"><?= $title['title_name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3 col-6">
                            <label for="book_stt_${rowCount}" class="form-label">
                                Mã số sách
                            </label>
                            <select class="form-select" name="book_stt_${rowCount}" id="book_stt_${rowCount}" required>
                                <option value="" disabled>-- Chọn mã số sách --</option>
                            </select>
                        </div>
                    </div>
                `;
            var addButtonRow = document.querySelector('.input_add');
            addButtonRow.insertAdjacentHTML('beforebegin', newRow);
                                    
            toggleAddButton();
            } else{
                toggleAddButton();
                var note2 = document.querySelector('.note2');
                note2.style.display = 'block';
            }
            toggleAddButton();
        }
    </script>
    <script>
        console.log("URL hash: " + window.location.hash);
        if (window.location.hash === "#tab2") {
        console.log("Hash matches #tab2");
        active_waiting();
        }
    </script>
</body>
<?php
unset($_SESSION['user']['error']);
unset($_SESSION['user']['success']);
?>

</html>