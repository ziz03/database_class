<?php
session_start();
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_url = trim($_POST['image_url']);

    // 簡單驗證
    if ($name && $price > 0 && $stock >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image_url);
        $stmt->execute();
        $stmt->close();

        $success = "商品新增成功！";
    } else {
        $error = "請填寫完整且有效的資料。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增商品</title>
    <link rel="icon" href="../image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
<?php include('compoents/sidebar.php'); ?>
    <div class="content-wrapper flex-grow-1 p-3">
        <!-- 麵包屑導航 -->
        <?php echo generate_breadcrumb($current_page); ?>

        <!-- 這裡將會是各頁面的主要內容 -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="js/sidebar.js"></script>

<div class="container mt-5">
    <h2>新增商品</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">商品名稱</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">商品描述</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">價格 (元)</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">庫存數量</label>
            <input type="number" name="stock" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">圖片網址</label>
            <input type="text" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
        </div>

        <button type="submit" class="btn btn-primary">新增商品</button>
    </form>
</div>


</body>
</html>
