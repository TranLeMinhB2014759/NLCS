<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
//Thêm dữ liệu
if (isset($_POST['submit1'])) {

    $title_name = $_POST['title_name'];
    $title_author = $_POST['title_author'];
    $title_type = $_POST['title_type'];
    $title_year = $_POST['title_year'];

    $file = $_FILES['title_img'];
    $allowType = ['image/png', 'image/jpeg', 'image/gif', 'image/tiff'];
    if (!in_array($file['type'], $allowType)) {
        die(header("Location: " . $_SERVER['HTTP_REFERER']));
    }
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["title_img"]["name"]);
    move_uploaded_file($_FILES["title_img"]["tmp_name"], $target_file);

    $stmt = $db->prepare('
                INSERT INTO dausach (title_name, title_author, title_type, title_year, title_img)
                VALUES (:title_name, :title_author, :title_type, :title_year, :title_img)
                ');
    $stmt->bindParam(':title_name', $title_name);
    $stmt->bindParam(':title_author', $title_author);
    $stmt->bindParam(':title_type', $title_type);
    $stmt->bindParam(':title_year', $title_year);
    $stmt->bindParam(':title_img', $_FILES['title_img']["name"]);
    $stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
}

// Lấy số lượng bản ghi trong cơ sở dữ liệu
$query_page = "SELECT COUNT(*) as total FROM dausach";
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
$query_s_e = "SELECT * FROM dausach LIMIT $startFrom, $recordsPerPage";
$result = $db->query($query_s_e);

$startFrom = ($currentPage - 1) * $recordsPerPage;

//Láy dữ liệu Đầu sách
$data = [];
if (isset($_POST['submit2'])) {
    $keyword = $_POST['keyword'];
    if (!empty($keyword)) {
        $query = $db->prepare("SELECT * FROM dausach WHERE title_id = :keyword");
        $query->bindValue(':keyword', $keyword);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT * FROM dausach LIMIT $startFrom, $recordsPerPage");
        $query->execute();
    }
} else {
    $query = $db->prepare("SELECT * FROM dausach LIMIT $startFrom, $recordsPerPage");
    $query->execute();
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $data[] = array(
        'title_id' => $row['title_id'],
        'title_name' => $row['title_name'],
        'title_author' => $row['title_author'],
        'title_type' => $row['title_type'],
        'title_year' => $row['title_year'],
        'title_img' => $row['title_img'],
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Card Catalog</title>
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
        QUẢN LÝ SÁCH TRONG THƯ VIỆN
    </div>
    <div class="row container">
        <div class="col-6 btn-modal">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal"
                id="btn-modal-book" title="Thêm tài khoản mới">
                Thêm Đầu Sách &nbsp<i class="fas fa-edit"></i>
            </button>
            <!-- The Modal -->
            <div class="modal" id="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h2 class="modal-title"><i class="fas fa-user-edit">&nbsp</i>Thêm đầu sách</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <form method="post" id="title" class="form-horizontal" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title_name" class="form-label"><b>Tên sách:</b></label>
                                    <input type="text" class="form-control" id="title_name" name="title_name"
                                        placeholder="Enter the title">
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-6">
                                        <label for="title_author" class="form-label"><b>Tên tác giả:</b></label>
                                        <input type="text" class="form-control" id="title_author" name="title_author"
                                            placeholder="Enter the author">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="title_type" class="form-label"><b>Thể loại:</b></label>
                                        <input type="text" class="form-control" id="title_type" name="title_type"
                                            placeholder="Enter the title type">
                                    </div>
                                </div>
                                <div class="mb-3 col-6">
                                    <label for="title_year" class="form-label"><b>Xuất bản:</b></label>
                                    <input class="form-control" id="title_year" name="title_year"
                                        placeholder="Enter the year">
                                </div>
                                <div class="mb-3 mt-3">
                                    <input type='file' name='title_img' id='title_img'
                                        accept='image/png, image/jpeg, image/gif, image/tiff' required> <br>
                                </div>
                                <div class="mb-3 row">
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
                    <input type="text" class="form-control" placeholder="Nhập vào mã đầu sách..." id="keyword"
                        name="keyword">
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
                    <th>Mã số</th>
                    <th>Tên đầu sách</th>
                    <th>Tác giả</th>
                    <th>Thể loại</th>
                    <th>Xuất bản</th>
                    <th>Ảnh</th>
                    <th>Số lượng</th>
                    <th>Sửa</th>
                    <th>Xóa</th>
                </tr>

                <?php foreach ($data as $title): ?>
                    <tr>
                        <td>
                            <?= $title['title_id'] ?>
                        </td>
                        <td>
                            <?= $title['title_name'] ?>
                        </td>
                        <td>
                            <?= $title['title_author'] ?>
                        </td>
                        <td>
                            <?= $title['title_type'] ?>
                        </td>
                        <td>
                            <?= $title['title_year'] ?>
                        </td>
                        <td style="background: white"><img src='uploads/<?= $title['title_img'] ?>'></td>
                        <td><a href="book.php?title_id=<?= $title['title_id'] ?>" class='btn btn-light'><i
                                    class="fa-solid fa-eye"></i> Xem chi tiết</a></td>
                        <td><a href="edit_title.php?id=<?= $title['title_id'] ?>" class='btn btn-warning'>Edit</a></td>
                        <td>
                            <a href="delete_title.php?id=<?= $title['title_id'] ?>" class='btn btn-danger'
                                id='btn_delete'>Delete</a>
                        </td>
                    <tr>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>
    <?php else:
        echo "<div class='no-result'>Mã đầu sách không tồn tại</div>"; ?>
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
    <script>
        //Delete user
        $("a#btn_delete").click(function () {
            if (confirm('Bạn chắc chắn muốn xóa quyển sách này không?')) {
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
            $("#title").validate({
                rules: {
                    title_name: { required: true, minlength: 4, maxlength: 50 },
                    title_author: { required: true, minlength: 4, maxlength: 50 },
                    title_type: { required: true, minlength: 4, maxlength: 50 },
                    title_year: { required: true, digits: true, maxlength: 4 },
                },
                messages: {
                    title_name: {
                        required: "Tên quyển sách không được để trống",
                        minlength: "Tên quyển sách quá ngắn",
                        maxlength: "Tên quyển sách quá dài"
                    },
                    title_author: {
                        required: "Tên tác giả không được để trống",
                        minlength: "Tên tác giả quá ngắn",
                        maxlength: "Tên tác giả quá dài"
                    },
                    title_type: {
                        required: "Tên thể loại không được để trống",
                        minlength: "Tên thể loại quá ngắn",
                        maxlength: "Tên thể loại quá dài"
                    },
                    title_year: {
                        required: "Năm xuất bản không được để trống",
                        digits: "Năm xuất bản phải là 1 dãy số",
                        maxlength: "Năm xuất bản không có thật"
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
        });
    </script>
</body>

</html>