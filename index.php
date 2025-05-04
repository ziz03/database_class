<?php
session_start();
require_once 'action/database.php'; // 確保連接到資料庫
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
            <h2 class="mb-4">精選產品</h2>
            <div class="row g-4 justify-content-center">

                <?php
                // 查詢所有產品
                $sql = "SELECT * FROM products";
                $result = $conn->query($sql);
                

                // 檢查是否有產品
                if ($result && $result->num_rows > 0) {
                    // 遍歷所有產品
                    while ($product = $result->fetch_assoc()) {
                        // 處理圖片URL，移除前綴 "../"
                        $image_url = $product['image_url'];
                        // 檢查並移除 "../" 前綴
                        if (strpos($image_url, '../') === 0) {
                            $image_url = substr($image_url, 3); // 移除前三個字符 "../"
                        }
                ?>
                        <div class="col-sm-6 col-md-3 col-lg-3">
                            <div class="card h-100 text-center" style="max-width: 300px; margin: 0 auto;">
                                <img src="<?= htmlspecialchars($image_url) ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-danger fw-bold">$<?= number_format($product['price']) ?></p>
                                    <a href="product.php?product_id=<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm">查看詳情</a>
                                    <form method="POST" action="action/cart.php" class="mt-2">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">加入購物車</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    // 沒有產品時顯示提示
                    echo '<div class="col-12 text-center"><p>暫無商品</p></div>';
                }
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