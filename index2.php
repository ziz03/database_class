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
    <?php
    include 'compoents/nav.php';
    ?>

    <section id="products" class="py-5 mb-5">
        <div class="container-fluid">
            <h2 class="mb-4">精選產品</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="card h-100 text-center" style="max-width: 300px; margin: 0 auto;">
                        <img src="https://sitestorage.notorious-2019.com/product/180101014_g7_1.jpg" class="card-img-top" alt="產品 1">
                        <div class="card-body">
                            <h5 class="card-title">產品 1</h5>
                            <a href="#" class="btn btn-outline-primary btn-sm">查看詳情</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="card h-100 text-center" style="max-width: 300px; margin: 0 auto;">
                        <img src="https://sitestorage.notorious-2019.com/product/240111002_g1_1.jpg" class="card-img-top" alt="產品 2">
                        <div class="card-body">
                            <h5 class="card-title">產品 2</h5>
                            <a href="#" class="btn btn-outline-primary btn-sm">查看詳情</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="card h-100 text-center" style="max-width: 300px; margin: 0 auto;">
                        <img src="https://sitestorage.notorious-2019.com/product/180101014_g8_1.jpg" class="card-img-top" alt="產品 3">
                        <div class="card-body">
                            <h5 class="card-title">產品 3</h5>
                            <a href="#" class="btn btn-outline-primary btn-sm">查看詳情</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    include 'compoents/footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</body>

</html>