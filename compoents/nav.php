<nav class="navbar navbar-expand-lg  navbar-dark py-4" style="background-color: black;">
    <div class="container-fluid align-items-center">
        <!-- 左側 Logo + 標題 -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="image\blackLOGO.png" alt="NOTORIOUS_logo" style="width: 100px; height: 100px;" class="me-2">
        </a>

        <!-- 漢堡按鈕 -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- 導覽選單 -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">購物車</a>
                </li>
                <?php if (!empty($_SESSION['loggedin'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <!-- 如果是管理員 -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin\dashboard.php">後台管理</a>
                        </li>
                    <?php else: ?>
                        <!-- 如果是普通使用者 -->
                        <li class="nav-item">
                            <a class="nav-link" href="userCenter.php">會員中心</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="action/logout.php">登出</a>
                    </li>
                <?php else: ?>
                    <!-- 沒有登入的人才顯示登入 -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">登入</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>