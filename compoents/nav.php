<nav class="navbar navbar-expand-lg bg-dark navbar-dark py-4">
    <div class="container-fluid align-items-center">
        <!-- 左側 Logo + 標題 -->
        <a class="navbar-brand d-flex align-items-center" href="index2.php">
            <img src="https://sitestorage.notorious-2019.com/icon/NOTORIOUS_logo.svg" alt="NOTORIOUS_logo" style="width: 200px;" class="me-2">
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
                    <a class="nav-link active" aria-current="page" href="index2.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">購物車</a>
                </li>
                <?php if (empty($_SESSION['loggedin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">登入</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>