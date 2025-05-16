<?php
session_start();
require_once 'action/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT ci.id, ci.quantity, p.name AS product_name, p.price, p.stock, p.id AS product_id
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
    <title>購物車｜人生研究室</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Serif TC', serif;
            background-color: #fefcf7;
        }

        h2 {
            font-weight: bold;
            color: #333;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .stock-warning {
            color: #f0ad4e;
            font-weight: bold;
        }

        .stock-danger {
            color: #d9534f;
            font-weight: bold;
        }

        .stock-ok {
            color: #5cb85c;
        }

        .btn-outline-secondary {
            border-radius: 0;
        }

        .cart-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .cart-total {
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
    <script>
        function updateQuantity(cartItemId, newQuantity, stock) {
            if (newQuantity < 1) return;
            if (newQuantity > stock) {
                alert("數量不能超過庫存！");
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'action/cart.php';

            form.innerHTML = `
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="cart_item_id" value="${cartItemId}">
                <input type="hidden" name="quantity" value="${newQuantity}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function manualUpdateQuantity(cartItemId, newQuantity, stock) {
            newQuantity = parseInt(newQuantity);
            if (isNaN(newQuantity) || newQuantity < 1) {
                alert("數量必須大於 0");
                return;
            }
            if (newQuantity > stock) {
                alert("數量不能超過庫存！");
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'action/cart.php';

            form.innerHTML = `
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="cart_item_id" value="${cartItemId}">
                <input type="hidden" name="quantity" value="${newQuantity}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function handleKeyPress(event, cartItemId) {
            if (event.keyCode === 13) {
                event.preventDefault();
                manualUpdateQuantity(cartItemId, event.target.value, event.target.getAttribute('data-stock'));
            }
        }
    </script>
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'compoents/nav.php'; ?>
        <main class="flex-grow-1">
            <div class="container my-5">
                <div class="cart-card">
                    <h2 class="mb-4">您的購物車</h2>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (empty($cart_items)): ?>
                        <p class="text-muted">目前購物車沒有商品。</p>
                    <?php else: ?>
                        <table class="table table-bordered align-middle text-center">
                            <thead>
                                <tr>
                                    <th>商品名稱</th>
                                    <th>價格</th>
                                    <th>數量</th>
                                    <th>小計</th>
                                    <th>庫存</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total_price = 0; ?>
                                <?php foreach ($cart_items as $item): ?>
                                    <?php
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total_price += $subtotal;
                                        $stock_class = ($item['quantity'] >= $item['stock']) ? 'stock-danger'
                                            : (($item['quantity'] >= $item['stock'] * 0.8) ? 'stock-warning' : 'stock-ok');
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td><?= htmlspecialchars($item['price']) ?> 元</td>
                                        <td>
                                            <div class="input-group justify-content-center" style="max-width: 140px; margin: auto;">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                    onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>, <?= $item['stock'] ?>)">-</button>
                                                <input type="text" class="form-control text-center" value="<?= $item['quantity'] ?>"
                                                    data-stock="<?= $item['stock'] ?>" 
                                                    onkeypress="handleKeyPress(event, <?= $item['id'] ?>)"
                                                    onchange="manualUpdateQuantity(<?= $item['id'] ?>, this.value, <?= $item['stock'] ?>)">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                    <?= ($item['quantity'] >= $item['stock']) ? 'disabled' : '' ?>
                                                    onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>, <?= $item['stock'] ?>)">+</button>
                                            </div>
                                        </td>
                                        <td><?= number_format($subtotal, 0) ?> 元</td>
                                        <td class="<?= $stock_class ?>">
                                            <?= $item['stock'] ?>
                                            <?php if ($item['quantity'] >= $item['stock']): ?>
                                                <small class="d-block">(已達上限)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" action="action/cart.php" class="d-inline">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="cart-total">總金額：<?= number_format($total_price, 0) ?> 元</div>
                            <a href="checkout.php" class="btn btn-success btn-lg">前往結帳</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <?php include 'compoents/footer.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
