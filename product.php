<?php
session_start();
require_once 'action/database.php';

// 查詢所有產品
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> 產品詳請</title>
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'compoents/nav.php'; ?>

    <section id="products" class="py-5 mb-5">
        <div class="container-fluid">
            <h2 class="mb-4"><?php echo htmlspecialchars($products[0]['name']) ?></h2>
            <div class="row g-4 justify-content-center">
                <?php foreach ($products as $product): ?>
                    <div class="col-12 col-md-10 col-lg-8 ">
                        <div class="card h-100 ">
                            <div class="row g-0 h-100">
                                <!-- 左側圖片 -->
                                <div class="col-4">
                                    <img
                                        src="<?= htmlspecialchars($product['image_url']) ?>"
                                        class="img-fluid h-100 w-100 object-fit-cover"
                                        alt="<?= htmlspecialchars($product['name']) ?>">
                                </div>
                                <!-- 右側內容 -->
                                <div class="col-8">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                        <div class="card-text mb-3 px-1">
                                            <?php
                                            // 將描述文字分段處理
                                            $paragraphs = explode("\n", htmlspecialchars($product['description']));
                                            foreach ($paragraphs as $paragraph) {
                                                if (trim($paragraph) !== '') {
                                                    echo '<p class="mb-3 lh-base text-break">' . $paragraph . '</p>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <p class="card-subtitle mb-3 fs-2  " style="color: red">$<?= number_format($product['price']) ?> 元</p>
                                        <!-- 按鈕區塊：在文字下面 -->
                                        <div class="mt-auto">
                                            <form method="POST" action="action/cart.php" class="d-inline-block me-2">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">加入購物車</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="container">
        <a href="cart.php" class="btn btn-success btn-lg w-100">查看購物車與結帳</a>
    </div>



    <?php include 'compoents/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>