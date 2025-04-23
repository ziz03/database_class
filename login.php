<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-4">
        <div class="container-fluid align-items-center">
            <!-- 左側 Logo + 標題 -->
            <a class="navbar-brand d-flex align-items-center" href="#">
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
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">購物車</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">登入</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <h1 class="text-center">這是登入頁面</h1>
    <section name="login">
        <div class="container vh-100">
            <div class="row h-100">

                <!-- 左邊登入區塊 -->
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <div class="w-100 px-5" style="max-width: 400px;"> 
                        <h4 class="mb-3">歡迎回來</h4>
                        <hr>
                        <form action="action/login.php" method="post">
                            <div class="mb-3">
                                <label for="account" class="form-label">帳號</label>
                                <input type="text" class="form-control form-control-lg" id="account" name="account" placeholder="請輸入帳號(email)">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">密碼</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="請輸入密碼">
                            </div>
                            <div class="d-flex justify-content-end mb-3">
                                <a href="#">忘記密碼</a>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-dark btn-lg">登入</button>
                            </div>
                            <div class="d-grid mb-3">
                                <a href="#" class="btn btn-outline-dark btn-lg">註冊</a>
                            </div>
                            <div class="text-center mb-2">其他登入方式</div>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#"><i class="bi bi-facebook fs-3"></i></a>
                                <a href="#"><i class="bi bi-google fs-3"></i></a>
                                <a href="#"><i class="bi bi-line fs-3"></i></a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 右邊品牌區塊 -->
                <div class="col-md-6 bg-dark text-white d-flex align-items-center justify-content-center flex-column">
                    <img src="https://sitestorage.notorious-2019.com/icon/NOTORIOUS_logo.svg" alt="NOTORIOUS" class="mb-3" style="max-width: 500px;">
                    
                </div>

            </div>
        </div>
    </section>

    <footer class="text-center py-3 bg-dark text-white">
        © <?php echo date("Y"); ?> 屌你老母. 保留所有權利
        <br>
        客服專線 886-8-766-3800 | 服務時間 08:00~17:00
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>