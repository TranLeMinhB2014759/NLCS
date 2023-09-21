<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
//Thêm dữ liệu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $book_name = $_POST['book_name'];
    $book_author = $_POST['book_author'];
    $book_type = $_POST['book_type'];
    $book_year = $_POST['book_year'];
    $book_quantity = $_POST['book_quantity'];

    $file = $_FILES['book_img'];
        $allowType = ['image/png', 'image/jpeg', 'image/gif', 'image/tiff'];
        if (!in_array($file['type'], $allowType)) {
                die(header("Location: " . $_SERVER['HTTP_REFERER']));
        }
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["book_img"]["name"]);
        move_uploaded_file($_FILES["book_img"]["tmp_name"], $target_file);

    $stmt = $db->prepare('
			INSERT INTO quyensach (book_name, book_author, book_type, book_year, book_quantity, book_img)
			VALUES (:book_name, :book_author, :book_type, :book_year, :book_quantity, :book_img)
			');
    $stmt->bindParam(':book_name', $book_name);
    $stmt->bindParam(':book_author', $book_author);
    $stmt->bindParam(':book_type', $book_type);
    $stmt->bindParam(':book_year', $book_year);
    $stmt->bindParam(':book_quantity', $book_quantity);
    $stmt->bindParam(':book_img', $_FILES['book_img']["name"]);
    $stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
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
        QUẢN LÝ SÁCH TRONG THƯ VIỆN
    </div>
    <div class="btn-modal">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal" id="btn-modal-book"
            title="Thêm tài khoản mới">
            Thêm Sách &nbsp<i class="fas fa-edit"></i>
        </button>
        <!-- The Modal -->
        <div class="modal" id="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h2 class="modal-title"><i class="fas fa-user-edit">&nbsp</i>Thêm sách</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <form method="post" id="book" class="form-horizontal" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="book_name" class="form-label"><b>Tên sách:</b></label>
                                <input type="text" class="form-control" id="book_name" name="book_name"
                                    placeholder="Enter the book title">
                            </div>
                            <div class="row">
                                <div class="mb-3 col-6">
                                    <label for="book_author" class="form-label"><b>Tên tác giả:</b></label>
                                    <input type="text" class="form-control" id="book_author" name="book_author"
                                        placeholder="Enter the author">
                                </div>
                                <div class="mb-3 col-6">
                                    <label for="book_type" class="form-label"><b>Thể loại:</b></label>
                                    <input type="text" class="form-control" id="book_type" name="book_type"
                                        placeholder="Enter the book type">
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-6">
                                    <label for="book_year" class="form-label"><b>Xuất bản:</b></label>
                                    <input class="form-control" id="book_year" name="book_year" placeholder="Enter the year">
                                </div>
                                <div class="mb-3 col-6">
                                    <label for="book_quantity" class="form-label"><b>Số lượng:</b></label>
                                    <input class="form-control" id="book_quantity" name="book_quantity" placeholder="Enter quantity">
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <input type='file' name='book_img' id='book_img' accept='image/png, image/jpeg, image/gif, image/tiff' required> <br>
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
                <th>ID</th>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Thể loại</th>
                <th>Xuất bản</th>
                <th>Số lượng</th>
                <th>Ảnh</th>
                <th>Sửa</th>
                <th>Xóa</th>
            </tr>
            <?php $query = 'SELECT * FROM quyensach;';
            $results = $db->query($query);

            $data = [];
            while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                $data[] = array(
                    'book_id' => $row['book_id'],
                    'book_name' => $row['book_name'],
                    'book_author' => $row['book_author'],
                    'book_type' => $row['book_type'],
                    'book_year' => $row['book_year'],
                    'book_img' => $row['book_img'],
                    'book_quantity' => $row['book_quantity'],
                );
            }
            ?>
            <?php foreach ($data as $book): ?>
                <tr>
                    <td>
                        <?= $book['book_id'] ?>
                    </td>
                    <td>
                        <?= $book['book_name'] ?>
                    </td>
                    <td>
                        <?= $book['book_author'] ?>
                    </td>
                    <td>
                        <?= $book['book_type'] ?>
                    </td>
                    <td>
                        <?= $book['book_year'] ?>
                    </td>
                    <td>
                        <?= $book['book_quantity'] ?>
                    </td>
                    <td style="background: white"><img src='uploads/<?= $book['book_img'] ?>'></td>
                    <td><a href="edit_book.php?id=<?= $book['book_id'] ?>" class='btn btn-warning'>Edit</a></td>
                    <td>
                        <a href="delete_book.php?id=<?= $book['book_id'] ?>" class='btn btn-danger' id='btn_delete'>Delete</a>
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
    <!-- <script src="js/DataTables-1.13.6/js/datatables.min.js"></script> -->
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
            $("#book").validate({
                rules: {
                    book_name: { required: true, minlength: 4, maxlength: 50 },
                    book_author: { required: true, minlength: 4, maxlength: 50 },
                    book_type: { required: true, minlength: 4, maxlength: 50 },
                    book_year: { required: true, digits: true, maxlength: 4 },
                    book_quantity: { required: true, digits: true},
                },
                messages: {
                    book_name: {
                        required: "Tên quyển sách không được để trống",
                        minlength: "Tên quyển sách quá ngắn",
                        maxlength: "Tên quyển sách quá dài"
                    },
                    book_author: {
                        required: "Tên tác giả không được để trống",
                        minlength: "Tên tác giả quá ngắn",
                        maxlength: "Tên tác giả quá dài"
                    },
                    book_type: {
                        required: "Tên thể loại không được để trống",
                        minlength: "Tên thể loại quá ngắn",
                        maxlength: "Tên thể loại quá dài"
                    },
                    book_year: {
                        required: "Năm xuất bản không được để trống",
                        digits: "Năm xuất bản phải là 1 dãy số",
                        maxlength: "Năm xuất bản không có thật"
                    },
                    book_quantity: {
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