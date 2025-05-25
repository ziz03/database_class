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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-cream: #faf8f2;
            --secondary-cream: #f5f2e8;
            --warm-white: #fefdfb;
            --soft-gray: #e8e6e0;
            --text-dark: #2c2c2c;
            --text-muted: #8b8680;
            --accent-sage: #a4b5a0;
            --accent-warm: #d4b896;
            --danger-soft: #e8a298;
            --warning-soft: #e8c798;
            --success-soft: #b5c9a4;
            --shadow-light: rgba(44, 44, 44, 0.08);
            --shadow-medium: rgba(44, 44, 44, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Noto Serif TC', serif;
            background: linear-gradient(135deg, var(--primary-cream) 0%, var(--warm-white) 100%);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
        }

        .page-header {
            background: var(--warm-white);
            border-bottom: 1px solid var(--soft-gray);
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .page-title {
            font-family: 'Noto Serif TC', serif;
            font-weight: 600;
            font-size: 2rem;
            color: var(--text-dark);
            margin: 0;
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-sage), var(--accent-warm));
            border-radius: 2px;
        }

        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .cart-card {
            background: var(--warm-white);
            border: none;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px var(--shadow-light);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .cart-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-sage), var(--accent-warm));
        }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--soft-gray);
        }

        .cart-table {
            background: transparent;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px var(--shadow-light);
        }

        .cart-table thead {
            background: var(--secondary-cream);
        }

        .cart-table th {
            border: none;
            padding: 1.25rem 1rem;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cart-table td {
            border: none;
            padding: 1.5rem 1rem;
            vertical-align: middle;
            background: var(--warm-white);
            border-bottom: 1px solid var(--soft-gray);
        }

        .cart-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cart-table tbody tr:hover td {
            background: var(--primary-cream);
            transition: background-color 0.2s ease;
        }

        .product-name {
            font-weight: 500;
            color: var(--text-dark);
            font-size: 1rem;
        }

        .price-text {
            font-weight: 500;
            color: var(--accent-sage);
            font-size: 1rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            max-width: 140px;
            margin: 0 auto;
            background: var(--secondary-cream);
            border-radius: 8px;
            padding: 4px;
        }

        .quantity-btn {
            background: transparent;
            border: none;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .quantity-btn:hover:not(:disabled) {
            background: var(--accent-sage);
            color: white;
            transform: scale(1.05);
        }

        .quantity-btn:disabled {
            color: var(--text-muted);
            cursor: not-allowed;
        }

        .quantity-input {
            border: none;
            background: transparent;
            text-align: center;
            width: 60px;
            height: 36px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 1rem;
        }

        .quantity-input:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 0 2px var(--accent-sage);
            border-radius: 4px;
        }

        .subtotal {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1.1rem;
        }

        .stock-info {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stock-ok {
            color: var(--success-soft);
        }

        .stock-warning {
            color: var(--warning-soft);
        }

        .stock-danger {
            color: var(--danger-soft);
        }

        .stock-limit {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-style: italic;
            margin-top: 2px;
        }

        .remove-btn {
            background: linear-gradient(135deg, var(--danger-soft), #e69a8f);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(232, 162, 152, 0.3);
        }

        .remove-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(232, 162, 152, 0.4);
            background: linear-gradient(135deg, #e69a8f, var(--danger-soft));
        }

        .cart-summary {
            background: var(--secondary-cream);
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 16px var(--shadow-light);
        }

        .total-amount {
            font-family: 'Noto Serif TC', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .checkout-btn {
            background: linear-gradient(135deg, #8b7355, #a4956b);
            border: none;
            padding: 0.875rem 2.5rem;
            border-radius: 50px;
            color: white;
            font-weight: 500;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(139, 115, 85, 0.3);
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 115, 85, 0.4);
            color: white;
            background: linear-gradient(135deg, #a4956b, #b8a477);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--danger-soft), #f2d2cd);
            color: var(--text-dark);
            box-shadow: 0 4px 16px rgba(232, 162, 152, 0.2);
        }

        .alert-dismissible .btn-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            opacity: 0.7;
        }

        .breadcrumb-section {
            padding: 1rem 0;
            background: transparent;
        }

        .breadcrumb {
            background: transparent;
            margin: 0;
            padding: 0;
        }

        .breadcrumb-item {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: var(--text-muted);
            font-weight: 300;
        }

        .breadcrumb-item.active {
            color: var(--accent-sage);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .cart-card {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .cart-table {
                font-size: 0.9rem;
            }
            
            .cart-table th,
            .cart-table td {
                padding: 1rem 0.5rem;
            }
            
            .page-title {
                font-size: 1.75rem;
            }
            
            .cart-summary {
                padding: 1.5rem;
            }
            
            .total-amount {
                font-size: 1.3rem;
            }
        }

        /* 動畫效果 */
        .cart-card {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cart-table tbody tr {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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
        
        <div class="page-header">
            <div class="cart-container">
                <nav class="breadcrumb-section">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" style="color: var(--text-muted); text-decoration: none;">首頁</a></li>
                        <li class="breadcrumb-item active">購物車</li>
                    </ol>
                </nav>
                <h1 class="page-title">
                    <i class="bi bi-cart3 me-3" style="color: var(--accent-sage);"></i>
                    我的購物車
                </h1>
            </div>
        </div>

        <main class="flex-grow-1">
            <div class="cart-container">
                <div class="cart-card">
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (empty($cart_items)): ?>
                        <div class="empty-cart">
                            <i class="bi bi-cart-x"></i>
                            <h3 style="color: var(--text-muted); font-weight: 400; margin-bottom: 1rem;">購物車空空如也</h3>
                            <p style="color: var(--text-muted); margin-bottom: 2rem;">還沒有挑選心儀的書籍嗎？</p>
                            <a href="index.php" class="checkout-btn">
                                <i class="bi bi-book me-2"></i>
                                開始探索
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table cart-table align-middle text-center">
                                <thead>
                                    <tr>
                                        <th>商品資訊</th>
                                        <th>單價</th>
                                        <th>數量</th>
                                        <th>小計</th>
                                        <th>庫存狀態</th>
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
                                            <td>
                                                <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                            </td>
                                            <td>
                                                <div class="price-text">NT$ <?= number_format($item['price'], 0) ?></div>
                                            </td>
                                            <td>
                                                <div class="quantity-control">
                                                    <button class="quantity-btn" type="button"
                                                        onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>, <?= $item['stock'] ?>)">−</button>
                                                    <input type="text" class="quantity-input" value="<?= $item['quantity'] ?>"
                                                        data-stock="<?= $item['stock'] ?>" 
                                                        onkeypress="handleKeyPress(event, <?= $item['id'] ?>)"
                                                        onchange="manualUpdateQuantity(<?= $item['id'] ?>, this.value, <?= $item['stock'] ?>)">
                                                    <button class="quantity-btn" type="button"
                                                        <?= ($item['quantity'] >= $item['stock']) ? 'disabled' : '' ?>
                                                        onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>, <?= $item['stock'] ?>)">+</button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="subtotal">NT$ <?= number_format($subtotal, 0) ?></div>
                                            </td>
                                            <td>
                                                <div class="stock-info <?= $stock_class ?>">
                                                    庫存 <?= $item['stock'] ?> 件
                                                    <?php if ($item['quantity'] >= $item['stock']): ?>
                                                        <div class="stock-limit">已達購買上限</div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <form method="POST" action="action/cart.php" class="d-inline">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="remove-btn" title="移除商品">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="cart-summary">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="total-amount">
                                        <i class="bi bi-receipt me-2" style="color: var(--accent-sage);"></i>
                                        總計：NT$ <?= number_format($total_price, 0) ?>
                                    </p>
                                    <small style="color: var(--text-muted);">已包含所有費用</small>
                                </div>
                                <a href="checkout.php" class="checkout-btn">
                                    <i class="bi bi-credit-card me-2"></i>
                                    前往結帳
                                </a>
                            </div>
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