<?php
session_start();
include '../partials/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Phone Store</title>
    <link rel="shortcut icon" href="image/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
            <div id="carouselControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselControls" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselControls" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselControls" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="image/img1.png" alt="First slide" class="d-block w-100" height="350px">
                        <div class="carousel-caption d-none d-md-block animationSlide" style="color: black">
                            <strong>Net telephony</strong>
                            <p>We could save a lot with it, but we don't use it.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="image/img2.png" alt="Second slide" class="d-block w-100" height="350px">
                        <div class="carousel-caption d-none d-md-block animationSlide">
                            <strong>Second slide label</strong>
                            <p>Some representative placeholder content for the second slide.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="image/img3.jpg" alt="Third slide" class="d-block w-100" height="350px">
                        <div class="carousel-caption d-none d-md-block animationSlide">
                            <strong>Connect with loved ones</strong>
                            <p>The more developed phones help us to connect, chat like face-to-face communication.</p>
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
        <div class="text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
            <h1>Quản Lý Thư Viện</h1>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
    <script src="js/partials.js"></script>
</body>
<?php include '../partials/footer.php'; ?>

</html>