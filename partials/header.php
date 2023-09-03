<header>
    <div class="container">
            <nav class="navbar navbar-expand-sm navbar-dark">
            <div class="logo">
                <img src="image/logo.png" alt="Logo" style="width:80px; height:80px;">
                <div class="title-logo">
                    <h2>Thư Viện Khoa CNTT-TT</h2>
                    <h1>Đại học Cần Thơ</h1>
                </div>
            </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse flex-row-reverse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Homepage</a></li>
                        <?php if (!isset($_SESSION['user'])): ?>
                            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                        <?php endif ?>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1): ?>
                            <li class="nav-item"><a class="nav-link" href="manage_posts.php">Manage Posts</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_accounts.php">Manage Accounts</a></li>
                        <?php endif ?>
                        <?php if (isset($_SESSION['user'])): ?>
                            <!-- Dropdown -->
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" id="navbardrop" data-bs-toggle="dropdown">

                                    <img class="rounded-circle" style="width:30px; height:30px;"
                                        src="avatar/<?php echo $_SESSION['user']['avatar'] ?>" alt="Ảnh người dùng">&nbsp

                                    <?php echo $_SESSION['user']['name'] ?>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="profile.php">Profile</i></a>
                                    <!-- <a class="dropdown-item" href="#">Manage comments</i></a> -->
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout.php">Logout &nbsp <i class="fa fa-sign-out"></i></a>
                                </div>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
            </nav>
    </div>
</header>