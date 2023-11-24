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
                        <!-- Người dùng chưa có tài khoản -->
                        <?php if (!isset($_SESSION['user'])): ?>
                            <li class="nav-item"><a class="nav-link" href=".">Look up documents</a></li>
                            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                            <li class="nav-item"><a class="nav-link" href="help.php" title="Help & Support" style="cursor: help;"><i class="fa-regular fa-circle-question"></i></a></li>
                        <?php endif ?>
                        <!-- Admin -->
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="statistic.php">Statistics</a></li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" id="navbardrop" data-bs-toggle="dropdown">
                                    Manage
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="manage_users_student.php">Manage Students</a>
                                    <a class="dropdown-item" href="manage_users_teacher.php">Manage Teachers</a>
                                    <a class="dropdown-item" href="manage_titles.php">Manage Titles</a>
                                    <a class="dropdown-item" href="manage_callcard.php">Manage Call Cards</a>
                                    </div>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Logout &nbsp <i class="fa fa-sign-out"></i></a></li>
                        <?php endif ?>
                        <!-- Người dùng có tài khoản -->
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] != 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href=".">Look up documents</a></li>
                            <!-- Dropdown -->
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" id="navbardrop" data-bs-toggle="dropdown">

                                    <img class="rounded-circle" style="width:30px; height:30px;"
                                        src="avatar/<?php echo $_SESSION['user']['avatar'] ?>" alt="Ảnh người dùng">&nbsp

                                    <?php echo $_SESSION['user']['name'] ?>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="profile.php"><i class="fa-regular fa-circle-user"></i> &nbsp My Profile</i></a>
                                    <!-- <a class="dropdown-item" href="#">Manage comments</i></a> -->
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout.php">Logout &nbsp <i class="fa fa-sign-out"></i></a>
                                </div>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="help.php" title="Help & Support" style="cursor: help;"><i class="fa-regular fa-circle-question"></i></a></li>
                        <?php endif ?>
                    </ul>
                </div>
            </nav>
    </div>
</header>