<?php
session_start();
include '../partials/db_connect.php';

//Kiểm tra xem book_id có tồn tài hay không
if (isset($_GET['book_id'])) {
    $_SESSION['book']['book_id'] = $_GET['book_id'];

    $query_check_id = "SELECT book_id FROM quyensach WHERE book_id = :giaTri";
    $book_check_id = $db->prepare($query_check_id);
    $book_check_id->bindParam(':giaTri', $_SESSION['book']['book_id'], PDO::PARAM_STR);
    $book_check_id->execute();
    
    //Nếu có book_id thì lấy dữ liệu
    if($book_check_id->rowCount() > 0 ){
        $query_book = "SELECT * FROM quyensach WHERE book_id={$_SESSION['book']['book_id']}";
        $book = $db->query($query_book);
        $row = $book->fetch();
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$username = $_POST['username'];
	$name = $_POST['name'];
	$sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $masach = $_POST['masach'];
    $tensach = $_POST['tensach'];
    $currentDate = date('d/m/Y');
    $book_return_date = date('d/m/Y', strtotime("+20 days"));

	$stmt = $db->prepare('
				INSERT INTO phieumuon (username, name, sdt, email, masach, tensach, pm_ngaymuon, pm_ngayhentra)
				VALUES (:username, :name, :sdt, :email, :masach, :tensach, :pm_ngaymuon, :pm_ngayhentra)
			    ');
	$stmt->bindParam(':username', $username);
	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':sdt', $sdt);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':masach', $masach);
    $stmt->bindParam(':tensach', $tensach);
    $stmt->bindParam(':pm_ngaymuon', $currentDate);
    $stmt->bindParam(':pm_ngayhentra', $book_return_date);

	$stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
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
                            <?php if (!isset($_SESSION['user'])): ?>
                                <a id="button" href="login.php">
                                    Đăng nhập để mượn sách
                                    <div class="arrow-wrapper">
                                        <div class="arrow"></div>

                                    </div>
                                </a>
                            <?php endif ?>
                            <?php if (isset($_SESSION['user'])): ?>
                                <?php if ($_SESSION['user']['sdt'] == 0 || $_SESSION['user']['email'] == 0): ?>
                                    <h5 style="color: red;">Hãy cập nhật thông tin đầy đủ để có thể mượn sách!</h5>
                                <?php endif ?>
                                <?php if ($_SESSION['user']['sdt'] != 0 && $_SESSION['user']['email'] != 0): ?>
                                <button data-bs-toggle="modal" data-bs-target="#modal" title="Đăng kí mượn sách">
                                    Mượn Sách
                                    <div class="arrow-wrapper">
                                        <div class="arrow"></div>

                                    </div>
                                </button>
                                    <!-- The Modal -->
                                    <div class="modal fade" id="modal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h2 class="modal-title">Phiếu mượn sách</h2>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal">
                                                </button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <form action="" id="signupForm" class="form-horizontal" method="POST">
                                                <div class="row">
                                                    <div class="col mb-3">
                                                        <label for="username" class="form-label">
                                                            Username: 
                                                        </label>
                                                        <input class="form-control" disabled value="<?php echo $_SESSION['user']['username'] ?>"></input>
                                                        <input  id="username" name="username" hidden value="<?php echo $_SESSION['user']['username'] ?>"></input>
                                                    </div>
                                                    <div class="col mb-3">
                                                        <label for="name" class="form-label">
                                                            First and last name:
                                                        </label>
                                                        <input class="form-control" disabled value="<?php echo $_SESSION['user']['name'] ?>"></input>
                                                        <input  id="name" name="name" hidden value="<?php echo $_SESSION['user']['name'] ?>"></input>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-8 mb-3">
                                                        <label for="name" class="form-label">
                                                            Email:
                                                        </label>
                                                        <input class="form-control" disabled value="<?php echo $_SESSION['user']['email'] ?>"></input>
                                                        <input id="email" name="email" hidden value="<?php echo $_SESSION['user']['email'] ?>"></input>
                                                    </div>
                                                    <div class="col-4 mb-3">
                                                        <label for="sdt" class="form-label">
                                                            Phone Number:
                                                        </label>
                                                        <input class="form-control" disabled value="<?php echo "0" . $_SESSION['user']['sdt'] ?>"></input>
                                                        <input id="sdt" name="sdt" hidden value="<?php echo "0" . $_SESSION['user']['sdt'] ?>"></input>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3 mb-3">
                                                        <label for="sdt" class="form-label">
                                                            Mã sách:
                                                        </label>
                                                        <input class="form-control" disabled value="<?php echo $_SESSION['book']['book_id'] ?>"></input>
                                                        <input id="masach" name="masach" hidden value="<?php echo $_SESSION['book']['book_id'] ?>"></input>
                                                    </div>
                                                    <div class="col-8 mb-3">
                                                        <label for="sdt" class="form-label">
                                                            Tên sách:
                                                        </label>
                                                        <input class="form-control" disabled value="<?php echo $row['book_name'] ?>"></input>
                                                        <input id="tensach" name="tensach" hidden value="<?php echo $row['book_name'] ?>"></input>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">
                                                    OK
                                                </button>
                                                </form><!-- Vì một vài lí do của bs5 nên phải đển form ở đây-->
                                                <button class="btn btn-danger" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif ?>

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
            <h3><strong>MỚI NHẤT</strong></h3>
            <?php 
            echo '<div id="book" class="gallery">';
            $query_book_author = "SELECT * FROM quyensach ORDER BY book_id DESC LIMIT 4";
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