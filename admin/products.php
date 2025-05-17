<?php
session_start();
require_once '../action/common.php';
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';

$userName = check_login();
$current_page = "products";

// 刪除操作
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: products.php");
    exit;
}

// 更新操作
if (
    isset($_POST['update_all']) &&
    isset($_POST['product_id']) &&
    isset($_POST['new_name']) &&
    isset($_POST['new_price']) &&
    isset($_POST['new_stock']) &&
    isset($_POST['new_classification']) &&    // 新增分類欄位
    isset($_POST['new_isbn'])                 // 新增ISBN欄位
) {
    $product_id = intval($_POST['product_id']);
    $new_name = trim($_POST['new_name']);
    $new_price = floatval($_POST['new_price']);
    $new_stock = intval($_POST['new_stock']);
    $new_classification = trim($_POST['new_classification']);
    $new_isbn = trim($_POST['new_isbn']);

    $sql = "UPDATE products SET name = ?, price = ?, stock = ?, classification = ?, isbn = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdissi", $new_name, $new_price, $new_stock, $new_classification, $new_isbn, $product_id);
    $stmt->execute();
    header("Location: products.php");
    exit;
}

// 搜尋處理
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = '%' . $conn->real_escape_string($search) . '%';

// 分頁設定
$perPage = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$startFrom = ($page - 1) * $perPage;

// 取得總筆數
$total_sql = "SELECT COUNT(*) FROM products WHERE name LIKE ?";
$stmt_total = $conn->prepare($total_sql);
$stmt_total->bind_param("s", $search_sql);
$stmt_total->execute();
$stmt_total->bind_result($total_records);
$stmt_total->fetch();
$stmt_total->close();

$total_pages = ceil($total_records / $perPage);

// 取得當前頁資料
$sql = "SELECT * FROM products WHERE name LIKE ? ORDER BY id ASC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $search_sql, $startFrom, $perPage);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>商品管理列表</title>
    <link rel="icon" href="../image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- 文青字體 -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Serif TC', serif;
            background-color: #f0f0f0;
            color: #333;
        }

        .content-wrapper {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            font-size: 2.5rem;
            font-weight: 700;
            color: #3e3e3e;
            margin-bottom: 30px;
        }

        .btn-success {
            background-color: #6e7a59;
            border: none;
            font-size: 1.2rem;
        }

        .btn-outline-primary {
            color: #6e7a59;
            border-color: #6e7a59;
            font-weight: bold;
        }

        .btn-outline-primary:hover {
            background-color: #6e7a59;
            color: #fff;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 1rem;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table th {
            background-color: #f8f8f8;
            font-weight: 600;
            color: #555;
        }

        .table td {
            background-color: #fff;
        }

        .form-control-sm {
            font-size: 1rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-item {
            margin: 0 4px;
        }

        .pagination .page-item.active .page-link {
            background-color: #6e7a59;
            border-color: #6e7a59;
            color: white;
        }

        .pagination .page-link {
            color: #6e7a59;
            border-color: #6e7a59;
        }

        .pagination .page-link:hover {
            background-color: #6e7a59;
            color: white;
        }

        .search-form {
            max-width: 400px;
            margin-bottom: 30px;
        }

        .breadcrumb {
            background-color: #f8f8f8;
        }

        .search-form .input-group input {
            font-size: 1rem;
        }

        .search-form .input-group button {
            font-size: 1rem;
            border-radius: 0.5rem;
        }

        /* 文青風格按鈕 */
        .btn-outline-success {
            color: #4CAF50;
            /* 深綠色 */
            border-color: #4CAF50;
            background-color: #e8f5e9;
            /* 淺綠色背景 */
        }

        .btn-outline-success:hover {
            color: #ffffff;
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .btn-outline-danger {
            color: #D32F2F;
            /* 深紅色 */
            border-color: #D32F2F;
            background-color: #FFEBEE;
            /* 淺紅色背景 */
        }

        .btn-outline-danger:hover {
            color: #ffffff;
            background-color: #D32F2F;
            border-color: #D32F2F;
        }
    </style>
</head>

<body>
    <?php include('compoents/sidebar.php'); ?>
    <div id="content" class="flex-grow-1 p-3">
        <?php echo generate_breadcrumb($current_page); ?>
        <h2 class="page-header">商品管理列表</h2>
        <a href="product_add.php" class="btn btn-success mb-3">新增商品</a>

        <!-- 搜尋列 -->
        <form class="search-form" method="GET" action="products.php">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="搜尋商品名稱"
                    value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-primary" type="submit">搜尋</button>
            </div>
        </form>

        <?php if (isset($_GET['search'])): ?>
            <div class="mb-3">
                <a href="products.php"
                    class="btn btn-outline-primary d-flex align-items-center gap-2 px-2 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    回到管理商品列表首頁
                </a>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>商品ID</th>
                    <th>名稱</th>
                    <th>價格</th>
                    <th>庫存</th>
                    <th>分類</th> 
                    <th>ISBN</th> 
                    <th>圖片</th>
                    <th>新增時間</th>
                    <th>動作</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = $startFrom + 1; ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $time=$row['created_at'];
                    $dt=new DateTime($time);
                    
                    
                    ?>
                    <tr>
                        <form method="POST" class="d-flex align-items-center">
                            <td><?= $index++ ?></td>

                            <td><input type="text" name="new_name" value="<?= htmlspecialchars($row['name']) ?>"
                                    class="form-control form-control-sm" required></td>
                            <td><input type="number" name="new_price" step="0.01"
                                    value="<?= htmlspecialchars($row['price']) ?>" class="form-control form-control-sm"
                                    required></td>
                            <td><input type="number" name="new_stock" value="<?= htmlspecialchars($row['stock']) ?>" min="0"
                                    class="form-control form-control-sm" required></td>
                            <!-- 新增分類欄位輸入 -->
                            <td><input type="text" name="new_classification"
                                    value="<?= htmlspecialchars($row['classification']) ?>"
                                    class="form-control form-control-sm"></td>

                            <!-- 新增ISBN欄位輸入 -->
                            <td><input type="text" name="new_isbn" value="<?= htmlspecialchars($row['isbn']) ?>"
                                    class="form-control form-control-sm"></td>

                            <td><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="商品圖片" style="height: 50px;"></td>
                            <td><?= $dt->format('y-m-d -H:i'); ?></td>
                            <td>
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($row['id']) ?>">
                                <div class="d-flex flex-column gap-1">
                                    <button type="submit" name="update_all"
                                        class="btn btn-outline-primary btn-sm ">更新<br>資料</button>
                                    <a href="products.php?delete=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('確定要刪除此商品嗎？')">刪除</a>
                                </div>
                            </td>

                        </form>
                    </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-danger">⚠ 無符合的搜尋結果</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 分頁列 -->
        <nav>
            <ul class="pagination justify-content-center">
                <!-- 上一頁 -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="products.php?page=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>">上一頁</a>
                </li>

                <!-- 頁碼 -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link"
                            href="products.php?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- 下一頁 -->
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="products.php?page=<?= min($total_pages, $page + 1) ?>&search=<?= urlencode($search) ?>">下一頁</a>
                </li>
            </ul>
        </nav>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <script>alert("<?= htmlspecialchars($_SESSION['message']) ?>");</script>
        <?php unset($_SESSION['message']); endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/sidebar.js"></script>
</body>

</html>