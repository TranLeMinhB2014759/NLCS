<?php
session_start();
include '../partials/db_connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $select_search = $_POST['select_search'];
    $keyword = $_POST['keyword'];

        if($select_search === 'book_name'){
            $query = $db ->prepare('SELECT * FROM quyensach WHERE book_name LIKE :keyword ORDER BY book_id');
            $query->bindValue(':keyword', '%'.$keyword.'%', PDO::PARAM_STR);
        
            $query->execute();
            $results = $query->fetchAll();
            $rows = $query->rowCount();
        }elseif($select_search === 'book_author'){
            $query = $db ->prepare('SELECT * FROM quyensach WHERE book_author LIKE :keyword ORDER BY book_id');
            $query->bindValue(':keyword', '%'.$keyword.'%', PDO::PARAM_STR);
        
            $query->execute();
            $results = $query->fetchAll();
            $rows = $query->rowCount();
        }else{
            $query = $db ->prepare('SELECT * FROM quyensach WHERE book_name LIKE :keyword OR book_author LIKE :keyword ORDER BY book_id');
            $query->bindValue(':keyword', '%'.$keyword.'%', PDO::PARAM_STR);
        
            $query->execute();
            $results = $query->fetchAll();
            $rows = $query->rowCount();
        // $query->bindParam(':select_search', $select_search);
    }
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
    <!-- <link href="css/DataTables-1.13.6/css/datatables.min.css" rel="stylesheet"> -->
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
                                <li style="font-weight:400"><b>Tìm nhanh: </b>Từ khóa bất kì</li>
                                <li style="font-weight:400"><b>Tìm tác giả: </b>Tên Tác giả</li>
                                <li style="font-weight:400"><b>Tìm tên sách: </b>Tên sách</li>
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
                                    <form method="POST">
                                        <div class="search input-group mb-3 mt-3">
                                            <select class="form-select" width="48" id="select_search" name="select_search">
                                                <option value="*">Tìm nhanh</option>
                                                <option value="book_name">Tên sách</option>
                                                <option value="book_author">Tên tác giả</option>
                                            </select>
                                            <input type="text" class="form-control" placeholder="Write Here..." id="keyword" name="keyword">
                                            <button class="btn btn-primary" type="submit" name="submit">Search <i class="fa-solid fa-magnifying-glass"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <?php
                                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                        if(!empty($keyword)){
                                            echo '<div class="Content">
                                                    <h5><b>Result:</b></h5> <h4>Có <b style="text-decoration: underline;">'. $rows .'</b> kết quả trùng khớp</h4>
                                                  </div>';
                                            echo '<div class="row result-search">';
                                            if($rows != 0){
                                                foreach($results as $r){
                                                    echo '
                                                        <div id="book" class="book col-sm-6 col-md-4 display d  ataTable">
                                                            <a href="book_detail.php?book_id=' . htmlspecialchars($r["book_id"]) . '" title="Tác Phẩm: ' . htmlspecialchars($r["book_name"]) . '">
                                                                <span class="book-tag" title="Mã số sách">'. htmlspecialchars($r["book_id"]) . '</span>
                                                                <img class="book_img" src="uploads/' . htmlspecialchars($r["book_img"]) . '">
                                                                <h4>' . htmlspecialchars($r["book_name"]) . '</h4>
                                                                <div class="author">
                                                                <h6><b>Tác giả: </b>' . htmlspecialchars($r["book_author"]) . '</h6>
                                                                </div>
                                                            </a>

                                                        </div>';                                     
                                                    }
                                            }
                                            echo'</div>';
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

    </div>
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <!--===============================================================================================-->
    <!-- <script src="js/DataTables-1.13.6/js/datatables.min.js"></script> -->

    <!-- <script type="text/javascript">
        $(document).ready(function(){
            $('#book').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': 'book.php'
                },
                'columns': [
                    {data: 'book_id'},
                    {data: 'book_name'},
                    {data: 'book_author'},
                    {data: 'book_img'},
                ]
            });
        });
    </script> -->
</body>
<?php include '../partials/footer.php'; ?>

</html>