<?php
session_start();
require_once 'action/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>全部產品</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'compoents/nav.php'; ?>
        <section id="products" class="py-5 mb-5">
        <div class="container-fluid">
            <h2 class="mb-4">
                <?php
                if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                    echo '搜尋結果：' . htmlspecialchars($_GET['keyword']);
                } else {
                    echo '所有產品';
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
        <?php include 'compoents/footer.php'; ?>
    </div>
</body>

</html>