<?php
if (isset($_SESSION['user'])) {
}else{
    header('location: login.php');
}

//Tạo tài khoản trên Database, để role là 2 => Admin và mặc định giá trị role lần tạo tiếp theo là 0 

//Khi đăng nhập thành công thì $_SESSION['user']['role'] nhận từ CSDL bảng user giá trị role: (Line 25: login.php)
//Nếu có role == 2 => Admin
//Nếu có role == 0, 1 => User