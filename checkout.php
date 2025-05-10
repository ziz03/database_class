<?php
session_start();
require_once 'action/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 查詢購物車內容，包含庫存信息
$sql = "SELECT ci.product_id, ci.quantity, p.name, p.price, p.stock
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_price = 0;
$has_stock_issues = false;
$stock_issues = [];

foreach ($cart_items as &$item) {
    $total_price += $item['price'] * $item['quantity'];
    
    // 檢查是否有庫存問題
    if ($item['quantity'] > $item['stock']) {
        $has_stock_issues = true;
        $stock_issues[] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'stock' => $item['stock']
        ];
        // 標記庫存不足的項目
        $item['stock_issue'] = true;
    } else {
        $item['stock_issue'] = false;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>結帳</title>
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

    /* 增加頁面間距相關樣式 */
    .wrapper {
        position: relative;
        min-height: 100vh;
    }

    main {
        padding-bottom: 100px;
        /* 確保 main 內容和 footer 之間有足夠空間 */
    }

    .form-action-buttons {
        margin-top: 30px;
        margin-bottom: 60px;
        /* 增加按鈕下方空間 */
    }

    .container {
        margin-bottom: 40px;
        /* 增加容器底部間距 */
    }
    </style>
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'compoents/nav.php'; ?>
        <main class="flex-grow-1 py-4">
            <!-- 添加 py-4 增加主內容區上下 padding -->

            <div class="container mt-5 mb-5">
                <!-- 增加 mb-5 容器底部間距 -->
                <h2>結帳資訊</h2>

                <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (empty($cart_items)): ?>
                <p>您的購物車是空的，無法結帳。</p>
                <?php else: ?>
                <!-- 庫存警告 -->
                <?php if ($has_stock_issues): ?>
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle-fill"></i> 庫存警告</h5>
                    <p>以下商品庫存不足，請返回購物車調整數量後再結帳：</p>
                    <ul>
                        <?php foreach ($stock_issues as $issue): ?>
                        <li><strong><?= htmlspecialchars($issue['name']) ?></strong> - 您要購買 <?= $issue['quantity'] ?>
                            件，但庫存只有 <?= $issue['stock'] ?> 件</li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="cart.php" class="btn btn-primary">返回購物車</a>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <strong>注意：</strong> 商品庫存可能隨時變動，實際庫存將在結帳時確認。若您停留在本頁時間過長，建議刷新頁面以查看最新庫存。
                </div>
                <?php endif; ?>

                <h4 class="mt-4">訂單摘要</h4>
                <ul class="list-group mb-4">
                    <!-- 增加 mb-4 增加下方間距 -->
                    <?php foreach ($cart_items as $item): 
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
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center <?= $item['stock_issue'] ? 'bg-light' : '' ?>">
                        <div>
                            <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                            <div class="<?= $stock_class ?> small">
                                庫存: <?= $item['stock'] ?>
                                <?php if ($item['stock_issue']): ?>
                                <span class="badge bg-danger">庫存不足</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span><?= number_format($item['price'] * $item['quantity'], 0) ?> 元</span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>總金額</strong>
                        <strong><?= number_format($total_price, 0) ?> 元</strong>
                    </li>
                </ul>

                <form action="action/checkout_process.php" method="POST" id="checkoutForm" class="mb-5">
                    <!-- 增加 mb-5 表單底部間距 -->
                    <h4 class="mt-4">收件資訊</h4>
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label">收件人姓名</label>
                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">地址</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="recipient_phone" class="form-label">聯絡電話</label>
                        <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" required>
                    </div>
                    <input type="hidden" name="total_price" value="<?= $total_price ?>">

                    <!-- 將按鈕包裝在一個 div 中並增加間距 -->
                    <div class="form-action-buttons mt-4 mb-5 pb-4">
                        <!-- 添加額外的間距 -->
                        <button type="submit" class="btn btn-success btn-lg" <?= $has_stock_issues ? 'disabled' : '' ?>
                            id="submitOrder">送出訂單</button>
                        <?php if ($has_stock_issues): ?>
                        <a href="cart.php" class="btn btn-outline-primary btn-lg ms-2">返回購物車調整數量</a>
                        <?php else: ?>
                        <a href="javascript:location.reload()" class="btn btn-outline-secondary ms-2">刷新庫存</a>
                        <?php endif; ?>
                    </div>
                </form>
                <?php endif; ?>
            </div>

        </main>
        <?php include 'compoents/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // 提交前檢查庫存
    document.getElementById('checkoutForm')?.addEventListener('submit', function(event) {
        <?php if ($has_stock_issues): ?>
        event.preventDefault();
        Swal.fire({
            icon: 'error',
            title: '庫存不足',
            html: '某些商品庫存不足，請返回購物車調整數量',
            confirmButtonText: '返回購物車'
        }).then(() => {
            window.location.href = 'cart.php';
        });
        <?php endif; ?>
    });
    </script>
</body>

</html>