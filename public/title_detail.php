<?php
session_start();
include '../partials/db_connect.php';

//Kiểm tra xem title_id có tồn tài hay không
if (isset($_GET['title_id'])) {
    $_SESSION['title']['title_id'] = $_GET['title_id'];

    $query_check_id = "SELECT title_id, searched FROM dausach WHERE title_id = :giaTri";
    $title_check_id = $db->prepare($query_check_id);
    $title_check_id->bindParam(':giaTri', $_SESSION['title']['title_id'], PDO::PARAM_STR);
    $title_check_id->execute();
    
    //Nếu có title_id thì lấy dữ liệu
    if($title_check_id->rowCount() > 0 ){
        $query_title = "SELECT * FROM dausach WHERE title_id={$_SESSION['title']['title_id']}";
        $title = $db->query($query_title);
        $row = $title->fetch();
        if (isset($_SERVER['HTTP_REFERER']) && stripos($_SERVER[ 'HTTP_REFERER' ], $_SERVER['REQUEST_URI']) !== true){
            $current_searched = $row['searched'];
            $new_searched = $current_searched + 1;

            $query_searched = "UPDATE dausach SET searched=:searched WHERE title_id={$_SESSION['title']['title_id']}";
            $stmt = $db->prepare($query_searched);
            $stmt->bindParam(':searched', $new_searched, PDO::PARAM_INT);
            $stmt->execute();
        }
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
                            <?php if (!isset($_SESSION['user'])): ?>
                                <a id="button" href="login.php">
                                    Đăng nhập để mượn sách
                                    <div class="arrow-wrapper">
                                        <div class="arrow"></div>
                                    </div>
                                </a>
                            <?php else: ?>
                                <a id="button" href="profile.php#tab2">
                                    Mượn sách
                                    <div class="arrow-wrapper">
                                        <div class="arrow"></div>
                                    </div>
                                </a>
                            <?php endif ?>

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
            <h3><strong>HOT SEARCH</strong></h3>
            <?php 
            echo '<div id="book" class="gallery">';
            $query_title_author = "SELECT * FROM dausach ORDER BY searched DESC LIMIT 4";
            $title_author = $db->query($query_title_author);

            while ($rows = $title_author->fetch())  {
                echo '  <div class="card">
                            <a href="title_detail.php?title_id=' . htmlspecialchars($rows["title_id"]) . '">
                                <figure>
                                    <img class="book_img img-fluid" src="uploads/' . htmlspecialchars($rows["title_img"]) . '">
                                </figure>
                                <figcaption>
                                    ' . htmlspecialchars($rows["title_name"]) . '
                                </figcaption>
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