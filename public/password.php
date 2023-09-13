<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';

// Lấy dữ liệu từ form đăng nhập
$username = $_SESSION['user']['username'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $db->prepare("SELECT username, password FROM user WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        //Kiểm tra mật khẩu
        if ($current_password == $user['password']) {
            // Đăng nhập thành công

            $stmt = $db->prepare('UPDATE user SET password=:password WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $new_password);

            $stmt->execute();
            $_SESSION['user']['success'] = "Change password successfully!";
            unset($_SESSION['user']['error']);


            header('location: profile.php');

        } else {
            $error = "Mật khẩu hiện tại không đúng";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/partials.css">
    <link rel="stylesheet" href="css/form.css">
</head>

<style>
    .container-form {
        max-width: 500px;
        margin: auto;
        margin-top: 10px;
    }

    .title a {
        color: black;
    }
</style>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="bg"></div>
    <div class="bg bg2"></div>
    <div class="bg bg3"></div>
    <div class="container">
        <div class="title">
            <br>
            <a href="../">Home</a> /
            <a href="profile.php">Profile</a> /
            <a href="#"><b>Change password</b></a>
        </div>
        <hr>
    </div>
    <div class="container-form">
        <ul class="list-group">
            <li class="list-group-item list-group-item-primary title-form"><b>Change Password</b></li>
            <li class="list-group-item">
                <form action="#" id="signupForm" class="form-horizontal" method="POST">

                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <strong>Lỗi!</strong>
                        <?php echo $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif ?>

                    <div class="mb-3 mt-3">
                        <label for="current_password" class="form-label"><b>Current password:</b></label>
                        <input type="password" class="form-control" id="current_password" name="current_password"
                            placeholder="Enter your current password">
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="new_password" class="form-label"><b>New password:</b></label>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                            placeholder="Enter new password">
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="confirm_password" class="form-label"><b>Confirm password:</b></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            placeholder="Confirm new password">
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Change</button>
                        </div>
                        <div class="col-3"><a href="profile.php" class="btn btn-danger" role="button">Cancel</a>
                        </div>
                    </div>
                </form>
            </li>
        </ul>
    </div>
    <br>
    <?php include '../partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#signupForm").validate({
                rules: {
                    current_password: { required: true, minlength: 4, maxlength: 50 },
                    new_password: { required: true, minlength: 4, maxlength: 50 },
                    confirm_password: { required: true, minlength: 4, maxlength: 50, equalTo: "#new_password" }, email: { required: true, email: true },
                },
                messages: {
                    current_password: {
                        required: "Bạn chưa nhập vào mật khẩu hiện tại",
                        minlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự"
                    },
                    new_password: {
                        required: "Bạn chưa nhập vào mật khẩu mới",
                        minlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự"
                    },
                    confirm_password: {
                        required: "Bạn chưa nhập lại mật khẩu",
                        minlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự",
                        maxlength: "Mật khẩu phải có ít nhất 4 - 50 ký tự",
                        equalTo: "Mật khẩu đã nhập không trùng khớp với nhau"
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