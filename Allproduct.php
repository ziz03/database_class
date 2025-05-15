<?php
session_start();
require_once 'action/database.php';


?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>所有產品</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 字體 -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f9f8f4;
            font-family: 'Noto Serif TC', serif;
            color: #4a4a4a;
        }

        h2 {
            font-weight: bold;
            color: #5c504d;
        }

        a {
            text-decoration: none;
        }
    </style>
    
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'compoents/nav.php'; ?>
        <section id="products" class="py-5 mb-5">
            <div class="container-fluid">
                <h2 class="mb-4 ">
                    <?php
                    if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                        echo '搜尋結果：' . htmlspecialchars($_GET['keyword']);
                    } else {
                        echo '所有產品';
                    }

                    ?>
                    <?php if (isset($_GET['keyword'])): ?>
                        <div class="mb-4">
                            <a href="Allproduct.php"
                                class="btn btn-outline-dark px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
                                <i class="bi bi-arrow-repeat"></i> 回到所有產品首頁
                            </a>
                        </div>
                    <?php endif; ?>
                </h2>
                <div class="row g-4 justify-content-center">
                    <?php
                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    // 檢查是否有搜尋關鍵字
                    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
                    // 使用搜尋函數獲取結果
                    $totalProducts = 0; // 新增變數保存總產品數
                    $result = getProductSearchResults($conn, 'products', ['name', 'description'], $keyword, $page, 3, $totalProducts);
                    // 使用現有函數顯示產品列表
                    displayProductsList($result, '暫無商品', $keyword, 3, $page, $totalProducts);
                    ?>
                </div>
            </div>
        </section>

        <?php include 'compoents/footer.php'; ?>
    </div>
</body>

</html>