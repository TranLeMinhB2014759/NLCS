<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';

if (isset($_POST['id'])) {
    $t_name = $_POST['t_name'];
    $t_author = $_POST['t_author'];
    $t_type = $_POST['t_type'];
    $t_year = $_POST['t_year'];

    if (empty($_FILES['t_img']['name'])) {
        //Lưu ảnh vào floder avatar
        $_FILES["t_img"]["name"] = $_POST['t_file_uploads'];
    }else{
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["t_img"]["name"]);

        unlink($target_dir . $_POST['t_file_uploads']);
        move_uploaded_file($_FILES["t_img"]["tmp_name"], $target_file);
    }

    $query = 'UPDATE dausach SET title_name=?, title_author=?, title_type=?, title_year=?, title_img=? WHERE title_id=?';
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_POST['t_name'],
        $_POST['t_author'],
        $_POST['t_type'],
        $_POST['t_year'],
        $_FILES["t_img"]["name"],
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
                <h2>Chỉnh sửa thông tin đầu sách</h2>
            </div>
            <?php if (isset($_GET['id'])) {
                $query = "SELECT * FROM dausach WHERE title_id={$_GET['id']}";
                $ch = $db->query($query);
                $row = $ch->fetch();
                echo "<form action='edit_title.php' method='post'id='edit' class='form-horizontal' enctype='multipart/form-data'>
            <div class='mb-3'>
                <label for='t_name'><b>Tên đầu sách:</b></label>
                <input class='form-control' id='t_name' name='t_name'
                    value='" . $row["title_name"] . "'>
            </div>
            <div class='mb-3 mt-3'>
                <label for='t_author'><b>Tác giả:</b></label>
                <input class='form-control' id='t_author' name='t_author' value='" . $row["title_author"] . "'>
            </div>
            <div class='mb-3 mt-3'>
                <label for='t_type'><b>Thể loại:</b></label>
                <input class='form-control' id='t_type' name='t_type' value='" . $row["title_type"] ."'>
            </div>
            <div class='mb-3 col-6'>
                <label for='t_year'><b>Xuất bản:</b></label>
                <input type='text' class='form-control' id='t_year' name='t_year'
                        value='" . $row["title_year"] . "'>
            </div>
            <div class='mb-3 mt-3'>
            <div style='left: 60px'>
                <img class='image image-after img-fluid rounded-circle'>
            </div>
            <br>
            <input type='file' name='t_img' id='t_img' accept='image/png, image/jpeg, image/gif, image/tiff'> <br>
            </div>
            <input type= 'hidden' name='t_file_uploads' value='" . $row['title_img'] . "'>

            <input type= 'hidden' name='id' value='" . $_GET['id'] . "'>
            <div class='row'>
                <div class='col-2'>
                    <button type='submit' class='btn btn-primary' name='btn-edit'>Sửa</button>
                </div>
                <div class='col-3'>
                    <a href='manage_titles.php' class='btn btn-info'> Quay lại</a>
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
                        <a href='manage_titles.php' class='btn btn-info'> Quay lại</a>
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
                    t_name: { required: true, minlength: 4, maxlength: 50 },
                    t_author: { required: true, minlength: 4, maxlength: 50 },
                    t_type: { required: true, minlength: 4, maxlength: 50 },
                    t_year: { required: true, digits: true, maxlength: 4 },
                },
                messages: {
                    t_name: {
                        required: "Tên đầu sách không được để trống",
                        minlength: "Tên đầu sách quá ngắn",
                        maxlength: "Tên đầu sách quá dài"
                    },
                    t_author: {
                        required: "Tên tác giả không được để trống",
                        minlength: "Tên tác giả quá ngắn",
                        maxlength: "Tên tác giả quá dài"
                    },
                    t_type: {
                        required: "Tên thể loại không được để trống",
                        minlength: "Tên thể loại quá ngắn",
                        maxlength: "Tên thể loại quá dài"
                    },
                    t_year: {
                        required: "Năm xuất bản không được để trống",
                        digits: "Năm xuất bản phải là một dãy số",
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
    <script>
        const image =document.querySelector(".image-after")
        const input=document.querySelector("#t_img")
        input.addEventListener("change", () =>{
            image.src=URL.createObjectURL(input.files[0])
        })
    </script>
</body>

</html>