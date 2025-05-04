<?php
session_start();
require_once '../action/common.php';
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';

$userName = check_login();
$current_page = "products";
// 處理刪除操作
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = intval($_GET['delete']);

    // 執行刪除操作
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "商品已成功刪除！";
    } else {
        $_SESSION['message'] = "刪除失敗，請稍後再試。";
    }

    // 重定向到相同頁面以清除 URL 參數
    header("Location: products.php");
    exit;
}
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
                    <th>動作</th>
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
                        <td> <a href="products.php?delete=<?= $row['id'] ?>" 
                                class="btn btn-primary btn-sm mt-2"
                                onclick="return confirm('確定要刪除此商品嗎？')">刪除</a></td><!-- 增加刪除連結 帶她的id就好 編輯也是一樣帶ID就可以顯示他的所有訊息了 -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- 使用 JavaScript alert 顯示訊息 -->
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            alert("<?= htmlspecialchars($_SESSION['message']) ?>");
        </script>
    <?php
        // 顯示後清除訊息，避免重新整理後再次顯示
        unset($_SESSION['message']);
    endif;
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./js/sidebar.js"></script>
</body>

</html>