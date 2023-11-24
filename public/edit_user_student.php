<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';

if (isset($_POST['id'])) {
    $username = $_POST['u_username'];
    $fullname = $_POST['u_fullname'];
    $password = $_POST['u_password'];
    $class = $_POST['u_class'];
    $course = $_POST['u_course'];
    $sdt = $_POST['u_sdt'];
    $email = $_POST['u_email'];

    if (empty($_FILES['u_avatar']['name'])) {
        //Lưu ảnh vào floder avatar
        $_FILES["u_avatar"]["name"] = $_POST['u_file_avatar'];
    } else {
        $target_dir = "avatar/";
        $target_file = $target_dir . basename($_FILES["u_avatar"]["name"]);
        if ($_POST['u_file_avatar'] == 'avatarUser.png') {
            move_uploaded_file($_FILES["u_avatar"]["tmp_name"], $target_file);
        } else {
            unlink($target_dir . $_POST['u_file_avatar']);
            move_uploaded_file($_FILES["u_avatar"]["tmp_name"], $target_file);
        }
    }


    $query = 'UPDATE user SET username=?, fullname=?, password=?, class=?, course=?, sdt=?, email=?, file_avatar=? WHERE user_id=?';
    $stmt = $db->prepare($query);
    $stmt->execute([
        $username,
        $fullname,
        $password,
        $class,
        $course,
        $sdt,
        $email,
        $_FILES["u_avatar"]["name"],
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
                    <h2>Chỉnh sửa thông tin người dùng</h2>
                </div>
                <?php if (isset($_GET['id'])) {
                    $query = "SELECT user_id, fullname, username, password, class, course, sdt, email, file_avatar FROM user WHERE user_id={$_GET['id']}";
                    $ch = $db->query($query);
                    $row = $ch->fetch();
                    echo "<form action='edit_user_student.php' method='post'id='edit' class='form-horizontal' enctype='multipart/form-data'>
                        <div class='mb-3'>
                            <label for='u_fullname'><b>Your Name:</b></label>
                            <input class='form-control' id='u_fullname' name='u_fullname'
                                value='" . $row["fullname"] . "'>
                        </div>
                        <div class='mb-3 mt-3'>
                            <label for='u_username'><b>Username:</b></label>
                            <input class='form-control' id='u_username' name='u_username' value='" . $row["username"] . "'>
                        </div>
                        <div class='mb-3 mt-3'>
                            <label for='u_password'><b>Password:</b></label>
                            <input class='form-control' id='u_password' name='u_password' value='" . $row["password"] . "'>
                        </div>
                        <div class='row'>
                            <div class='mb-3 col-6'>
                                <label for='u_class'><b>Class:</b></label>
                                <input type='text' class='form-control' id='u_class' name='u_class'
                                    value='" . $row["class"] . "'>
                            </div>
                            <div class='mb-3 col-6'>
                                <label for='u_course'><b>Course:</b></label>
                                <input type='text' class='form-control' id='u_course' name='u_course'
                                    value='" . $row["course"] . "'>
                            </div>
                        </div>
                        <div class='mb-3 mt-3'>
                            <label for='u_sdt'><b>Phone Number:</b></label>
                            <input type='text' class='form-control' id='u_sdt' name='u_sdt'
                                value='" . $row["sdt"] . "'>
                        </div>
                        <div class='mb-3 mt-3'><b>Email:</b></label>
                            <input type='email' class='form-control' id='u_email' name='u_email' placeholder='Enter the email'
                                value='" . $row["email"] . "'>
                        </div>";
                    echo"
                        <div class='mb-3 mt-3'>
                        <div style='left: 60px'>
                            <img class='image image-after img-fluid rounded-circle'>
                        </div>
                        <br>
                        <input type='file' name='u_avatar' id='u_avatar' accept='image/png, image/jpeg, image/gif, image/tiff'> <br>
                        </div>
                        <input type= 'hidden' name='u_file_avatar' value='" . $row['file_avatar'] . "'>

                        <input type= 'hidden' name='id' value='" . $_GET['id'] . "'>
                        <div class='row'>
                            <div class='col-2'>
                                <button type='submit' class='btn btn-primary' name='btn-edit'>Sửa</button>
                            </div>
                            <div class='col-3'>
                                <a href='manage_users_student.php' class='btn btn-info'> Quay lại</a>
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
                        <a href='manage_users_student.php' class='btn btn-info'> Quay lại</a>
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
    <script type="text/javascript" src="js/jquery.validate.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#edit").validate({
                rules: {
                    u_fullname: { required: true, minlength: 4, maxlength: 50 },
                    u_username: { required: true, minlength: 4, maxlength: 50 },
                    u_password: { required: true, minlength: 4, maxlength: 50 },
                    u_class: { required: true, maxlength: 8 },
                    u_sdt: { required: true, digits: true, minlength: 9 },
                    u_email: { required: true, minlength: 4, maxlength: 50 },
                },
                messages: {
                    u_fullname: {
                        required: "Bạn chưa nhập vào tên của bạn",
                        minlength: "Tên phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Tên phải có ít nhất 4 - 50 ký tự"
                    },
                    u_username: {
                        required: "Bạn chưa nhập vào tên đăng nhập",
                        minlength: "Tên đăng nhập phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Tên đăng nhập phải có ít nhất 4 - 50 ký tự"
                    },
                    u_password: {
                        required: "Bạn chưa nhập vào mật khẩu mới",
                        minlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự"
                    },
                    u_class: {
                        required: "Bạn chưa nhập vào tên lớp",
                        maxlength: "Tên lớp không có thật"
                    },
                    u_sdt: {
                        required: "Bạn chưa nhập vào số điện thoại",
                        digits: "Số điện thoại phải là một dãy số",
                        minlength: "Số điện thoại phải tử 9 chữ số"
                    },
                    u_email: "Email không hợp lệ",
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
        const image = document.querySelector(".image-after")
        const input = document.querySelector("#u_avatar")
        input.addEventListener("change", () => {
            image.src = URL.createObjectURL(input.files[0])
        })
    </script>
</body>

</html>