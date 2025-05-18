<?php
session_start();
require_once('action/common.php');
$userName = check_login();
// 撈訂單資料（包括商品名稱）
$user_id = $_SESSION['user_id'];
$sql = "
SELECT o.id AS order_id, o.total_price, o.status,o.created_at, GROUP_CONCAT(p.name SEPARATOR '、') AS product_names
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN products p ON oi.product_id = p.id
WHERE o.user_id = ?
GROUP BY o.id
ORDER BY o.created_at ASC;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

if($_SESSION['error']){
    $error = $_SESSION['error'];
    echo '<script>alert("' . $error . '");</script>';
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員中心｜人生研究室</title>
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2;
            font-family: 'Noto Serif TC', serif;
            color: #3e3e3e;
        }

        .custom-card {
            background: #faf8f5;
            border: 1px solid #eae5df;
            border-radius: 1.5rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }

        .form-control {
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .btn-dark {
            background-color: #3e3e3e;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-dark:hover {
            background-color: #000;
        }

        .btn-outline-dark:hover {
            background-color: #3e3e3e;
            color: white;
        }

        h1,
        h4 {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'compoents/nav.php'; ?>

    <section class="py-5">
        <div class="container py-4">
            <div class="custom-card p-5 mx-auto" style="max-width: 960px;">
                <div class="row g-4 align-items-center">
                    <!-- 左側：Tabs區域 -->
                    <div class="col-md-6">
                        <!-- Tabs 切換列 -->
                        <ul class="nav nav-tabs mb-3" id="userTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="name-tab" data-bs-toggle="tab" data-bs-target="#name" type="button" role="tab" aria-controls="name" aria-selected="true">
                                    更換名字
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
                                    訂單狀態
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs 內容 -->
                        <div class="tab-content" id="userTabContent" style="background-color: #faf8f5; border-radius: 1rem; padding: 1.5rem;">
                            <!-- 更換名字內容 -->
                            <div class="tab-pane fade show active" id="name" role="tabpanel" aria-labelledby="name-tab">
                                <h4 class="mb-3">歡迎，<?php echo htmlspecialchars($userName); ?></h4>
                                <p class="mb-4 text-muted">在這裡你可以更新你的帳號名稱。</p>
                                <form action="action/usercenter.php" method="post">
                                    <div class="mb-3">
                                        <label for="account" class="form-label">帳號（Email）</label>
                                        <input type="text" class="form-control" id="account" name="account" placeholder="請輸入帳號" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editname" class="form-label">新名字</label>
                                        <input type="text" class="form-control" id="editname" name="editname" placeholder="請輸入新名字" required>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-dark btn-lg rounded-pill">更改名字</button>
                                        <a href="register.php" class="btn btn-outline-dark btn-lg rounded-pill">註冊新帳號</a>
                                    </div>
                                </form>
                            </div>

                            <!-- 訂單狀態內容 -->
                            <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                <h5 class="mb-3">你的訂單</h5>
                                <p class="text-muted">這裡會列出你曾經送出的訂單狀態。</p>
                                <!-- 假資料範例 -->
                                <ul class="list-group">
                                    <?php if (empty($orders)): ?>
                                        <li class="list-group-item">目前沒有訂單紀錄。</li>
                                    <?php else: ?>
                                        <?php foreach ($orders as $order):
                                            $time = $order['created_at'];
                                            $dt = new DateTime($time);
                                        ?>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>訂單 #<?php echo $order['order_id']; ?></strong><br>
                                                        商品：<?php echo htmlspecialchars($order['product_names']); ?><br>
                                                        總金額：$<?php echo $order['total_price'] . '<br/>'; ?>
                                                        訂單創建時間: <?php echo $dt->format('Y-m-d H:i'); ?>
                                                    </div>
                                                    <span class="badge 
                    <?php
                                            if ($order['status'] == 'Processing') echo 'bg-warning text-dark';
                                            elseif ($order['status'] == 'paid') echo 'bg-success';
                                            elseif ($order['status'] == 'shipped') echo 'bg-primary';
                                            else echo 'bg-secondary';
                    ?> 
                    rounded-pill">
                                                        <?php
                                                        if ($order['status'] == 'Processing') echo '處理中';
                                                        elseif ($order['status'] == 'paid') echo '已付款';
                                                        elseif ($order['status'] == 'shipped') echo '已出貨';
                                                        else echo $order['status'];
                                                        ?>
                                                    </span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- 右側：會跟著tab切換的內容 -->
                    <div class="col-md-6">
                        <!-- 對應「更換名字」tab的右側內容 -->
                        <div id="right-name-content" class="right-content">
                            <div class="text-center">
                                <i class="bi bi-book-half fs-2 mb-3" style="color: #6e5843;"></i>
                                <h3 class="fw-bold mb-2">人生研究室</h3>
                                <p class="text-center mb-4" style="max-width: 300px; margin: 0 auto;">
                                    我們相信：<br>
                                    「最深的感性，來自最深的知性。」<br>
                                    透過閱讀與學習，為人生注入新的風景。
                                </p>
                                <blockquote class="text-muted fst-italic" style="max-width: 280px; margin: 0 auto;">
                                    "The cost of ignorance is always higher than the price of knowledge." <br>- Margaret Atwood
                                </blockquote>
                            </div>
                        </div>

                        <!-- 對應「訂單狀態」tab的右側內容 -->
                        <div id="right-orders-content" class="right-content" style="display: none;">
                            <div class="text-center">
                                <i class="bi bi-cart-check fs-2 mb-3" style="color: #6e5843;"></i>
                                <h3 class="fw-bold mb-2">訂單資訊</h3>
                                <p class="text-center mb-4" style="max-width: 300px; margin: 0 auto;">
                                    感謝您的購買！<br>
                                    我們將盡快處理您的訂單。<br>
                                    如有任何問題，請隨時聯絡我們。
                                </p>
                                <div class="alert alert-info">
                                    <strong>提示：</strong> 您可以在左側查看您的訂單詳細狀態。
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 添加JavaScript來控制右側內容的切換 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 獲取tab按鈕
            const nameTab = document.getElementById('name-tab');
            const ordersTab = document.getElementById('orders-tab');

            // 獲取右側內容區域
            const rightNameContent = document.getElementById('right-name-content');
            const rightOrdersContent = document.getElementById('right-orders-content');

            // 監聽tab切換事件
            nameTab.addEventListener('shown.bs.tab', function() {
                rightNameContent.style.display = 'block';
                rightOrdersContent.style.display = 'none';
            });

            ordersTab.addEventListener('shown.bs.tab', function() {
                rightNameContent.style.display = 'none';
                rightOrdersContent.style.display = 'block';
            });
        });
    </script>

    <?php include 'compoents/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>