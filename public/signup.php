<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_logged.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$username = $_POST['username'];
	$password = $_POST['password'];
	$fullname = $_POST['fullname'];
	$email = $_POST['email'];

	$stmt = $db->prepare("SELECT username, email FROM user WHERE username=:username OR email=:email");
	$stmt->bindParam(':username', $username);
	$stmt->bindParam(':email', $email);
	$stmt->execute();


	if ($stmt->rowCount() > 0) {
		$error = "Tên đăng nhập hoặc email đã tồn tại";
	} else {

		$stmt = $db->prepare('
				INSERT INTO user (username, password, fullname, email)
				VALUES (:username, :password, :fullname, :email)
				');
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':fullname', $fullname);
		$stmt->bindParam(':email', $email);

		$stmt->execute();
		header('location: login.php');
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Sign Up</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
	<link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/form.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">

</head>
<?php include '../partials/header.php'; ?>
<body>
	<div class="limiter">
		<div class="container-login">
			<div class="wrap-login slide">
				<div class="login-pic" data-tilt>
					<img src="image/img-login.jpg" alt="IMG">
				</div>

				<form action="#" id="signupForm" method="POST" class="login-form validate-form" style="margin-top: -37px;">
					<strong class="login-form-title">
						Member Sign Up
					</strong>
					<?php if (isset($error)): ?>
                    <div class="alert alert-danger" style="margin-top: -40px">
                        <strong style="font-size: 1rem">Lỗi!</strong>
                        <?php echo $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true"></button>
                    </div>
                    <?php endif ?>

					<div class="wrap-input">
						<input class="input" type="text" name="fullname" placeholder="Enter your name">
						<span class="focus-input"></span>
						<span class="symbol-input">
                            <i class="fa-sharp fa-solid fa-user-secret" aria-hidden="true"></i>
						</span>
					</div>
                    
                    <div class="wrap-input">
						<input class="input" type="text" name="username" placeholder="Enter your username">
						<span class="focus-input"></span>
						<span class="symbol-input">
							<i class="fa-solid fa-user" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input">
						<input class="input" type="text" name="email" placeholder="Enter your email">
						<span class="focus-input"></span>
						<span class="symbol-input">
							<i class="fa-solid fa-envelope"></i>
						</span>
					</div>

                    <div class="wrap-input">
						<input class="input" type="password" name="password" id="password" placeholder="Enter your password">
						<span class="focus-input"></span>
						<span class="symbol-input">
                            <i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input">
						<input class="input" type="password" id="confirm_password" name="confirm_password" placeholder="Enter the password">
						<span class="focus-input"></span>
						<span class="symbol-input">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login-form-btn">
						<button class="login-form-btn">
							Sign Up 
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
						<a class="txt" href="login.php">
							Login
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
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
<!--===============================================================================================-->
	<script type="text/javascript" src="js/jquery.validate.js"></script>

	<script type="text/javascript">
		$(document).ready(function () {
			$("#signupForm").validate({
				rules: {
					fullname: { required: true, minlength: 4, maxlength: 50 },
					username: { required: true, minlength: 4, maxlength: 50 },
					email: { required: true },
					password: { required: true, minlength: 4, maxlength: 50 },
					confirm_password: { required: true, minlength: 4, maxlength: 50, equalTo: "#password" }, email: { required: true, email: true },
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
					email: "Email không hợp lệ",
					password: {
						required: "Bạn chưa nhập vào mật khẩu",
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
                    error.insertAfter(element.parent("div")); //instertAfter là thêm sau giá trị của jquery
				},
			});
		});
	</script>
</body>
</html>