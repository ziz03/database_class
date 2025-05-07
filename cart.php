<?php
session_start();
require_once 'action/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 查詢使用者的購物車內容（JOIN products 以取商品名稱和價格）
$sql = "SELECT ci.id, ci.quantity, p.name AS product_name, p.price
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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
<div class="wrapper d-flex flex-column min-vh-100">
<?php include 'compoents/nav.php'; ?>
<main class="flex-grow-1">

<div class="container mt-5">
    <h2 class="mb-4">購物車內容</h2>
    <?php if (empty($cart_items)): ?>
        <p>您的購物車是空的。</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>商品名稱</th>
                    <th>價格</th>
                    <th>數量</th>
                    <th>小計</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0; // 總金額
                foreach ($cart_items as $item):
                    $subtotal = $item['price'] * $item['quantity']; // 計算小計
                    $total_price += $subtotal; // 累加到總金額
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['price']) ?> 元</td>
                        <td>
                            <form method="POST" action="action/cart.php" class="d-inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm" style="width: 70px;">
                                <button type="submit" class="btn btn-primary btn-sm mt-2">更新數量</button>
                            </form>
                        </td>
                        <td><?= number_format($subtotal, 0) ?> 元</td>
                        <td>
                            <form method="POST" action="action/cart.php" class="d-inline">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">移除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <h4>總金額: <?= number_format($total_price,0 ) ?> 元</h4>
            <a href="checkout.php" class="btn btn-success btn-lg">前往結帳</a>
        </div>
    <?php endif; ?>
</div>
</main>

    <?php 
    include 'compoents/footer.php'; 
    ?>
</div>
</body>
</html>
