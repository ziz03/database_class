<?php
session_start();
require_once('../action/database.php'); // 正確抓資料庫連線
require_once('compoents/breadcrumb.php');
require_once('../action/common.php');
$userName = check_login();

// 資料統計
$productCount = 0;
$userCount = 0;
$orderCount = 0;

// 查詢商品數量
$sql1 = "SELECT COUNT(*) AS total FROM products";
$result1 = $conn->query($sql1);
$productCount = $result1->fetch_assoc()['total'] ?? 0;

// 查詢一般會員數量（排除 admin）
$sql2 = "SELECT COUNT(*) AS total FROM user WHERE role != 'admin'";
$result2 = $conn->query($sql2);
$userCount = $result2->fetch_assoc()['total'] ?? 0;

// 查詢訂單數量
$sql3 = "SELECT COUNT(*) AS total FROM orders";
$result3 = $conn->query($sql3);
$orderCount = $result3->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>後台管理系統</title>
    <link rel="icon" href="../image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/sidebar.css">
    <style>
        body {
            background-color: #f7f6f2;
            font-family: 'Noto Serif TC', serif;
        }
        .card-custom {
            border: none;
            border-radius: 20px;
            padding: 20px;
            background-color: #fffdfb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 1.2rem;
            color: #3e3e3e;
        }
        .card-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #a4b5a0;
        }
        .icon {
            font-size: 2rem;
            color: #d4b896;
        }
    </style>
</head>

<body>

<?php include('./compoents/sidebar.php'); ?>

<div id="content" class="flex-grow-1 p-4">
    <?php echo generate_breadcrumb($current_page); ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">後台管理首頁</h1>
            <h5 class="text-muted">歡迎回來，<?php echo htmlspecialchars($userName); ?>！</h5>
        </div>
    </div>

    <div class="row g-4">
        <!-- 商品卡片 -->
        <div class="col-md-4">
            <div class="card card-custom text-center">
                <div class="card-body">
                    <i class="bi bi-box-seam icon mb-2"></i>
                    <h5 class="card-title">商品總數</h5>
                    <div class="card-number"><?php echo $productCount; ?></div>
                </div>
            </div>
        </div>

        <!-- 會員卡片 -->
        <div class="col-md-4">
            <div class="card card-custom text-center">
                <div class="card-body">
                    <i class="bi bi-people icon mb-2"></i>
                    <h5 class="card-title">會員人數</h5>
                    <div class="card-number"><?php echo $userCount; ?></div>
                </div>
            </div>
        </div>

        <!-- 訂單卡片 -->
        <div class="col-md-4">
            <div class="card card-custom text-center">
                <div class="card-body">
                    <i class="bi bi-receipt icon mb-2"></i>
                    <h5 class="card-title">訂單總數</h5>
                    <div class="card-number"><?php echo $orderCount; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/sidebar.js"></script>
</body>
</html>
