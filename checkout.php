<?php
session_start();
require_once 'action/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

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

    if ($item['quantity'] > $item['stock']) {
        $has_stock_issues = true;
        $stock_issues[] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'stock' => $item['stock']
        ];
        $item['stock_issue'] = true;
    } else {
        $item['stock_issue'] = false;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <title>結帳</title>
    <link rel="icon" href="image/blackLOGO.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #faf8f2;
            font-family: "Noto Serif TC", serif;
        }

        h2, h4 {
            font-weight: 600;
            color: #3e3e3e;
        }

        .stock-warning {
            color: #d97706;
            font-weight: 600;
        }

        .stock-danger {
            color: #dc3545;
            font-weight: 700;
        }

        .stock-ok {
            color: #198754;
        }

        .bg-light.stock-issue {
            background-color: #fff5f5 !important;
        }

        .list-group-item {
            font-size: 1rem;
        }

        .form-action-buttons {
            margin-top: 2.5rem;
        }

        .alert h5 {
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        ::placeholder {
            color: #6c757d;
            opacity: 1;
        }

        input.form-control, textarea.form-control {
            background-color: #fefdfb;
            border-radius: 0.75rem;
            border: 1px solid #ccc;
        }

        .btn {
            border-radius: 0.75rem;
        }
    </style>
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'compoents/nav.php'; ?>

        <main class="flex-grow-1 py-4">
            <div class="container mt-5 mb-5">
                <h2 class="mb-4">結帳資訊</h2>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (empty($cart_items)): ?>
                    <p class="fs-5">您的購物車是空的，無法結帳。</p>
                <?php else: ?>
                    <?php if ($has_stock_issues): ?>
                        <div class="alert alert-danger" role="alert">
                            <h5><i class="bi bi-exclamation-triangle-fill"></i> 庫存警告</h5>
                            <p class="mb-3">以下商品庫存不足，請返回購物車調整數量後再結帳：</p>
                            <ul class="mb-3">
                                <?php foreach ($stock_issues as $issue): ?>
                                    <li><strong><?= htmlspecialchars($issue['name']) ?></strong> - 您要購買 <?= $issue['quantity'] ?> 件，但庫存只有 <?= $issue['stock'] ?> 件</li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="cart.php" class="btn btn-primary btn-sm">返回購物車調整數量</a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                            <div>
                                <strong>注意：</strong> 商品庫存可能隨時變動，實際庫存將在結帳時確認。若您停留在本頁時間過長，建議刷新頁面以查看最新庫存。
                            </div>
                        </div>
                    <?php endif; ?>

                    <h4 class="mt-4 mb-3">訂單摘要</h4>
                    <ul class="list-group shadow-sm mb-4">
                        <?php foreach ($cart_items as $item):
                            $stock_class = '';
                            if ($item['quantity'] > $item['stock']) {
                                $stock_class = 'stock-danger';
                            } elseif ($item['quantity'] >= $item['stock'] * 0.8) {
                                $stock_class = 'stock-warning';
                            } else {
                                $stock_class = 'stock-ok';
                            }
                        ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center <?= $item['stock_issue'] ? 'bg-light stock-issue' : '' ?>">
                                <div>
                                    <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                                    <div class="<?= $stock_class ?> small mt-1">
                                        庫存: <?= $item['stock'] ?>
                                        <?php if ($item['stock_issue']): ?>
                                            <span class="badge bg-danger ms-2">庫存不足</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="fw-semibold"><?= number_format($item['price'] * $item['quantity'], 0) ?> 元</span>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between fw-bold fs-5">
                            <span>總金額</span>
                            <span><?= number_format($total_price, 0) ?> 元</span>
                        </li>
                    </ul>

                    <form action="action/checkout_process.php" method="POST" id="checkoutForm" class="mb-5">
                        <h4 class="mb-3">收件資訊</h4>
                        <div class="mb-3">
                            <label for="recipient_name" class="form-label">收件人姓名</label>
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name" placeholder="請輸入收件人姓名" required />
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">地址</label>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="請輸入完整地址" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="recipient_phone" class="form-label">聯絡電話</label>
                            <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" placeholder="請輸入聯絡電話" required />
                        </div>
                        <input type="hidden" name="total_price" value="<?= $total_price ?>" />

                        <div class="form-action-buttons d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-success btn-lg flex-grow-1" <?= $has_stock_issues ? 'disabled' : '' ?> id="submitOrder">
                                <i class="bi bi-cart-check-fill me-2"></i>送出訂單
                            </button>

                            <?php if ($has_stock_issues): ?>
                                <a href="cart.php" class="btn btn-outline-primary btn-lg flex-grow-1">返回購物車調整數量</a>
                            <?php else: ?>
                                <button type="button" class="btn btn-outline-secondary btn-lg flex-grow-1" onclick="location.reload();">
                                    <i class="bi bi-arrow-clockwise me-2"></i>刷新庫存
                                </button>
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
