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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC&display=swap" rel="stylesheet">

</head>

<body>
    <?php
    include 'compoents/nav.php';
    ?>
    <section class="bg-dark text-white py-5 text-center">
        <div class="container text-center py-5 px-4" style="
    background: #faf8f5;
    border: 1px solid #eae5df;
    border-radius: 1.5rem;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    font-family: 'Noto Serif TC', serif;
    color: #3e3e3e;
">
            <h1 class="fw-bold mb-3" style="font-size: 3rem;">人生研究室</h1>
            <p class="mb-4" style="font-size: 1.25rem; line-height: 1.8;">
                用知識品味人生，<br class="d-none d-md-block">用閱讀改變風景。
            </p>
            <a href="#products" class="btn btn-outline-dark rounded-pill px-4 py-2 shadow-sm" style="
        font-weight: 500;
        letter-spacing: 0.5px;  
        transition: all 0.3s ease;
    " onmouseover="this.style.backgroundColor='#3e3e3e'; this.style.color='#fff';"
                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#3e3e3e';">
                立即探索書海
            </a>
        </div>

    </section>
    <section id="products" class="py-5 mb-5">

        <div class="container-fluid">
            <h2 class="mb-4 text-center fw-bold border-bottom pb-2">
                <i class="bi bi-book-half me-2 text-primary"></i>
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
                $limit = 5;
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                $totalProducts = $result ? $result->num_rows : 0;
                // 使用現有函數顯示產品列表
                displayProductsList($result, '暫無商品', $keyword, $limit, $page, );
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