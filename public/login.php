<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_logged.php';
// Lấy dữ liệu từ form đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];


    //Kiểm tra user
    $stmt = $db->prepare('SELECT * FROM user WHERE username = :username OR email = :username');
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        //Kiểm tra mật khẩu
        if ($password == $user['password']) {
            // Đăng nhập thành công, lưu thông tin vào session
            $_SESSION['user']['name'] = $user['fullname'];
            $_SESSION['user']['username'] = $user['username'];
            $_SESSION['user']['sdt'] = $user['sdt'];
            $_SESSION['user']['email'] = $user['email'];
			$_SESSION['user']['class'] = $user['class'];
			$_SESSION['user']['course'] = $user['course'];
            $_SESSION['user']['role'] = $user['role'];
            $_SESSION['user']['id'] = $user['user_id'];
            $_SESSION['user']['avatar'] = $user['file_avatar'];
			if ($_SESSION['user']['role'] == "admin"){
				header('location: dashboard.html');
			} else{
				header('location: ../');
			}
        } else {
            $error = "Sai tên đăng nhập hoặc mật khẩu";
        }
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu"; //Tài khoản không tồn tại
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/form.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">
<!--===============================================================================================-->
</head>
<?php include '../partials/header.php'; ?>
<body>
<div class="limiter">
		<div class="container-login">
			<div class="wrap-login slide">
				<div class="login-pic" data-tilt>
					<img src="image/img-login.jpg" alt="IMG">
				</div>

				<form action="#" id="signupForm" method="POST" class="login-form validate-form">
					<strong class="login-form-title">
						Member Login
					</strong>
					<?php if (isset($error)): ?>
                    <div class="alert alert-danger" style="margin-top: -40px">
                        <strong style="font-size: 1rem">Lỗi!</strong>
                        <?php echo $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true"></button>
                    </div>
                    <?php endif ?>

					<div class="wrap-input">
						<input class="input" type="text" name="username" placeholder="Enter your username or email">
						<span class="focus-input"></span>
						<span class="symbol-input">
							<i class="fa-solid fa-user" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input">
						<input class="input" type="password" name="password" placeholder="Enter your password">
						<span class="focus-input"></span>
						<span class="symbol-input">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login-form-btn">
						<button class="login-form-btn">
							Login
						</button>
					</div>

					<!-- <div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="#">
							Username / Password?
						</a>
					</div> -->

					<div class="text-center login-signup">
						<a class="txt" href="signup.php">
							Create your Account
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
<!--===============================================================================================-->	
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
<!--===============================================================================================-->
<script src="vendor/bootstrap/js/popper.js"></script>
<script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
<!--===============================================================================================-->
<script type="text/javascript" src="js/jquery.validate.js"></script>

<script type="text/javascript">
	$(document).ready(function () {
		$("#signupForm").validate({
			rules: {
				username: { required: true, minlength: 4, maxlength: 50 },
				password: { required: true, minlength: 4, maxlength: 50 }
			},
			messages: {
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
			},

			errorElement: "div",
			errorPlacement: function (error, element) {
				error.addClass("invalid-feedback"); //invalid-feedback là của bootstrap và các valid tương tự
					error.insertAfter(element.parent("div"));
			},
		});
	});
</script>
</body>
</html>