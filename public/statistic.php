<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';

//Total Accounts
// Định nghĩa một mảng để lưu trữ số đếm
$status_counts_acc = array();

// Xác định các giá trị trạng thái và điều kiện SQL tương ứng
$status_values_acc = array(0, 1, 2);
$status_conditions_acc = array("role = 'student'", "role = 'teacher'", "role = 'other'");

// Lặp qua các giá trị trạng thái và truy vấn cơ sở dữ liệu cho từng giá trị
foreach ($status_values_acc as $index => $status) {
    $query = $db->prepare("SELECT * FROM user WHERE " . $status_conditions_acc[$index]);
    $query->execute();
    $status_counts_acc[$status] = $query->rowCount();
}

// Bây giờ bạn có một mảng $status_counts với số lượng cho mỗi trạng thái
$student = $status_counts_acc[0];
$teacher = $status_counts_acc[1];
$other = $status_counts_acc[2];

//--===============================================================================================--//

//Total Call Cards
// Định nghĩa một mảng để lưu trữ số đếm
$status_counts = array();

// Xác định các giá trị trạng thái và điều kiện SQL tương ứng
$status_values = array(0, 1, 2, 3);
$status_conditions = array("trangthai = 0", "trangthai = 1", "trangthai = 2", "trangthai = 3");

// Lặp qua các giá trị trạng thái và truy vấn cơ sở dữ liệu cho từng giá trị
foreach ($status_values as $index => $status) {
    $query = $db->prepare("SELECT * FROM phieumuon WHERE " . $status_conditions[$index]);
    $query->execute();
    $status_counts[$status] = $query->rowCount();
}

// Bây giờ bạn có một mảng $status_counts với số lượng cho mỗi trạng thái
$pending = $status_counts[0];
$borrow = $status_counts[1];
$return = $status_counts[2];
$cancel = $status_counts[3];

//--===============================================================================================--//

//Total Titles
$query = $db->prepare("SELECT * FROM dausach");
$query->execute();
$title = $query->rowCount();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/partials.css">
    <link rel="stylesheet" href="css/manage.css">
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="container" style="padding: 20px">
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="title">
                    THỐNG KÊ TÀI KHOẢN
                </div>
                <div class="text-center">
                    Tổng số:
                    <?= $student + $teacher+ $other ?>
                </div>
                <div id="donut-chart-account"></div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="title">
                    THỐNG KÊ PHIẾU MƯỢN
                </div>
                <div class="text-center">
                    Tổng số:
                    <?= $pending + $borrow + $return + $cancel ?>
                </div>
                <div id="donut-chart-pm"></div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="title">
                    THỐNG KÊ ĐẦU SÁCH
                </div>
                <div class="text-center">
                    Tổng số:
                    <?= $title ?>
                </div>
                <div id="donut-chart-title"></div>
            </div>
        </div>
    </div>
    <!--===============================================================================================-->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // ACCOUNTs
            Morris.Donut({
                element: 'donut-chart-account',
                resize: true,
                colors: [
                    '#0AFC53',
                    '#07e7F7',
                    '#B2B4C2',
                ],
                //labelColor:"#cccccc", // text color
                //backgroundColor: '#333333', // border color
                data: [
                    { label: "Teacher", value: <?= $teacher ?> },
                    { label: "Student", value: <?= $student ?> },
                    { label: "Other", value: <?= $other ?> },
                ]
            });

            //CALL CARDs
            var colorDanger = "#FF1744";
            Morris.Donut({
                element: 'donut-chart-pm',
                resize: true,
                colors: [
                    '#0AFC53',
                    '#07e7F7',
                    '#B2B4C2',
                ],
                //labelColor:"#cccccc", // text color
                //backgroundColor: '#333333', // border color
                data: [
                    { label: "Returned", value: <?= $return ?> },
                    { label: "Borrowing", value: <?= $borrow ?> },
                    { label: "Pending", value: <?= $pending ?> },
                    { label: "Cancelled", value: <?= $cancel ?>, color: colorDanger }
                ]
            });

            // //TITLEs
            // var colorDanger = "#FF1744";
            // Morris.Donut({
            // element: 'donut-chart-title',
            // resize: true,
            // colors: [
            //     '#0AFC53',
            //     '#07e7F7',
            //     '#B2B4C2',
            // ],
            // //labelColor:"#cccccc", // text color
            // //backgroundColor: '#333333', // border color
            // data: [
            //     {label:"Returned", value:<?= $return ?>},
            //     {label:"Borrowing", value:<?= $borrow ?>},
            //     {label:"Pending", value:<?= $pending ?>},
            //     {label:"Cancelled", value:<?= $cancel ?>, color:colorDanger}
            // ]
            // });
        })
    </script>
</body>

</html>