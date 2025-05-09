<?php
session_start();
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';

// 取得目前頁數
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1);
$limit = 5; // 每頁顯示幾筆資料
$offset = ($page - 1) * $limit;

// 搜尋功能
$search = trim($_GET['search'] ?? '');
$search_exact_found = false;
$total_orders = 0;
$orders = [];

if ($search !== '') {
    // 1️⃣ 完全符合的查詢 (收件人電話)
    $stmt = $conn->prepare("SELECT * FROM orders WHERE recipient_phone = ? LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $search, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (count($orders) > 0) {
        $search_exact_found = true;
        // 取得總筆數
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE recipient_phone = ?");
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $stmt->bind_result($total_orders);
        $stmt->fetch();
        $stmt->close();
    } else {
        // 2️⃣ 模糊搜尋 (部分符合)
        $likeSearch = "%" . $search . "%";
        $stmt = $conn->prepare("SELECT * FROM orders WHERE recipient_phone LIKE ? LIMIT ? OFFSET ?");
        $stmt->bind_param("sii", $likeSearch, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (count($orders) > 0) {
            // 取得總筆數
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE recipient_phone LIKE ?");
            $stmt->bind_param("s", $likeSearch);
            $stmt->execute();
            $stmt->bind_result($total_orders);
            $stmt->fetch();
            $stmt->close();
        }
    }
} else {
    // 沒有搜尋，顯示全部訂單
    $stmt = $conn->prepare("SELECT * FROM orders LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // 取得總筆數
    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $total_orders = $result->fetch_assoc()['total'];
}

// 計算總頁數
$total_pages = ceil($total_orders / $limit);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>訂單列表</title>
<link rel="icon" href="../image\blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/sidebar.css">
</head>

<body>
    <?php include('./compoents/sidebar.php'); ?>
    <div class="container mt-5">
        <h2>訂單列表</h2>
        <form class="d-flex mb-3" method="get" action="">
            <input class="form-control me-2" type="search" placeholder="輸入收件人電話" aria-label="Search" name="search"
                value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-success" type="submit">搜尋</button>
        </form>

        <?php if ($search !== '' && !$search_exact_found && count($orders) === 0): ?>
            <div class="alert alert-warning">找不到訂單。</div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>訂單ID</th>
                    <th>會員ID</th>
                    <th>總金額</th>
                    <th>訂單狀態</th>
                    <th>下單時間</th>
                    <th>收件人姓名</th>
                    <th>收件人電話</th>
                    <th>收件人地址</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['user_id']) ?></td>
                            <td><?= htmlspecialchars($order['total_price']) ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td><?= htmlspecialchars($order['recipient_name']) ?></td>
                            <td><?= htmlspecialchars($order['recipient_phone']) ?></td>
                            <td><?= nl2br(htmlspecialchars($order['recipient_address'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">目前沒有訂單資料。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">上一頁</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">下一頁</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script src="./js/sidebar.js"></script>
</body>

</html>
