<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notorious</title>
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

    <section id="products" class="py-5">
        <div class="container">
            <h2 class="mb-4">精選產品</h2>
            <div class="row g-4">
                <div class="col-sm-6 col-md-4">
                    <div class="card h-100 text-center">
                        <img src="https://sitestorage.notorious-2019.com/product/180101014_g7_1.jpg" class="card-img-top" alt="產品 1">
                        <div class="card-body">
                            <h5 class="card-title">產品 1</h5>
                            <a href="#" class="btn btn-outline-primary">查看詳情</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="card h-100 text-center">
                        <img src="https://sitestorage.notorious-2019.com/product/240111002_g1_1.jpg" class="card-img-top" alt="產品 2">
                        <div class="card-body">
                            <h5 class="card-title">產品 2</h5>
                            <a href="#" class="btn btn-outline-primary">查看詳情</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="card h-100 text-center">
                        <img src="https://sitestorage.notorious-2019.com/product/180101014_g8_1.jpg" class="card-img-top" alt="產品 3">
                        <div class="card-body">
                            <h5 class="card-title">產品 3</h5>
                            <a href="#" class="btn btn-outline-primary">查看詳情</a>
                        </div>
                    </div>
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