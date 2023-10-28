<?php
session_start();
include '../partials/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support</title>
    <link rel="shortcut icon" href="image/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-5.3.0-alpha3-dist/bootstrap.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/partials.css">
    <link rel="stylesheet" href="css/help.css">
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="container mt-3 bg-primary text-white rounded text-center">
        <h1 class="big-title"><strong>CHÍNH SÁCH BẠN ĐỌC</strong></h1>
    </div>
    <div class="main container mt-3 article-content">
        <h1 class="system-pagebreak">
            <strong>Bạn đọc trong trường ĐHCT<br></strong>
        </h1>
        <div id="accordion">
            <div class="card">
                <div class="card-header">
                    <a class="btn" data-bs-toggle="collapse" href="#collapseOne">
                        <strong>Sinh viên đại học</strong>
                    </a>
                    <div id="collapseOne" class="collapse" data-bs-parent="#accordion">
                        <div class="card-body">
                            <p>
                                Lệ phí làm thẻ là 50.000 đồng. Số lượng sách được mượn là 5 quyển/2
                                tuần và được gia hạn một tuần tiếp theo nếu sách không có nhu cầu cao. Ngoài ra sinh
                                viên được sử dụng
                                cơ sở vật chất, tất cả các dịch vụ và các nguồn tài liệu của TTHL.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwo">
                        <strong>Giảng viên</strong> 
                    </a>
                </div>
                <div id="collapseTwo" class="collapse" data-bs-parent="#accordion">
                    <div class="card-body">
                    <p>
                        TTHL cấp thẻ miễn phí cho cán bộ viên chức do
                        trường trả lương với tất cả trường hợp có quyết định của Hiệu trưởng. Đổi thẻ miễn phí cho công chức,
                        viên chức, người lao động 1 lần/năm do thẻ cũ, nhòe, không rõ, hư chíp, mất thẻ hoặc thay đổi vị trí
                        công tác, có xác nhận của trưởng đơn vị. Số lượng sách được mượn của công chức, viên chức, ngưòi lao
                        động là 10 quyển/8 tuần và được gia hạn một tuần tiếp theo nếu sách không có nhu cầu cao. Ngoài ra, TTHL
                        phục vụ giảng viên, cán bộ viên chức, học viên sau đại học phòng Suy ngẫm về cơ sở vật chất, nguồn tài
                        liệu và các dịch vụ của TTHL. Đặc biệt, TTHL cung cấp Dịch vụ Thư viện văn phòng và Dịch vụ Cung cấp
                        thông tin học thuật.
                    </p>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="system-pagebreak">
            <strong>Bạn đọc ngoài trường ĐHCT<br></strong>
        </h1>
        <div id="accordion">
            <div class="card">
                <div class="card-header">
                    <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseThree">
                        <strong>Bạn đọc ngoài trường ĐHCT</strong>
                    </a>
                </div>
                <div id="collapseThree" class="collapse" data-bs-parent="#accordion">
                    <div class="card-body">
                        <p>
                            Đối với bạn đọc ngoài trường ĐHCT, chỉ có thể tham khảo sách thực tiếp tại thư viện.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <h1 class="system-pagebreak">
            <strong>Hướng dẫn mượn sách<br></strong>
        </h1>
        <div id="accordion">
            <div class="card">
                <div class="card-header">
                    <a class="btn" data-bs-toggle="collapse" href="#collapseFour">
                        <strong>Hướng dẫn mượn sách</strong>
                    </a>
                    <div id="collapseFour" class="collapse" data-bs-parent="#accordion">
                        <div class="card-body">
                            Để mượn sách trong thư viện Khoa CNTT-TT, bạn có thể làm theo các hướng dẫn sau:<br>
                            <b>Bước 1</b> <br>
                                Truy cập địa chỉ <a href="profile.php#tab2">Tại đây</a> hoặc Click vào Avatar <i class="fa-solid fa-arrow-right"></i> Profile <i class="fa-solid fa-arrow-right"></i> Chờ xử lý, sau đó nhấn nút "Mượn sách".
                            <br><br>
                            <b>Bước 2</b> <br>
                                Chọn mã số đầu sách cần mượn, sau đó chọn mã số sách:<br>
                                Lưu ý:
                                <li>Nếu không thấy mã số sách, có nghĩa là thư viện đã hết loại sách bạn cần.</li>
                                <li>Được mượn tối đa 5 quyển / lần. Các đầu sách không được trùng nhau.</li>
                                <div class="img"><img id="img" src="image/b2.1.png"></div>
                            <br>
                            <b>Bước 3</b><br>
                                Chọn "OK" và đợi cán bộ thư viện chấp nhận.
                           <br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-5.3.0-alpha3-dist/bootstrap.bundle.min.js"></script>
</body>
<?php include '../partials/footer.php'; ?>

</html>