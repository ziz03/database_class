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
</head>

<body>
    <?php include 'compoents/nav.php'; ?>

    <section id="product-detail" class="py-5 mb-5">
        <div class="container-fluid">
            <h2 class="mb-4"><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="row g-4 justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="card h-100">
                        <div class="row g-0 h-100">
                            <!-- 左側圖片 -->
                            <div class="col-4 d-flex align-items-center justify-content-center">
                                <img src="<?php echo htmlspecialchars($imgUrl); ?>"
                                    class="img-fluid w-100 object-fit-contain"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <!-- 右側內容 -->
                            <div class="col-8">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <div class="card-text mb-3 px-1">
                                        <?php
                                        // 分段顯示描述文字
                                        $paras = explode("\n", htmlspecialchars($product['description']));
                                        foreach ($paras as $p) {
                                            $p = trim($p);
                                            if ($p !== '') {
                                                echo '<p class="mb-3 lh-base text-break">' . $p . '</p>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <p class="card-subtitle mb-3 fs-2" style="color: red">
                                        $<?php echo number_format($product['price']); ?> 元
                                    </p>
                                    <div class="mt-auto">
                                        <form method="POST" action="action/cart.php" class="d-inline-block me-2">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="product_id"
                                                value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                加入購物車
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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