<?php
if (isset($_SESSION['user'])) {
    if (($_SESSION['user']['role'] == 1)) {
        ///Có quyền admin
    } if($_SESSION['user']['role'] == 0) {
        die('Bạn không có quyền truy cập vào trang web này!');
    }
}
else{
    die('Bạn không có quyền truy cập vào trang web này!');
}

//Tạo tài khoản trên Database, để role là 1 => Admin và mặc định giá trị role lần tạo tiếp theo là 0 

//Khi đăng nhập thành công thì $_SESSION['user']['role'] nhận từ CSDL bảng user giá trị role: (Line 25: login.php)
//Nếu có role == 1 => Admin
//Nếu có role == 0 => User