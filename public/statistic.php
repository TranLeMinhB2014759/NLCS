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
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

// Initialize empty arrays
$xHotsearch = [];
$yHotsearch = [];

foreach ($rows as $row) {
    $xHotsearch[] = $row['title_name'];
    $yHotsearch[] = $row['searched'];
}

$xHotsearchJSON = json_encode($xHotsearch);
$yHotsearchJSON = json_encode($yHotsearch);

//Total Books
$query = $db->prepare("
    SELECT dausach.title_name, COUNT(quyensach.book_stt) as soLuong
    FROM dausach
    LEFT JOIN quyensach ON dausach.title_id = quyensach.title_id
    GROUP BY dausach.title_name
");
$query->execute();
$book = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/manage.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<style>
    .total-book{
        text-align: left;
        padding: 20px;
    }
</style>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="dashboard.html" class="simple-text">
                        <img src="image/logo.png" alt="logoctu">
                        Quản lý thư viện
                    </a>
                </div>
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.html">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users_student.php">
                            <i class="fa-solid fa-user"></i>
                            <p>Manage Students</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users_teacher.php">
                            <i class="fa-solid fa-user"></i>
                            <p>Manage Teachers</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_titles.php">
                            <i class="fa-solid fa-book"></i>
                            <p>Manage Titles</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_callcard.php">
                            <i class="fa-solid fa-ticket"></i>
                            <p>Manage Call Cards</p>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="statistic.php">
                            <i class="fa-solid fa-chart-pie"></i>
                            <p>Thống kê</p>
                        </a>
                    </li>
                    <li class="nav-item active active-pro">
                        <a class="nav-link active" href="logout.php">
                            <p>Log out</p>
                            <i class="fa fa-sign-out"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <div class="content">
                <h3 class="title-comm"><span class="title-holder">THỐNG KÊ</span></h3>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="text-center">
                                THỐNG KÊ TÀI KHOẢN
                            </div>
                            <canvas id="accounts" style="width:100%;max-width:600px; margin-bottom: 100px"></canvas>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="text-center">
                                THỐNG KÊ PHIẾU MƯỢN
                            </div>
                            <canvas id="callcards" style="width:100%;max-width:600px; margin-bottom: 100px"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-8">
                            <div class="text-center">
                                THỐNG KÊ ĐẦU SÁCH VÀ TÌM KIẾM
                            </div>
                            <canvas id="hotsearch" style="width:100%; margin-bottom: 100px"></canvas>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="text-center">
                                THỐNG KÊ TỔNG SỐ QUYỂN SÁCH CÁC LOẠI
                            </div>
                            <div class="total-book">
                                <?php 
                                foreach ($book as $row) {
                                    echo $row['title_name'] . ": " . $row['soLuong'] . "<br>";
                                }?>
                            </div>
                            <canvas id="hotsearch" style="width:100%; margin-bottom: 100px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--===============================================================================================-->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // ACCOUNTs
            var xAccount = ["Teacher", "Student", "Other"];
            var yAccount = [<?= $teacher ?>, <?= $student ?>, <?= $other ?>];
            var barColors = [
                "#b91d47",
                "#00aba9",
                "#ffffff",
            ];

            new Chart("accounts", {
                type: "doughnut",
                data: {
                    labels: xAccount,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yAccount
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: "Tổng số tài khoản: <?= $student + $teacher + $other ?>"
                    }
                }
            });

            //CALL CARDs
            var xCallCard = ["Pending", "Borrowing", "Returned", "Cancelled"];
            var yCallCard = [<?= $pending ?>, <?= $borrow ?>, <?= $return ?>, <?= $cancel ?>];
            var barColors = [
                "#dcdcdc",
                "#00aba9",
                "#30fc30",
                "#ff0000",
            ];

            new Chart("callcards", {
                type: "doughnut",
                data: {
                    labels: xCallCard,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yCallCard
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: "Tổng số phiếu mượn: <?= $pending + $borrow + $return + $cancel ?>"
                    }
                }
            });

            var xHotsearch = <?php echo $xHotsearchJSON; ?>;
            var yHotsearch = <?php echo $yHotsearchJSON; ?>;
            var barColors = ["red", "green", "blue", "orange", "brown"];

            new Chart("hotsearch", {
                type: "bar",
                data: {
                    labels: xHotsearch,
                    datasets: [{
                        backgroundColor: barColors,
                        data: yHotsearch
                    }]
                },
                options: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: "Tổng số đầu sách có trong thư viện: <?=$title?>"
                    }
                }
            });

        })
    </script>
</body>

</html>