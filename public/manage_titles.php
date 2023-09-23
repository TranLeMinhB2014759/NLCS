<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
//Thêm dữ liệu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title_name = $_POST['title_name'];
    $title_author = $_POST['title_author'];
    $title_type = $_POST['title_type'];
    $title_year = $_POST['title_year'];
    $title_quantity = $_POST['title_quantity'];

    $file = $_FILES['title_img'];
        $allowType = ['image/png', 'image/jpeg', 'image/gif', 'image/tiff'];
        if (!in_array($file['type'], $allowType)) {
                die(header("Location: " . $_SERVER['HTTP_REFERER']));
        }
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["title_img"]["name"]);
        move_uploaded_file($_FILES["title_img"]["tmp_name"], $target_file);

        $stmt = $db->prepare('
                INSERT INTO dausach (title_name, title_author, title_type, title_year, title_quantity, title_img)
                VALUES (:title_name, :title_author, :title_type, :title_year, :title_quantity, :title_img)
                ');
        $stmt->bindParam(':title_name', $title_name);
        $stmt->bindParam(':title_author', $title_author);
        $stmt->bindParam(':title_type', $title_type);
        $stmt->bindParam(':title_year', $title_year);
        $stmt->bindParam(':title_quantity', $title_quantity);
        $stmt->bindParam(':title_img', $_FILES['title_img']["name"]);
        $stmt->execute();
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
//Đầu sách
$query = 'SELECT * FROM dausach;';
$results = $db->query($query);

$data = [];
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $data[] = array(
        'title_id' => $row['title_id'],
        'title_name' => $row['title_name'],
        'title_author' => $row['title_author'],
        'title_type' => $row['title_type'],
        'title_year' => $row['title_year'],
        'title_img' => $row['title_img'],
        'title_quantity' => $row['title_quantity'],
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
        QUẢN LÝ SÁCH TRONG THƯ VIỆN
    </div>
    <div class="btn-modal">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal" id="btn-modal-book"
            title="Thêm tài khoản mới">
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
                            <div class="row">
                                <div class="mb-3 col-6">
                                    <label for="title_year" class="form-label"><b>Xuất bản:</b></label>
                                    <input class="form-control" id="title_year" name="title_year" placeholder="Enter the year">
                                </div>
                                <div class="mb-3 col-6">
                                    <label for="title_quantity" class="form-label"><b>Số lượng:</b></label>
                                    <input class="form-control" id="title_quantity" name="title_quantity" placeholder="Enter quantity">
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <input type='file' name='title_img' id='title_img' accept='image/png, image/jpeg, image/gif, image/tiff' required> <br>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-2">
                                    <button type="submit" class="btn btn-primary btn-block">
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
                    <td><a href="book.php?title_id=<?= $title['title_id'] ?>" class='btn btn-light'><i class="fa-solid fa-eye"></i> Xem chi tiết</a></td>
                    <td><a href="edit_title.php?id=<?= $title['title_id'] ?>" class='btn btn-warning'>Edit</a></td>
                    <td>
                        <a href="delete_title.php?id=<?= $title['title_id'] ?>" class='btn btn-danger' id='btn_delete'>Delete</a>
                    </td>
                <tr>
                <?php endforeach; ?>
            </tr>
        </table>
    </div>
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
                    title_quantity: { required: true, digits: true},
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
                    title_quantity: {
                        required: "Hãy nhập vào số lượng",
                        digits: "Số lượng phải là một dãy số"
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