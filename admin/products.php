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

    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "商品已成功刪除！";
    } else {
        $_SESSION['message'] = "刪除失敗，請稍後再試。";
    }

    header("Location: products.php");
    exit;
}

// 處理名稱、價格、庫存更新操作
if (
    isset($_POST['update_all']) &&
    isset($_POST['product_id']) &&
    isset($_POST['new_name']) &&
    isset($_POST['new_price']) &&
    isset($_POST['new_stock'])
) {
    $product_id = intval($_POST['product_id']);
    $new_name = trim($_POST['new_name']);
    $new_price = floatval($_POST['new_price']);
    $new_stock = intval($_POST['new_stock']);

    if ($new_name === "" || $new_price < 0 || $new_stock < 0) {
        $_SESSION['message'] = "請填寫正確的資料。";
    } else {
        $sql = "UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdii", $new_name, $new_price, $new_stock, $product_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "商品資料已成功更新！";
        } else {
            $_SESSION['message'] = "更新失敗，請稍後再試。";
        }
    }

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="./css/sidebar.css">
</head>

<body>
    <?php include('compoents/sidebar.php'); ?>

    <div class="content-wrapper flex-grow-1 p-3">
        <?php echo generate_breadcrumb($current_page); ?>

        <h2>商品管理</h2>

        <a href="product_add.php" class="btn btn-success mb-3">新增商品</a>

        <?php
        $sql = "SELECT * FROM products ORDER BY id ASC ";
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
                    <form method="POST" class="d-flex align-items-center">
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td>
                            <input type="text" name="new_name" value="<?= htmlspecialchars($row['name']) ?>"
                                class="form-control form-control-sm" required>
                        </td>
                        <td>
                            <input type="number" name="new_price" step="0.01"
                                value="<?= htmlspecialchars($row['price']) ?>"
                                class="form-control form-control-sm" required>
                        </td>
                        <td>
                            <input type="number" name="new_stock" value="<?= htmlspecialchars($row['stock']) ?>" min="0"
                                class="form-control form-control-sm" required>
                        </td>
                        <td><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="商品圖片" style="height: 50px;"></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit" name="update_all" class="btn btn-primary btn-sm mt-2">更新資料</button>
                            <a href="products.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm mt-2"
                                onclick="return confirm('確定要刪除此商品嗎？')">刪除</a>
                        </td>
                    </form>
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
        unset($_SESSION['message']);
    endif;
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="./js/sidebar.js"></script>
</body>

</html>
