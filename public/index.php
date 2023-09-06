<?php
session_start();
include '../partials/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $select_search = $_POST['select_search'];
    $content_search = $_POST['content_search'];
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
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <div id="carouselControls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselControls" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselControls" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselControls" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="image/img1.png" alt="First slide" class="d-block w-100" height="350px">
                    <div class="carousel-caption d-none d-md-block animationSlide">

                            <strong>Diverse book store</strong>
                            <p>The more that you read, the more things you will know. The more that you learn, the more places you’ll go.</p>

                    </div>
                </div>
                <div class="carousel-item">
                    <img src="image/img2.png" alt="Second slide" class="d-block w-100" height="350px">
                    <div class="carousel-caption d-none d-md-block animationSlide">
                        <strong>Study space</strong>
                        <p>The space is quiet, airy, and has a computer system to support searching.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="image/img3.jpg" alt="Third slide" class="d-block w-100" height="350px">
                    <div class="carousel-caption d-none d-md-block animationSlide">
                        <strong>Professional document</strong>
                        <p>Helps you easily research projects.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
        </div>

        <div class="main-container">
            <div class="row">
                <div class="col-sm-3" id="leftSide">
                    <div class="group-box">
                        <div class="title text-center" style="font-size: 25px">Search Guide</div>
                        <div class="leftMenu">
                            <ul>
                                <li style="font-weight:400"><b>Tìm nhanh:</b> Tìm trong Tên tài liệu, Tác giả, Năm xuất
                                    bản, Từ khóa</li>
                                <li style="font-weight:400"><b>Tìm đơn giản:</b> Tìm theo Loại tài liệu, Từ khóa, Tên
                                    tài liệu, Tác giả, Năm xuất bản</li>
                                <li style="font-weight:400"><b>Tìm nâng cao:</b> Tìm theo theo toán tử AND, OR, NOT</li>
                                <li style="font-weight:400"><b>Tìm liên thư viện:</b> Tìm tài liệu ở thư viện liên kế
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mainContent col-sm-9">
                    <div class="group-box min-height">
                        <div class="title text-center">Notification</div>
                        <div class="main">

                            <div class="child-container">
                                <div class="form-search">
                                    <form action="index.php" method="post">
                                        <div class="search input-group mb-3 mt-3">
                                            <select class="form-select" width="48" id="select_search" name="select_search">
                                                <option value="*">Tìm nhanh</option>
                                                <option value="nhande">Nhan đề</option>
                                                <option value="tacgia">Tên tác giả</option>
                                            </select>
                                            <input type="text" class="form-control" placeholder="Write Here..." id="content_search" name="content_search">
                                            <button class="btn btn-primary" type="submit">Search <i class="fa-solid fa-magnifying-glass"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <hr>
                                <div class="Content"><b>Result:</b>
                                <!-- ?php
                                    $query = "SELECT * FROM quyensach WHERE tacgia = :select_search LIKE content_search;";
                                    $ch = $db->query($query);
                                    while ($row = $ch->fetch()) {
                                        echo '<div id="book" class="col-sm-6 col-md-3 ">
                                                <a href="book_detail.php?book_id=' . htmlspecialchars($row["book_id"]) . '"><img class="img-fluid img-product" src="' . htmlspecialchars($row["book_file_name"]) . '">
                                                <h3>' . htmlspecialchars($row["book_name"]) . '</h3>
                                            </div>';
                                    }
                                ?> -->
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script src="js/partials.js"></script>
</body>
<?php include '../partials/footer.php'; ?>

</html>