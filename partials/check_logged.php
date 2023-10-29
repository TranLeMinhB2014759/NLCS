<?php
if (isset($_SESSION['user'])) {
        header('location: ../');
}

//Tạo tài khoản trên Database, để role là 'admin' => Admin và mặc định giá trị role lần tạo tiếp theo là other

//Khi đăng nhập thành công thì $_SESSION['user']['role'] nhận từ CSDL bảng user giá trị role: (Line 27: login.php)
//Nếu có role == 'admin' => Admin
//Nếu có role == 'other', 'studen', 'teacher' => User