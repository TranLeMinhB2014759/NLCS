<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
// Thêm dữ liệu
if (isset($_POST['submit1'])) {

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $class = $_POST['class'];
    $course = $_POST['course'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];

    $stmt = $db->prepare("SELECT username FROM user WHERE username=:username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $error = "Tên đăng nhập đã được sử dụng";
    } else {
        $stmt = $db->prepare('
				INSERT INTO user (username, password, fullname, class, course, sdt, email)
				VALUES (:username, :password, :fullname, :class, :course, :sdt, :email)
				');
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':course', $course);
        $stmt->bindParam(':sdt', $sdt);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}


// Lấy số lượng bản ghi trong cơ sở dữ liệu
$query_page = "SELECT COUNT(*) as total FROM user";
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
$query_s_e = "SELECT * FROM user LIMIT $startFrom, $recordsPerPage";
$result = $db->query($query_s_e);

$startFrom = ($currentPage - 1) * $recordsPerPage;

//Lấy dữ liệu User
$data = [];
if (isset($_POST['submit2'])) {
    $keyword = $_POST['keyword'];
    if (!empty($keyword)) {
        $query = $db->prepare("SELECT user_id, username, password, fullname, class, course, sdt, email, file_avatar FROM user WHERE user_id = :keyword");
        $query->bindValue(':keyword', $keyword);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT user_id, username, password, fullname, class, course, sdt, email, file_avatar FROM user LIMIT $startFrom, $recordsPerPage");
        $query->execute();
    }
} else {
    $query = $db->prepare("SELECT user_id, username, password, fullname, class, course, sdt, email, file_avatar FROM user LIMIT $startFrom, $recordsPerPage");
    $query->execute();
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $data[] = array(
        'user_id' => $row['user_id'],
        'username' => $row['username'],
        'password' => $row['password'],
        'fullname' => $row['fullname'],
        'class' => $row['class'],
        'course' => $row['course'],
        'sdt' => $row['sdt'],
        'email' => $row['email'],
        'file_avatar' => $row['file_avatar'],
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
    <!-- <link href="css/DataTables-1.13.6/css/datatables.min.css" rel="stylesheet"> -->
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="title">
        QUẢN LÝ TÀI KHOẢN NGƯỜI DÙNG
    </div>
    <div class="row container">
        <div class="col-6 btn-modal">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal" id="btn-modal"
                title="Thêm tài khoản mới">
                Thêm Tài Khoản Mới &nbsp<i class="fas fa-edit"></i>
            </button>
            <!-- The Modal -->
            <div class="modal" id="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h2 class="modal-title"><i class="fas fa-user-edit">&nbsp</i>Thêm tài khoản mới</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <form method="post" id="account" class="form-horizontal" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="fullname" class="form-label"><b>Your Name:</b></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname"
                                        placeholder="Enter your name">
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="username" class="form-label"><b>Username:</b></label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Enter your username">
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="password" class="form-label"><b>Password:</b></label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter your password">
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-6">
                                        <label for="class" class="form-label"><b>Class:</b></label>
                                        <input class="form-control" id="class" name="class"
                                            placeholder="Enter your class">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="course" class="form-label"><b>Course:</b></label>
                                        <input class="form-control" id="course" name="course"
                                            placeholder="Enter your course">
                                    </div>
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="sdt" class="form-label"><b>Phone Number:</b>
                                    </label>
                                    <input type="text" class="form-control" id="sdt" name="sdt"
                                        placeholder="Enter the phone number">
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="email" class="form-label"><b>Email:</b>
                                    </label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Enter the email">
                                </div>
                                <div class="row">
                                    <div class="col-2">
                                        <button type="submit" name="submit1" class="btn btn-primary btn-block">
                                            OK
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        <button type="button" class="btn btn-danger btn-block" data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                    </div>
                                    <div class="col-5"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-6 form-search" style="padding: 0 60px;">
            <form method="POST">
                <div class="search input-group mb-3 mt-3">
                    <input type="text" class="form-control" placeholder="Nhập vào ID..." id="keyword" name="keyword">
                    <button class="btn btn-primary" type="submit" name="submit2"><i
                            class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php if ($query->rowCount() > 0): ?>
        <div class="container-m">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Fullname</th>
                    <th>Class</th>
                    <th>Course</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Avatar</th>
                    <th>Sửa</th>
                    <th>Xóa</th>
                </tr>
                <?php foreach ($data as $user): ?>
                    <?php if ($user['user_id'] != 1): ?>
                        <tr>
                            <td>
                                <?= $user['user_id'] ?>
                            </td>
                            <td>
                                <?= $user['username'] ?>
                            </td>
                            <td>
                                <?= $user['password'] ?>
                            </td>
                            <td>
                                <?= $user['fullname'] ?>
                            </td>
                            <td>
                                <?= $user['class'] ?>
                            </td>
                            <td>
                                <?= $user['course'] ?>
                            </td>
                            <td>
                                <?php if ($user['sdt'] == 0): ?>
                                    <span>Chưa cập nhật</span>
                                <?php else: ?>
                                    (+84)
                                    <?= $user['sdt'] ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['email'] == 0): ?>
                                    <span>Chưa cập nhật</span>
                                <?php else: ?>
                                    <?= $user['email'] ?>
                                <?php endif; ?>
                            </td>
                            <td><img class='rounded-circle' src='avatar/<?= $user['file_avatar'] ?>'></td>
                            <td><a href="edit_user.php?id=<?= $user['user_id'] ?>" class='btn btn-warning'>Edit</a></td>
                            <td>
                                <a href="delete_user.php?id=<?= $user['user_id'] ?>" class='btn btn-danger'
                                    id='btn_delete'>Delete</a>
                            </td>
                        <tr>
                        <?php else: ?>
                            <td>
                                <?= $user['user_id'] ?>
                            </td>
                            <td colspan="10">ADMIN</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
    <?php else:
        echo "<div class='no-result'>ID người dùng không tồn tại</div>"; ?>
    <?php endif; ?>
    <?php

    echo '<ul class="pagination ';
    if (isset($_POST['submit2']) && !empty($keyword)) {
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
    <!-- <script src="js/DataTables-1.13.6/js/datatables.min.js"></script> -->
    <!--===============================================================================================-->
    <script>
        //Delete user
        $("a#btn_delete").click(function () {
            if (confirm('Bạn chắc chắn muốn xóa tài khoản của người dùng này không?')) {
                return true;
            }
            return false;
        });
    </script>
    <script>
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
    </script>
    <script type="text/javascript" src="js/jquery.validate.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#account").validate({
                rules: {
                    fullname: { required: true, minlength: 4, maxlength: 50 },
                    username: { required: true, minlength: 4, maxlength: 50 },
                    password: { required: true, minlength: 4, maxlength: 50 },
                    class: { required: true, maxlength: 8 },
                    course: { required: true, minlength: 3 },
                    sdt: { required: true, digits: true, minlength: 9, maxlength: 10 },
                    email: { required: true, minlength: 4, maxlength: 50 },
                },
                messages: {
                    fullname: {
                        required: "Bạn chưa nhập vào tên của bạn",
                        minlength: "Tên phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Tên phải có ít nhất 4 - 50 ký tự"
                    },
                    username: {
                        required: "Bạn chưa nhập vào tên đăng nhập",
                        minlength: "Tên đăng nhập phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Tên đăng nhập phải có ít nhất 4 - 50 ký tự"
                    },
                    password: {
                        required: "Bạn chưa nhập vào mật khẩu",
                        minlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự"
                    },
                    class: {
                        required: "Bạn chưa nhập vào tên lớp",
                        maxlength: "Tên lớp không có thật"
                    },
                    course: {
                        required: "Bạn chưa nhập vào tên khóa",
                        maxlength: "Tên khóa không có thật"
                    },
                    sdt: {
                        required: "Bạn chưa nhập vào số điện thoại",
                        digits: "Số điện thoại phải là một dãy số",
                        minlength: "Số điện thoại phải tử 9 chữ số",
                        maxlength: "Số điện thoại tối đa 10 chữ số"
                    },
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
</body>

</html>