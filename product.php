<?php
session_start();
require_once 'action/database.php';

// 1. 取得並驗證 GET 傳入的 id
if (!isset($_GET['product_id']) || !ctype_digit($_GET['product_id'])) {
    echo '參數錯誤';
    exit;
}
$product_id = intval($_GET['product_id']);

// 2. 準備並執行單筆查詢
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

$raw = $product['image_url'];

// 先把 imgUrl 預設為原始值
$imgUrl = $raw;
// 如果它不是以 http:// 或 https:// 開頭，就去掉前面的所有點 (.)  
if (!preg_match('#^https?://#i', $imgUrl)) {
    // ltrim 第二個參數放 '.'，會去掉最前面所有的點
    $imgUrl = substr($imgUrl, 1);
    echo $imgUrl;
}
// 3. 若找不到該商品
if (!$product) {
    echo '找不到該商品';
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>產品詳情：<?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        #product-detail .card {
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            overflow: hidden;
        }

        #product-detail img {
            max-height: 360px;
            object-fit: contain;
            background-color: #fff;
            padding: 1rem;
            border-radius: 0.75rem;
        }

        .product-price {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
        }

        .description p {
            font-size: 1rem;
            line-height: 1.7;
            color: #555;
        }

        .btn-add-cart {
            font-size: 1.1rem;
            padding: 0.6rem 1.5rem;
        }

        .checkout-btn {
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            #product-detail img {
                max-height: 260px;
            }

            .product-price {
                font-size: 1.5rem;
            }
        }
        
    </style>
</head>

<body>
    <?php include 'compoents/nav.php'; ?>

    <section id="product-detail" class="py-5 mb-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5"><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-lg p-4 rounded-4 bg-light">
                        <div class="row g-5 align-items-start">
    <!-- 商品圖片 -->
    <div class="col-md-5">
        <!-- sticky-top 讓圖片黏在視窗左側，top:100px 可微調距離 -->
        <div class="sticky-top" style="top: 100px;">
            <div class="position-relative text-center">
                <img src="<?php echo htmlspecialchars($imgUrl); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                    class="img-fluid rounded-4 shadow-sm border border-2 border-white"
                    style="transition: transform .3s ease; max-height: 380px;"
                    onmouseover="this.style.transform='scale(1.05)';"
                    onmouseout="this.style.transform='scale(1)';">
                <span
                    class="badge bg-gradient text-white position-absolute top-0 start-0 m-2 px-3 py-2 rounded-pill"
                    style="background: linear-gradient(45deg, #f12711, #f5af19); font-size: 0.9rem;">
                    熱銷商品
                </span>
            </div>
        </div>
    </div>

    <!-- 商品資訊 -->
    <div class="col-md-7">
        <h3 class="fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h3>

        <!-- 商品介紹 Accordion 預設為 collapse（關閉） -->
        <div class="accordion mb-4" id="descriptionAccordion">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header" id="headingDesc">
                    <button class="accordion-button collapsed bg- fw-semibold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseDesc"
                        aria-expanded="false" aria-controls="collapseDesc">
                        商品介紹
                    </button>
                </h2>
                <div id="collapseDesc" class="accordion-collapse collapse"
                    aria-labelledby="headingDesc" data-bs-parent="#descriptionAccordion">
                    <div class="accordion-body">
                        <?php
                        $paras = explode("\n", htmlspecialchars($product['description']));
                        foreach ($paras as $p) {
                            $p = trim($p);
                            if ($p !== '') {
                                echo '<p class="text-muted lh-lg">' . $p . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <p class="fs-3 text-danger fw-bold mb-4">
            <i class="bi bi-cash-coin me-2"></i>$<?php echo number_format($product['price']); ?> 元
        </p>

        <form method="POST" action="action/cart.php" class="d-grid gap-2">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <button type="submit" class="btn btn-outline-danger btn-lg fw-semibold shadow-sm">
                <i class="bi bi-cart-plus me-2"></i>加入購物車
            </button>
        </form>

        <div class="mt-3 text-muted small d-flex align-items-center gap-2">
            <i class="bi bi-shield-check text-success"></i>
            本商品享有 7 天安心退貨保證
        </div>
    </div>
</div>

                    <!-- 底部大按鈕 -->
                    <div class="text-center mt-5">
                        <a href="cart.php" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow fw-bold">
                            <i class="bi bi-bag-check-fill me-2"></i>查看購物車與結帳
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- <div class="container">
        <a href="cart.php" class="btn btn-success btn-lg w-100">查看購物車與結帳</a>
    </div> -->

    <?php include 'compoents/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>