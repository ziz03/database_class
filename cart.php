<?php
session_start();


// 初始化購物車
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 處理清除購物車訊息
if (isset($_GET['msg'])) {
    echo "<div class='alert alert-success text-center'>" . htmlspecialchars($_GET['msg']) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>購物車</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <?php
    include 'compoents/nav.php';
    ?>
    <div class="container mt-5">
        <h2 class="mb-4">購物車內容</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>您的購物車是空的。</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>商品名稱</th>
                        <th>數量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productName => $quantity): ?>
                        <tr>
                            <td><?= htmlspecialchars($productName) ?></td>
                            <td><?= htmlspecialchars($quantity) ?></td>
                            <td>
                                <form method="POST" action="cart_action.php" class="d-inline">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product" value="<?= htmlspecialchars($productName) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">移除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php include 'compoents/footer.php'; ?>
</body>

</html>