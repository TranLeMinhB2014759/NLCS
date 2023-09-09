<?php
session_start();
include '../partials/db_connect.php';

//Lấy dữ liệu
if (isset($_GET['book_id'])) {
    $_SESSION['book']['book_id'] = $_GET['book_id'];
}
$query_book = "SELECT * FROM quyensach WHERE book_id={$_SESSION['book']['book_id']}";
$book = $db->query($query_book);
$row = $book->fetch();

$permission = 0;
//Xét có đăng nhập chưa
// if (isset($_SESSION['user'])) {
//     $permission = 1;
//     $user_id = $_SESSION['user']['id'];
//     $phone_id = $_SESSION['phone']['phone_id'];
// }

// //Kiểm tra điều kiện gửi comment
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $comment = $_POST['frm_comment'];
//     if ($permission != 1) {
//         echo ' <script>alert("Bạn chưa đăng nhập để bình luận")</script>';
//     } else {
//         if (empty($comment)) {
//             echo ' <script>alert("Bình luận để trống")</script>';
//         } else {
//             $opinion = $_POST['frm_comment'];
//             $sk = $db->prepare('INSERT INTO comment (user_id, phone_id, opinion) VALUES (:user_id, :phone_id, :opinion);');
//             $sk->bindParam(':user_id', $user_id);
//             $sk->bindParam(':phone_id', $phone_id);
//             $sk->bindParam(':opinion', $opinion);

//             $sk->execute();
//         }
//     }
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php echo htmlspecialchars($row['book_name']) ?> - Libary
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
    <!-- <div id="loader-wrapper">
        <div id="loader"></div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>
    </div> -->
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <div class="book-detail-container">
            <h3>TỔNG QUAN</h3>
            <div class="book-detail row">
                <div class="img-book col-md-5 col-sm-12">
                    <?php echo '<img src="uploads/' . htmlspecialchars($row['book_img']) . '" alt="">'; ?>
                </div>
                <div class="info-book col-md-7 col-sm-12">
                    <div class="row">
                        <div class="col-md-9">
                            <strong>
                                <?php echo '' . htmlspecialchars($row['book_name']) . ''; ?>
                            </strong>
                            <br>
                            <h4>
                                <?php echo '<b>Mã số sách: </b>' . htmlspecialchars($row['book_id']) . ''; ?>
                            </h4>
                            <h4>
                                <?php echo '<b>Tác giả: </b>' . htmlspecialchars($row['book_author']) . ''; ?>
                            </h4>
                            <h4>
                                <?php echo '<b>Thể loại: </b>' . htmlspecialchars($row['book_type']) . ''; ?>
                            </h4>
                            <h4>
                                <?php echo '<b>Xuất bản năm: </b>' . htmlspecialchars($row['book_year']) . ''; ?>
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
            <h3>GỢI Ý</h3>
            <?php 
            echo '<div id="book" class="gallery">';
            $query_book_author = "SELECT * FROM quyensach";
            $book_author = $db->query($query_book_author);

            while ($rows = $book_author->fetch())  {
                echo '  <div class="card">
                            <a href="book_detail.php?book_id=' . htmlspecialchars($rows["book_id"]) . '">
                                <figure>
                                    <img class="book_img img-fluid" src="uploads/' . htmlspecialchars($rows["book_img"]) . '">
                                <figure>
                                <figcaption>
                                    ' . htmlspecialchars($rows["book_name"]) . '
                                <figcaption>
                            </a>
                        </div>';
            }
            echo '</div>';
            ?>
        </div>
    </div>
    <br>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/convert_en.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.js"></script>
    <script>
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: convert("<?php
            echo htmlspecialchars($row['book_name']) . '\n' .
                'Mã Số Sách: ' . htmlspecialchars($row['book_id']) . '\n' .
                'Tác giả: ' . htmlspecialchars($row['book_author']) . '\n' .
                'Thể loại: ' . htmlspecialchars($row['book_type']) . '\n' .
                'Xuất bản năm: ' . htmlspecialchars($row['book_year']) . '';
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