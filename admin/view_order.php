<?php
session_start();
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';
require_once('../action/common.php');
$userName = check_login();

// 取得目前頁數
$page = isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
$limit = 5; // 每頁顯示幾筆資料
$offset = ($page - 1) * $limit;

// 搜尋功能
$search = trim($_GET['search'] ?? '');
$search_exact_found = false;
$total_orders = 0;
$orders = [];

if ($search !== '') {
    // 完全符合查詢（收件人電話）
    $stmt = $conn->prepare("SELECT * FROM orders WHERE recipient_phone = ? LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $search, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (count($orders) > 0) {
        $search_exact_found = true;
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE recipient_phone = ?");
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $stmt->bind_result($total_orders);
        $stmt->fetch();
        $stmt->close();
    } else {
        // 模糊搜尋（部分符合）
        $likeSearch = "%" . $search . "%";
        $stmt = $conn->prepare("SELECT * FROM orders WHERE recipient_phone LIKE ? LIMIT ? OFFSET ?");
        $stmt->bind_param("sii", $likeSearch, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (count($orders) > 0) {
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

    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $total_orders = $result->fetch_assoc()['total'];
}

// 計算總頁數
$total_pages = ceil($total_orders / $limit);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <title>訂單列表</title>
    <link rel="icon" href="../image/blackLOGO.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous" />
    <link rel="stylesheet" href="./css/sidebar.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

    <style>
        body {
            background-color: #fdfaf6;
            font-family: 'Noto Serif TC', serif;
        }

        #content {
            min-height: 100vh;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgb(0 0 0 / 0.1);
        }

        h2 {
            font-weight: 700;
            color: #2c3e50;
        }

        form.d-flex {
            max-width: 420px;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 25px;
            border: 1.5px solid #b1a78e;
            padding-left: 1.25rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #6c757d;
            box-shadow: 0 0 8px #6c757d66;
        }

        .btn-outline-success {
            border-radius: 25px;
            padding: 0.45rem 1.5rem;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-outline-success:hover {
            background-color: #20c997;
            color: white;
            border-color: #20c997;
        }

        table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.08);
        }

        .table {
            margin-bottom: 2rem;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 0.9rem 0.75rem;
            font-size: 0.95rem;
            color: #444;
        }

        .table thead {
            background-color: #f0ead8;
            color: #5a4e3c;
            font-weight: 700;
            font-size: 1rem;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #faf7f0;
        }

        .pagination {
            justify-content: center;
            margin-bottom: 2rem;
        }

        .page-item .page-link {
            border-radius: 25px;
            margin: 0 6px;
            color: #6c757d;
            font-weight: 500;
            padding: 6px 14px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .page-item.active .page-link {
            background-color: #20c997;
            border-color: #20c997;
            color: white;
            font-weight: 700;
        }

        .page-link:hover {
            background-color: #d9e6e2;
            color: #20c997;
        }

        .alert-warning {
            max-width: 420px;
            margin-bottom: 1.5rem;
            border-radius: 15px;
            font-size: 1rem;
            background-color: #fff4e5;
            border-color: #ffd97d;
            color: #a86900;
        }
    </style>
</head>

<body>
    <?php include('./compoents/sidebar.php'); ?>
    <div id="content" class="flex-grow-1">
        <?php echo generate_breadcrumb($current_page ?? '訂單列表'); ?>

        <h2>訂單列表</h2>

        <form class="d-flex" method="get" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="輸入收件人電話" aria-label="搜尋"
                value="<?= htmlspecialchars($search) ?>" />
            <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i> 搜尋</button>
        </form>
        <?php if ($search !== ''): ?>
            <div class="mb-3">
                <a href="?" class="btn btn-outline-success">回訂單首頁</a>
            </div>
        <?php endif; ?>

        <?php if ($search !== '' && !$search_exact_found && count($orders) === 0): ?>
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> 找不到訂單。
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
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
                    <th>商品名稱</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php $display_id = ($page - 1) * $limit + 1; ?>
                    <?php foreach ($orders as $order):
                        $time = $order['created_at'];
                        $dt = new DateTime($time);
                        ?>
                        <?php
                        // 查詢商品名稱
                        $stmt = $conn->prepare("
                            SELECT products.name 
                            FROM order_items 
                            JOIN products ON order_items.product_id = products.id 
                            WHERE order_items.order_id = ?
                        ");
                        $stmt->bind_param("i", $order['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $product_names = [];
                        while ($row = $result->fetch_assoc()) {
                            $product_names[] = $row['name'];
                        }
                        $stmt->close();
                        ?>
                        <tr>
                            <td><?= $display_id++ ?></td>
                            <td><?= htmlspecialchars($order['user_id']) ?></td>
                            <td><?= htmlspecialchars(number_format($order['total_price'])) ?> 元</td>

                            <td>
                                <select class="form-select order-status" data-order-id="<?= $order['id'] ?>">
                                    <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>處理中</option>
                                    <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>已付款</option>
                                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>已出貨</option>
                                </select>
                            </td>


                            <td><?= htmlspecialchars($dt->format('y-m-d -H:i')) ?></td>
                            <td><?= htmlspecialchars($order['recipient_name']) ?></td>
                            <td><?= htmlspecialchars($order['recipient_phone']) ?></td>
                            <td><?= nl2br(htmlspecialchars($order['recipient_address'])) ?></td>
                            <td><?= htmlspecialchars(implode(', ', $product_names)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted fst-italic">目前沒有訂單資料。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"
                                aria-label="上一頁">
                                <span aria-hidden="true">&laquo; 上一頁</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"
                                aria-label="下一頁">
                                <span aria-hidden="true">下一頁 &raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
        </script>
    <script src="./js/sidebar.js"></script>
<script>document.querySelectorAll('.order-status').forEach(select => {
  select.addEventListener('change', function () {
    const orderId = this.getAttribute('data-order-id');
    const newStatus = this.value;

    fetch('../action/update_vieworders.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ order_id: orderId, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('訂單狀態更新成功！');
      } else {
        alert('更新失敗：' + data.message);
      }
    })
    .catch(() => alert('網路錯誤，請稍後再試。'));
  });
});
</script>
</html>