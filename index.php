<?php
session_start(); // 啟用 session
include 'database.php'; // 包含資料庫連接檔案

// 範例：從資料庫中獲取一些資訊 (可以替換成你需要的資料)
$sql = "SELECT ID, Title FROM php_docker_table LIMIT 6"; // 範例查詢
$result = $conn->query($sql);

$products = []; // 儲存產品資料的陣列

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $products[] = $row; // 將每一列資料加入到陣列
  }
}

?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notorious</title>
  <link rel="stylesheet" href="css/style.css"> <!-- 連結到你的 CSS 樣式表 -->
  <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">
</head>
<body>

  <header>
    <img src="https://sitestorage.notorious-2019.com/icon/NOTORIOUS_logo.svg" alt="NOTORIOUS_logo">
    <h1>Notorious</h1>
  </header>

  <nav>
    <a href="#">首頁</a>
    <a href="#">產品</a>
    <a href="#">關於我們</a>
    <a href="#">聯絡我們</a>
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
      <a href="logout.php">登出</a>
    <?php else: ?>
      <a href="login.php">登入</a>
      <a href="register.php">註冊</a>
    <?php endif; ?>
  </nav>

  <div class="container">
    <h2>精選產品</h2>
    <div class="product-grid">
      <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
          <div class="product">
            <img src="<?php echo htmlspecialchars($product['image_url']??"你他媽沒放URL"); ?>" alt="<?php echo htmlspecialchars($product['Title']); ?>">
            <h3><?php echo htmlspecialchars($product['Title']); ?></h3>
            <p><a href="product_details.php?id=<?php echo htmlspecialchars($product['ID']); ?>">查看詳情</a></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>目前沒有產品。</p>
      <?php endif; ?>
    </div>
  </div>

  <footer>
    © <?php echo date("Y"); ?> 網站名稱. 保留所有權利.
  </footer>

</body>
</html>

<?php $conn->close(); ?>