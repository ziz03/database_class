<?php
session_start();
require_once 'action/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 查詢使用者的購物車內容（JOIN products 以取商品名稱、價格和庫存）
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
    <title>購物車</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .stock-warning {
        color: orange;
        font-weight: bold;
    }

    .stock-danger {
        color: red;
        font-weight: bold;
    }

    .stock-ok {
        color: green;
    }
    </style>
    <script>
    function updateQuantity(cartItemId, newQuantity, stock) {
        if (newQuantity < 1) return; // 不允許低於 1
        if (newQuantity > stock) {
            alert("數量不能超過庫存！");
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'action/cart.php';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'update';
        form.appendChild(actionInput);

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'cart_item_id';
        idInput.value = cartItemId;
        form.appendChild(idInput);

        const qtyInput = document.createElement('input');
        qtyInput.type = 'hidden';
        qtyInput.name = 'quantity';
        qtyInput.value = newQuantity;
        form.appendChild(qtyInput);

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

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'update';
        form.appendChild(actionInput);

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'cart_item_id';
        idInput.value = cartItemId;
        form.appendChild(idInput);

        const qtyInput = document.createElement('input');
        qtyInput.type = 'hidden';
        qtyInput.name = 'quantity';
        qtyInput.value = newQuantity;
        form.appendChild(qtyInput);

        document.body.appendChild(form);
        form.submit();
    }

    function handleKeyPress(event, cartItemId) {
        if (event.keyCode === 13) { // Enter key
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

            <div class="container mt-5">
                <h2 class="mb-4">購物車內容</h2>

                <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

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
                            <th>庫存</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $total_price = 0;
                            foreach ($cart_items as $item):
                                $subtotal = $item['price'] * $item['quantity'];
                                $total_price += $subtotal;
                                
                                // 確定庫存狀態的 CSS 類
                                $stock_class = '';
                                if ($item['quantity'] >= $item['stock']) {
                                    $stock_class = 'stock-danger';
                                } elseif ($item['quantity'] >= $item['stock'] * 0.8) {
                                    $stock_class = 'stock-warning';
                                } else {
                                    $stock_class = 'stock-ok';
                                }
                                ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['price']) ?> 元</td>
                            <td>
                                <div class="input-group" style="max-width: 120px;">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>, <?= $item['stock'] ?>)">-</button>
                                    <input type="text" class="form-control text-center" value="<?= $item['quantity'] ?>"
                                        min="1" max="<?= $item['stock'] ?>" data-stock="<?= $item['stock'] ?>"
                                        onchange="manualUpdateQuantity(<?= $item['id'] ?>, this.value, <?= $item['stock'] ?>)"
                                        onkeypress="handleKeyPress(event, <?= $item['id'] ?>)">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        <?= ($item['quantity'] >= $item['stock']) ? 'disabled' : '' ?>
                                        onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>, <?= $item['stock'] ?>)">+</button>
                                </div>
                            </td>
                            <td><?= number_format($subtotal, 0) ?> 元</td>
                            <td style="color:red;" class="<?= $stock_class ?>"  >
                                <?= htmlspecialchars($item['stock']) ?>
                                <?php if ($item['quantity'] >= $item['stock']): ?>
                                <small class="d-block">(已達上限)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="action/cart.php" class="d-inline">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                            <path
                                                d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between">
                    <h4>總金額: <?= number_format($total_price, 0) ?> 元</h4>
                    <a href="checkout.php" class="btn btn-success btn-lg">前往結帳</a>
                </div>
                <?php endif; ?>
            </div>
        </main>

        <?php include 'compoents/footer.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>