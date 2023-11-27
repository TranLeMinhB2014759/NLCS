<?php
session_start();
include '../partials/db_connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $select_search = $_POST['select_search'];
    $keyword = $_POST['keyword'];

    if ($select_search === 'title_id') {
        $query = $db->prepare('SELECT * FROM dausach WHERE title_id = :keyword ORDER BY title_id');
        $query->bindValue(':keyword', $keyword);

        $query->execute();
        $results = $query->fetchAll();
        $rows = $query->rowCount();
    } elseif ($select_search === 'title_name') {
        $query = $db->prepare('SELECT * FROM dausach WHERE title_name LIKE :keyword ORDER BY title_id');
        $query->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);

        $query->execute();
        $results = $query->fetchAll();
        $rows = $query->rowCount();
    } elseif ($select_search === 'title_author') {
        $query = $db->prepare('SELECT * FROM dausach WHERE title_author LIKE :keyword ORDER BY title_id');
        $query->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);

        $query->execute();
        $results = $query->fetchAll();
        $rows = $query->rowCount();
    }
    elseif ($select_search === 'title_type') {
            $query = $db->prepare('SELECT * FROM dausach WHERE title_type LIKE :keyword ORDER BY title_id');
            $query->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    
            $query->execute();
            $results = $query->fetchAll();
            $rows = $query->rowCount();
    } else {
        $query = $db->prepare('SELECT * FROM dausach WHERE title_id LIKE :keyword OR title_name LIKE :keyword OR title_author LIKE :keyword OR title_type LIKE :keyword ORDER BY title_id');
        $query->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);

        $query->execute();
        $results = $query->fetchAll();
        $rows = $query->rowCount();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Library</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">
    <link rel="stylesheet" href="css/loader.css">
    <!-- <link href="css/DataTables-1.13.6/css/datatables.min.css" rel="stylesheet"> -->
</head>

<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo '<div id="loader-wrapper">
            <div id="loader"></div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>';
    }
    include '../partials/header.php';
    ?>
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
                        <p>The more that you read, the more things you will know. The more that you learn, the more
                            places you’ll go.</p>

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
                                <li style="font-weight:400"><b>Tìm nhanh: </b>Từ khóa bất kì</li>
                                <li style="font-weight:400"><b>Tìm theo đầu sách: </b>Mã số sách</li>
                                <li style="font-weight:400"><b>Tìm tác giả: </b>Tên Tác giả</li>
                                <li style="font-weight:400"><b>Tìm tên sách: </b>Tên sách</li>
                                <li style="font-weight:400"><b>Tìm theo thể loại: </b>Tên thể loại</li>
                            </ul>
                        </div>
                    </div>
                    <div class="group-box">
                        <div class="title text-center" style="font-size: 25px">Hot Search</div>
                        <div class="leftMenu">
                            <ul>
                                <?php
                                $query_title_author = "SELECT * FROM dausach ORDER BY searched DESC LIMIT 6";
                                $title_author = $db->query($query_title_author);
                                $rank = 1;
                                while ($hot_search = $title_author->fetch()) {
                                    echo '<li style="font-weight:400; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden">
                                    <a href="title_detail.php?title_id=' . htmlspecialchars($hot_search["title_id"]) . '"><b>' . $rank . '.</b> ' . htmlspecialchars($hot_search["title_name"]) . '</a>
                                        </li>';
                                    $rank++;
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="mainContent col-sm-9" id="notification">
                    <div class="group-box min-height">
                        <div class="title text-center">Notification</div>
                        <div class="main">

                            <div class="child-container">
                                <div class="form-search">
                                    <form method="POST" id="searchForm">
                                        <div class="search input-group mb-3 mt-3">
                                            <select class="form-select" width="48" id="select_search"
                                                name="select_search">
                                                <option value="*">Tìm nhanh</option>
                                                <option value="title_id">Đầu sách</option>
                                                <option value="title_name">Tên sách</option>
                                                <option value="title_author">Tên tác giả</option>
                                                <option value="title_type">Thể loại</option>
                                            </select>
                                            <input type="text" class="form-control" placeholder="Write Here..."
                                                id="keyword" name="keyword">
                                            <button class="btn btn-primary" type="submit" name="submit"><i
                                                    class="fa-solid fa-magnifying-glass"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <?php
                                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                    if (!empty($keyword)) {
                                        echo '<div class="Content">
                                                    <h5><b>Result:</b></h5> <h4>Có <b style="text-decoration: underline;">' . $rows . '</b> kết quả trùng khớp</h4>
                                                  </div>';
                                        echo '<div class="row result-search">';
                                        if ($rows != 0) {
                                            foreach ($results as $r) {
                                                echo '
                                                        <div id="book" class="book col-sm-6 col-md-4 display">
                                                            <a href="title_detail.php?title_id=' . htmlspecialchars($r["title_id"]) . '" title="Tác Phẩm: ' . htmlspecialchars($r["title_name"]) . '">
                                                                <span class="book-tag" title="Mã số sách">' . htmlspecialchars($r["title_id"]) . '</span>
                                                                <img class="book_img" src="uploads/' . htmlspecialchars($r["title_img"]) . '">
                                                                <h4>' . htmlspecialchars($r["title_name"]) . '</h4>
                                                                <div class="author">
                                                                <h6><b>Tác giả: </b>' . htmlspecialchars($r["title_author"]) . '</h6>
                                                                </div>
                                                            </a>

                                                        </div>';
                                            }
                                        }
                                        echo '</div>';
                                        echo '<script type="text/javascript" src="js/index.js"></script>';
                                    }
                                }
                                ?>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <button onclick="topFunction()" id="myBtn" title="Go to top"><img src="image/toTop.png" alt=""></button>
    <script type="text/javascript" src="js/btnTotop.js"></script>
    <!--===============================================================================================-->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <!--===============================================================================================-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("searchForm").addEventListener("submit", function (event) {
                var keyword = document.getElementById("keyword").value;
                if (keyword.trim() === "") {
                    event.preventDefault();
                    alert("Keyword is required for the search.");
                }
            });
        });
    </script>
</body>
<?php include '../partials/footer.php'; ?>

</html>