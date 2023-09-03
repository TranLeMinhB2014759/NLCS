<!-- Site footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <h6>About</h6>
                <p class="text-justify"><b>Thư Viện Khoa CNTT-TT</b></p>
                <p class="text-justify">Khu II - Đường 3/2 - Q.Ninh Kiều - TP.Cần Thơ</p>
            </div>

            <div class="col-xs-6 col-md-3">
                <!-- Grid-between  -->
            </div>

            <div class="col-xs-6 col-md-3">
                <h6>Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="../">Homepage</a></li>
                    <?php if (!isset($_SESSION['user'])): ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif ?>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 0): ?>
                        <li class="nav-item"><a class="nav-link" href="giohang.php">Giỏ hàng</a></li>
                    <?php endif ?>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1): ?>
                        <li><a href="manage_posts.php">Manage Posts</a></li>
                        <li><a href="manage_accounts.php">Manage Accounts</a></li>
                    <?php endif ?>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="logout.php">Logout &nbsp <i class="fa-solid fa-right-from-bracket"></i></a></li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
        <hr>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-6 col-xs-12">
                <p class="copyright-text">Copyright &copy; 2023 All Rights Reserved by
                    <a href="#">Master Minh</a>.
                </p>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <ul class="social-icons">
                    <li><a class="facebook" href="#"><i class="fa-brands fa-facebook"></i></a></li>
                    <li><a class="twitter" href="#"><i class="fa-brands fa-twitter"></i></a></li>
                    <li><a class="github" href="#"><i class="fa-brands fa-github"></i></a></li>
                    <li><a class="linkedin" href="#"><i class="fa-brands fa-linkedin"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>