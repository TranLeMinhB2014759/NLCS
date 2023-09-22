<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';

if (isset($_POST['id'])) {
    $b_name = $_POST['b_name'];
    $b_author = $_POST['b_author'];
    $b_type = $_POST['b_type'];
    $b_year = $_POST['b_year'];
    $b_quantity = $_POST['b_quantity'];

    if (empty($_FILES['b_img']['name'])) {
        //Lưu ảnh vào floder avatar
        $_FILES["b_img"]["name"] = $_POST['b_file_uploads'];
    }else{
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["b_img"]["name"]);

        unlink($target_dir . $_POST['b_file_uploads']);
        move_uploaded_file($_FILES["b_img"]["tmp_name"], $target_file);
    }

    $query = 'UPDATE quyensach SET book_name=?, book_author=?, book_type=?, book_year=?, book_quantity=?, book_img=? WHERE book_id=?';
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_POST['b_name'],
        $_POST['b_author'],
        $_POST['b_type'],
        $_POST['b_year'],
        $_POST['b_quantity'],
        $_FILES["b_img"]["name"],
        $_POST['id']
    ]);

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Accounts</title>
    <link rel="shortcut icon" href="image/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/edit_user.css">
    <link rel="stylesheet" href="css/partials.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
    <section class="manage">
        <div class="container-form">
            <div class="title-edit">
                <h2>Chỉnh sửa thông tin quyển sách</h2>
            </div>
            <?php if (isset($_GET['id'])) {
                $query = "SELECT * FROM quyensach WHERE book_id={$_GET['id']}";
                $ch = $db->query($query);
                $row = $ch->fetch();
                echo "<form action='edit_book.php' method='post'id='edit' class='form-horizontal' enctype='multipart/form-data'>
            <div class='mb-3'>
                <label for='b_name'><b>Tên quyển sách:</b></label>
                <input class='form-control' id='b_name' name='b_name'
                    value='" . $row["book_name"] . "'>
            </div>
            <div class='mb-3 mt-3'>
                <label for='b_author'><b>Tác giả:</b></label>
                <input class='form-control' id='b_author' name='b_author' value='" . $row["book_author"] . "'>
            </div>
            <div class='mb-3 mt-3'>
                <label for='b_type'><b>Thể loại:</b></label>
                <input class='form-control' id='b_type' name='b_type' value='" . $row["book_type"] ."'>
            </div>
            <div class='row'>
                <div class='mb-3 col-6'>
                    <label for='b_year'><b>Xuất bản:</b></label>
                    <input type='text' class='form-control' id='b_year' name='b_year'
                        value='" . $row["book_year"] . "'>
                </div>
                <div class='mb-3 col-6'>
                    <label for='b_quantity'><b>Số lượng:</b></label>
                    <input type='text' class='form-control' id='b_quantity' name='b_quantity'
                        value='" . $row["book_quantity"] . "'>
                </div>
            </div>
            <div class='mb-3 mt-3'>
            <div style='left: 60px'>
                <img class='image image-after img-fluid rounded-circle'>
            </div>
            <br>
            <input type='file' name='b_img' id='b_img' accept='image/png, image/jpeg, image/gif, image/tiff'> <br>
            </div>
            <input type= 'hidden' name='b_file_uploads' value='" . $row['book_img'] . "'>

            <input type= 'hidden' name='id' value='" . $_GET['id'] . "'>
            <div class='row'>
                <div class='col-2'>
                    <button type='submit' class='btn btn-primary' name='btn-edit'>Sửa</button>
                </div>
                <div class='col-3'>
                    <a href='manage_books.php' class='btn btn-info'> Quay lại</a>
                </div>
                <div class='col-7'>
                </div>
            </div>
            </div>
            </form>
            ";
            } elseif (isset($_POST['id'])) {
                echo "
                <div class='success'>
                    <div class='text-center'><h2>Đã sửa thành công</h2></div>
                    <div class='back'>
                        <a href='manage_books.php' class='btn btn-info'> Quay lại</a>
                    </div>
                </div>
                ";
            }
            ?>
        </div>
    </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script src="js/edit_phone.js"></script>
    <script src="js/partials.js"></script>
    <script type="text/javascript" src="js/jquery.validate.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#edit").validate({
                rules: {
                    b_name: { required: true, minlength: 4, maxlength: 50 },
                    b_author: { required: true, minlength: 4, maxlength: 50 },
                    b_type: { required: true, minlength: 4, maxlength: 50 },
                    b_year: { required: true, digits: true, maxlength: 4 },
                    b_quantity: { required: true, digits: true},
                },
                messages: {
                    b_name: {
                        required: "Tên quyển sách không được để trống",
                        minlength: "Tên quyển sách quá ngắn",
                        maxlength: "Tên quyển sách quá dài"
                    },
                    b_author: {
                        required: "Tên tác giả không được để trống",
                        minlength: "Tên tác giả quá ngắn",
                        maxlength: "Tên tác giả quá dài"
                    },
                    b_type: {
                        required: "Tên thể loại không được để trống",
                        minlength: "Tên thể loại quá ngắn",
                        maxlength: "Tên thể loại quá dài"
                    },
                    b_year: {
                        required: "Năm xuất bản không được để trống",
                        digits: "Năm xuất bản phải là một dãy số",
                        maxlength: "Năm xuất bản không có thật"
                    },
                    b_quantity: {
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
    <script>
        const image =document.querySelector(".image-after")
        const input=document.querySelector("#b_img")
        input.addEventListener("change", () =>{
            image.src=URL.createObjectURL(input.files[0])
        })
    </script>
</body>

</html>