<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_user.php';
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
                            <li id="tab2"><a href="#" onclick="active_borrow()">Sách đã mượn</a></li>
                            <li id="tab3"><a href="#" onclick="active_giveback()">Sách đã trả</a></li>
                            <li id="tab4"><a href="#" onclick="active_expired()">Sắp hết hạn</a></li>
                    </div>
                </div>
            </div>

            <div class="mainContent col-sm-9">
                <div class="group-box min-height">
                    <div class="title text-center">Thông báo</div>
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
                        <div class="main-content tab1">
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
                                            <form action="avatar.php" method="POST" enctype="multipart/form-data">
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
                                            </form><!-- Vì một vài lí do của bs5 nên phải đển form ở đây-->
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
                                                <form action="fullname.php" id="signupForm0" class="form-horizontal"
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
                                                <button id="btnuser" type="submit" class="btn btn-primary">
                                                    OK
                                                </button>
                                                </form><!-- Vì một vài lí do của bs5 nên phải đển form ở đây-->
                                                <button class="btn btn-danger" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="username" class="form-label">Username:</label>
                                <?php echo $_SESSION['user']['username'] ?>
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label for="sdt" class="form-label">Phone Number: </label>
                                <?php if ($_SESSION['user']['sdt'] == 0) {
                                    echo "Thông tin chưa được cập nhật";
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
                                                <form action="sdt.php" id="signupForm1" class="form-horizontal"
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
                                                <button type="submit" class="btn btn-primary">
                                                    OK
                                                </button>
                                                </form><!-- Vì một vài lí do của bs5 nên phải đển form ở đây-->
                                                <button class="btn btn-danger" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="email" class="form-label">Email: </label>
                                <?php if ($_SESSION['user']['email'] == 0) {
                                    echo "Thông tin chưa được cập nhật";
                                } else {
                                    echo $_SESSION['user']['email'];
                                }
                                ?>
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
                                                <form action="email.php" id="signupForm2" class="form-horizontal"
                                                    method="POST">
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">
                                                            <i class="fa-solid fa-envelope fa-bounce">&nbsp</i>Email:
                                                        </label>
                                                        <input type="email" class="form-control"
                                                            placeholder="Nhập vào email" id="email" name="email"
                                                            value="<?php echo $_SESSION['user']['email'] ?>"></input>
                                                    </div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">
                                                    OK
                                                </button>
                                                </form><!-- Vì một vài lí do của bs5 nên phải đển form ở đây-->
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
                        <div class="text-center tab2" style="display:none">Không có thông tin</div>
                        <div class="text-center tab3" style="display:none">Không có thông tin</div>
                        <div class="text-center tab4" style="display:none">Không có thông tin</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    <?php include '../partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/profile.js"></script>
    <!-- <script src="js/partials.js"></script> -->

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
        });
        $(document).ready(function () {
            $("#signupForm1").validate({
                rules: {
                    sdt: { required: true, digits: true, minlength: 9 },
                },
                messages: {
                    sdt: {
                        required: "Bạn chưa nhập vào số điện thoại",
                        digits: "Số điện thoại phải là một dãy số",
                        minlength: "Số điện thoại phải tử 9 chữ số"
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
        $(document).ready(function () {
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
</body>
<?php
unset($_SESSION['user']['error']);
unset($_SESSION['user']['success']);
?>

</html>