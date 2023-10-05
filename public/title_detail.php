<?php
session_start();
include '../partials/db_connect.php';

//Kiểm tra xem title_id có tồn tài hay không
if (isset($_GET['title_id'])) {
    $_SESSION['title']['title_id'] = $_GET['title_id'];

    $query_check_id = "SELECT title_id FROM dausach WHERE title_id = :giaTri";
    $title_check_id = $db->prepare($query_check_id);
    $title_check_id->bindParam(':giaTri', $_SESSION['title']['title_id'], PDO::PARAM_STR);
    $title_check_id->execute();
    
    //Nếu có title_id thì lấy dữ liệu
    if($title_check_id->rowCount() > 0 ){
        $query_title = "SELECT * FROM dausach WHERE title_id={$_SESSION['title']['title_id']}";
        $title = $db->query($query_title);
        $row = $title->fetch();
    } else {
        echo '<script>
        var confirmation = confirm("Mã sách bạn tìm không có trong cơ sở dữ liệu");
        if (confirmation) {
            window.location.href = "../";
        }else{
            window.location.href = "../";
        }
        </script>';
    }
} else{
    header('location: ../');
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php echo htmlspecialchars($row['title_name']) ?> - Libary
    </title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/loader.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">
    <link rel="stylesheet" href="css/book-detail.css">
</head>

<body>
    <div id="loader-wrapper">
        <div id="loader"></div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>
    </div>
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <div class="book-detail-container">
            <h3><strong>TỔNG QUAN</strong></h3>
            <div class="book-detail row">
                <div class="img-book col-md-5 col-sm-12">
                    <?php echo '<img src="uploads/' . htmlspecialchars($row['title_img']) . '" alt="">'; ?>
                </div>
                <div class="info-book col-md-7 col-sm-12">
                    <div class="row">
                        <div class="col-md-9">
                            <strong>
                                <?php echo '' . htmlspecialchars($row['title_name']) . ''; ?>
                            </strong>
                            <br>
                            <h4>
                                <?php echo '<b>Mã số đầu sách: </b>' . htmlspecialchars($row['title_id']) . ''; ?>
                            </h4>
                            <h4>
                                <?php echo '<b>Tác giả: </b>' . htmlspecialchars($row['title_author']) . ''; ?>
                            </h4>
                            <h4>
                                <?php echo '<b>Thể loại: </b>' . htmlspecialchars($row['title_type']) . ''; ?>
                            </h4>
                            <h4>
                                <?php echo '<b>Xuất bản năm: </b>' . htmlspecialchars($row['title_year']) . ''; ?>
                            </h4>
                        </div>
                        <div class="col-md-3" id="qrcode"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="same-book-author">
            <h3><strong>MỚI NHẤT</strong></h3>
            <?php 
            echo '<div id="book" class="gallery">';
            $query_title_author = "SELECT * FROM dausach ORDER BY title_id DESC LIMIT 4";
            $title_author = $db->query($query_title_author);

            while ($rows = $title_author->fetch())  {
                echo '  <div class="card">
                            <a href="title_detail.php?title_id=' . htmlspecialchars($rows["title_id"]) . '">
                                <figure>
                                    <img class="book_img img-fluid" src="uploads/' . htmlspecialchars($rows["title_img"]) . '">
                                <figure>
                                <figcaption>
                                    ' . htmlspecialchars($rows["title_name"]) . '
                                <figcaption>
                            </a>
                        </div>';
            }
            echo '</div>';
            ?>
        </div>
    </div>
    <br>
    
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.validator.addMethod("checkDate", function(value, element) {
                var inputDate = new Date(value);
                var currentDate = new Date();
                var diffInDays = Math.floor((inputDate - currentDate) / (1000 * 60 * 60 * 24));
                
                return diffInDays <= 60;
                }, "Ngày không được vượt quá 60 ngày sau ngày hiện tại.");
            $.validator.addMethod("dateAfterToday", function(value, element) {
                var currentDate = new Date();
                var inputDate = new Date(value);

                return inputDate > currentDate;
                }, "Ngày hẹn trả phải sau ngày hiện tại");
                    $("#pm").validate({
            rules: {
                book_return_date: {
                    required: true,
                    date: true,
                    checkDate: true,
                    dateAfterToday: true,
                },
            },
            messages: {
                book_return_date: {
                required: "Vui lòng chọn ngày",
                date: "Ngày không hợp lệ",
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
    <!-- QR CODE -->
    <script type="text/javascript" src="js/convert_en.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.js"></script>
    <script>
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: convert("<?php
            echo htmlspecialchars($row['title_name']) . '\n' .
                'Mã số đầu sách: ' . htmlspecialchars($row['title_id']) . '\n' .
                'Tác giả: ' . htmlspecialchars($row['title_author']) . '\n' .
                'Thể loại: ' . htmlspecialchars($row['title_type']) . '\n' .
                'Xuất bản năm: ' . htmlspecialchars($row['title_year']) . '';
            ?>"),
            width: 100,
            height: 100,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
    <script>
        const loaded = document.querySelector("body");
        loaded.classList.add("loaded");
    </script>
</body>
<?php include '../partials/footer.php'; ?>

</html>