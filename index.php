<?php
session_start();
require_once 'action/database.php'; // 確保連接到資料庫
require_once 'action/common.php';
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>人生研究室</title>
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

</head>

<body>
    <?php
    include 'compoents/nav.php';
    ?>

    <section id="products" class="py-5 mb-5">
        <div class="container-fluid">
            <h2 class="mb-4">
                <?php
                if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                    echo '搜尋結果：' . htmlspecialchars($_GET['keyword']);
                } else {
                    echo '推薦好書';
                }
                ?>
            </h2>
            <div class="row g-4 justify-content-center">
                <?php
                // 檢查是否有搜尋關鍵字
                $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

                // 使用搜尋函數獲取結果
                $result = getProductSearchResults($conn, 'products', ['name', 'description'], $keyword);

                // 使用現有函數顯示產品列表
                displayProductsList($result, '暫無商品', $keyword);
                ?>
            </div>
        </div>
    </section>

    <?php
    include 'compoents/footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>

</body>

</html>