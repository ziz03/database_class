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
            <h2 class="mb-4">所有產品</h2>
            <div class="row g-4 justify-content-center">
                <?php foreach ($products as $product): ?>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="card h-100 text-center" style="max-width: 300px; margin: 0 auto;">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                <h6 class="card-subtitle mb-2 text-muted"><?= number_format($product['price'], 2) ?> 元</h6>
                                <form method="POST" action="action/cart.php">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">加入購物車</button>
                                </form>
                                <a href="product_detail.php?product_id=<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm mt-2">查看詳情</a>
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
