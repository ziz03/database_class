<?php
session_start();
require_once('../action/common.php');
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';

$userName = check_login();
$current_page = "products";
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理</title>
    <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/sidebar.css">
</head>

<body>
    <?php include('compoents/sidebar.php'); ?>

    <div class="content-wrapper flex-grow-1 p-3">
        <?php echo generate_breadcrumb($current_page); ?>

        <h2>商品管理</h2>

        <a href="product_add.php" class="btn btn-success mb-3">新增商品</a>

        <?php
        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        $result = $conn->query($sql);
        ?>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>商品ID</th>
                    <th>名稱</th>
                    <th>價格</th>
                    <th>庫存</th>
                    <th>圖片</th>
                    <th>新增時間</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>$<?= htmlspecialchars($row['price']) ?></td>
                        <td><?= htmlspecialchars($row['stock']) ?></td>
                        <td><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="商品圖片" style="height: 50px;"></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./js/sidebar.js"></script>
</body>

</html>
