<?php
session_start();
require_once 'action/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 查詢購物車內容
$sql = "SELECT ci.product_id, ci.quantity, p.name, p.price
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
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>結帳</title>
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'compoents/nav.php'; ?>
        <main class="flex-grow-1">

            <div class="container mt-5">
                <h2>結帳資訊</h2>

                <?php if (empty($cart_items)): ?>
                    <p>您的購物車是空的，無法結帳。</p>
                <?php else: ?>
                    <h4 class="mt-4">訂單摘要</h4>
                    <ul class="list-group mb-3">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <div>
                                    <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                                </div>
                                <span><?= number_format($item['price'] * $item['quantity'], 0) ?> 元</span>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>總金額</strong>
                            <strong><?= number_format($total_price, 0) ?> 元</strong>
                        </li>
                    </ul>

                    <form action="action/checkout_process.php" method="POST">
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
                        <button type="submit" class="btn btn-success btn-lg">送出訂單</button>
                    </form>
                <?php endif; ?>
            </div>

        </main>
        <?php include 'compoents/footer.php'; ?>
    </div>
</body>

</html>