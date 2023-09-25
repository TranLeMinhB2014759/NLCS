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

// Lấy book_stt
$query_b = "SELECT * FROM quyensach WHERE title_id = :title_id";
$book = $db->prepare($query_b);
$book->bindParam(':title_id', $_SESSION['title']['title_id']);
$book->execute();

$data = [];
while ($row_stt = $book->fetch(PDO::FETCH_ASSOC)) {
    $data[] = array(
        'book_stt' => $row_stt['book_stt'],
        'book_status' => $row_stt['book_status'],
        'title_id' => $row_stt['title_id'],
    );
}

//Mượn sách
$currentDate = date('d-m-Y');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$user_id = $_POST['user_id'];
    $title_id = $_POST['title_id'];
    $book_stt = $_POST['book_stt'];
    $book_return_date = date("d-m-Y", strtotime($_POST['book_return_date']));

	$stmt = $db->prepare('
				INSERT INTO phieumuon (user_id, title_id, book_stt, pm_ngaymuon, pm_ngayhentra, trangthai)
				VALUES (:user_id, :title_id, :book_stt, :pm_ngaymuon, :pm_ngayhentra, :trangthai)
			    ');
	$stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title_id', $title_id);
    $stmt->bindParam(':book_stt', $book_stt);
    $stmt->bindParam(':pm_ngaymuon', $currentDate);
    $stmt->bindParam(':pm_ngayhentra', $book_return_date);
    $stmt->bindValue(':trangthai', "");

	$stmt->execute();
    echo '<script>
        alert("Mượn sách thành công");
    </script>';
    header("Location: " . $_SERVER['HTTP_REFERER']);

    $query_update = 'UPDATE quyensach SET book_status=? WHERE book_stt=? AND title_id=?';
    $stmt_update = $db->prepare($query_update);
    $stmt_update->execute([
        0,
        $_POST['book_stt'],
        $_POST['title_id'],
    ]);
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
                                <?php echo '<b>Mã số sách: </b>' . htmlspecialchars($row['title_id']) . ''; ?>
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
                            <?php endif ?>
                            <?php if (isset($_SESSION['user'])): ?>
                                <?php if ($_SESSION['user']['sdt'] == 0 && $_SESSION['user']['role'] != 1 || $_SESSION['user']['email'] == 0 && $_SESSION['user']['role'] != 1): ?>
                                    <h5 style="color: red;">Hãy cập nhật thông tin đầy đủ để có thể mượn sách!</h5>
                                <?php endif ?>
                                <?php if ($_SESSION['user']['sdt'] != 0 && $_SESSION['user']['email'] != 0 && $_SESSION['user']['role'] != 1): ?>
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
                                                <form action="" id="pm" class="form-horizontal" method="POST">

                                                <div class="mb-3 mt-3">
                                                    <label for="user_id" class="form-label">
                                                        User_ID:
                                                    </label>
                                                    <input class="form-control" disabled value="<?php echo $_SESSION['user']['id'] ?>"></input>
                                                    <input  id="user_id" name="user_id" hidden value="<?php echo $_SESSION['user']['id'] ?>"></input>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="book_stt" class="form-label">
                                                        Mã số sách
                                                    </label>

                                                    <select class="form-select" name="book_stt" id="book_stt" required>
                                                        <option value="">------- Chọn mã số sách -------</option>
                                                        <?php foreach ($data as $book): ?>
                                                            <?php if($book['book_status'] == "1") :?>
                                                                <option value="<?= $book['book_stt']?>"><?= $_SESSION['title']['title_id'].$book['book_stt']?></option>
                                                            <?php else:?>
                                                                <option value="">Không còn sách tại thư viện</option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                        <input id="title_id" name="title_id" hidden value="<?= $_SESSION['title']['title_id']?>"></input>
                                                    </select>

                                                </div>

                                                <div class="row">
                                                    <div class="mb-3 col-6">
                                                        <label for="currentDate" class="form-label">
                                                            Ngày mượn:
                                                        </label>
                                                        <input type="date" class="form-control" id="currentDate" name="currentDate" value="<?php echo $currentDate;?>" hidden></input>
                                                        <input class="form-control" value="<?php echo $currentDate;?>" disabled></input>
                                                    </div>
                                                    <div class="mb-3 col-6">
                                                        <label for="book_return_date" class="form-label">
                                                            Ngày hẹn trả:
                                                        </label>
                                                        <input type="date" class="form-control" id="book_return_date" name="book_return_date"></input>
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
                'Mã Số Sách: ' . htmlspecialchars($row['title_id']) . '\n' .
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