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

    <style>
        .fade-in {
            opacity: 0;
            animation: fadeIn ease 1.5s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards;
        }

        .fade-in-up.delay-1 {
            animation-delay: 0.3s;
        }

        .fade-in-up.delay-2 {
            animation-delay: 0.6s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

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
            <h1 class="fw-bold mb-3 fade-in-up" style="font-size: 3rem;">人生研究室</h1>
            <p class="mb-4 fade-in-up delay-1" style="font-size: 1.25rem; line-height: 1.8;">
                用知識品味人生，<br class="d-none d-md-block">用閱讀改變風景。
            </p>


            <a href="#products" class="btn btn-outline-dark rounded-pill px-4 py-2 shadow-sm fade-in-up delay-2" style="
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
            <h2 class="mb-4 text-center fw-bold border-bottom pb-3" style="color: #5a4637;">
                <p class="text-muted mb-3" style="font-size: 1rem;">
                    <i class="bi bi-pen me-2" style="color: #967259;"></i>
                    不讀書而有遠見，那是奢望<br>
                    讀書而有遠見，就不是偶然。
                </p>
                <?php
                if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                    echo '<span class="fs-5">搜尋結果：</span><span class="text-decoration-underline fs-5">' . htmlspecialchars($_GET['keyword']) . '</span>';
                } else {
                    echo '<span class="fs-5">推薦好書</span>';
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
                displayProductsList($result, '暫無商品', $keyword, $limit, $page, $totalProducts);
                ?>
            </div>
        </div>
    </section>
    <?php
    $quotes = [
        "閱讀無法逃避現實，卻會讓現實變得可愛一點。",
        "書本是靈魂最溫柔的庇護所。",
        "每本書，都是一場與自己的對話;認識自己是所有智慧的開端。",
        "在文字之間，我們練習成為更好的人。",
        "閱讀使靈魂發光，使見識增長。",
        "書籍是人類進步的階梯。",
        "人間的黑夜靠燈火點燃，人心的黑夜靠書本點燃。"

    ];
    $randomQuote = $quotes[array_rand($quotes)];
    ?>
    <section class="text-center py-4">
    <a href="Allproduct.php" class="btn btn-outline-dark rounded-pill px-4 py-2 shadow-sm"
        style="font-weight: 500; letter-spacing: 0.5px; font-family: 'Noto Serif TC', serif;
        transition: all 0.3s ease;"
        onmouseover="this.style.backgroundColor='#3e3e3e'; this.style.color='#fff';"
        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#3e3e3e';">
        前往所有商品頁
    </a>
</section>

    <section class="text-center py-5 fade-in"
        style=" font-family: 'Noto Serif TC'; color: #5e4638;">
        <blockquote class="blockquote mb-0">

            <p style="font-size: 1.5rem;">「<?= $randomQuote ?>」</p>

            <footer class="blockquote-footer mt-2">人生研究室 · 今日金句</footer>
        </blockquote>
    </section>

    <?php
    include 'compoents/footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>

</body>

</html>