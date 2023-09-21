<?php
session_start();
include '../partials/db_connect.php';
include '../partials/check_admin.php';
if (isset($_GET['pm_stt'])) {
    $query = 'UPDATE phieumuon SET trangthai=? WHERE pm_stt=?';
    $stmt = $db->prepare($query);
    $stmt->execute([
        $_GET['trangthai'],
        $_GET['pm_stt']
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Libary</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/partials.css">
    <link rel="stylesheet" href="css/loader.css">
    <link rel="stylesheet" href="css/manage.css">
    <!-- <link href="css/DataTables-1.13.6/css/datatables.min.css" rel="stylesheet"> -->
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="title">
        QUẢN LÝ PHIẾU MƯỢN SÁCH
    </div>
    <div class="container-m">
        <table>
            <tr>
                <th>Số thứ tự</th>
                <th>Tên người mượn</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Tên quyển sách</th>
                <th>Ngày mượn</th>
                <th>Ngày hẹn trả</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
            <?php $query = 'SELECT * FROM phieumuon;';
            $results = $db->query($query);

            $data = [];
            while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                $data[] = array(
                    'pm_stt' => $row['pm_stt'],
                    'name' => $row['name'],
                    'sdt' => $row['sdt'],
                    'email' => $row['email'],
                    'tensach' => $row['tensach'],
                    'pm_ngaymuon' => $row['pm_ngaymuon'],
                    'pm_ngayhentra' => $row['pm_ngayhentra'],
                    'trangthai' => $row['trangthai'],
                );
            }
            ?>
            <?php foreach ($data as $pm): ?>
                <tr>
                    <td>
                        <?= $pm['pm_stt'] ?>
                    </td>
                    <td>
                        <?= $pm['name'] ?>
                    </td>
                    <td>
                        0<?= $pm['sdt'] ?>
                    </td>
                    <td>
                        <?= $pm['email'] ?>
                    </td>
                    <td>
                        <?= $pm['tensach'] ?>
                    </td>
                    <td>
                        <?= $pm['pm_ngaymuon'] ?>
                    </td>
                    <td>
                        <?= $pm['pm_ngayhentra'] ?>
                    </td>
                    <?php if($pm['trangthai'] == ''):?>
                        <td><a href="manage_callcard.php?pm_stt=<?= $pm['pm_stt'] ?>&trangthai=0" class='btn btn-primary' id='btn_confirm'>Xác nhận</a></td>
                        <td><a href="manage_callcard.php?pm_stt=<?= $pm['pm_stt'] ?>&trangthai=2" class='btn btn-danger' id='btn_delete'>Hủy</a></td>
                    <?php elseif($pm['trangthai'] == 0):?>
                        <td><a href="manage_callcard.php?pm_stt=<?= $pm['pm_stt'] ?>&trangthai=1" class='btn btn-success'  id='btn_returned'>Trả sách</a></td>
                        <td><a href="manage_callcard.php?pm_stt=<?= $pm['pm_stt'] ?>&trangthai=2" class='btn btn-danger' id='btn_delete'>Hủy</a></td>
                    <?php elseif($pm['trangthai'] == 1):?>
                        <td style="color: green">Đã trả</td>
                        <td></td>
                    <?php elseif($pm['trangthai'] == 2):?>
                        <td style="color: red">Đã hủy</td>
                        <td></td>
                    <?php endif;?>
                <tr>
                <?php endforeach; ?>
            </tr>
        </table>
    </div>
    <button onclick="topFunction()" id="myBtn" title="Go to top"><img src="image/toTop.png" alt=""></button>
    <!--===============================================================================================-->
    <!-- <script type="text/javascript" src="js/index.js"></script> -->
    <!--===============================================================================================-->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <!--===============================================================================================-->
    <script>
        //Duyêt
        $("a#btn_confirm").click(function () {
            if (confirm('Bạn bạn chấp nhận phiếu mượn này?')) {
                alert("Xác nhận thành công");
                return true;
            }
            return false;
        });
        //Xác nhận trả
        $("a#btn_returned").click(function () {
            if (confirm('Bạn xác nhận người mượn đã trả sách?')) {
                alert("Xác nhận thành công");
                return true;
            }
            return false;
        });
        //Hủy
        $("a#btn_delete").click(function () {
            if (confirm('Bạn chắc chắn muốn hủy yêu cầu này không?')) {
                alert("Đã hủy thành công");
                return true;
            }
            return false;
        });
    </script>
    <script>
        // Button SrolltoTop
        window.onscroll = function () { scrollFunction() };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 300) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
</body>

</html>