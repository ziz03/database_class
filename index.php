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
  <title>首頁 - 網站名稱 (替換成你的網站名稱)</title>
  <link rel="stylesheet" href="style.css"> <!-- 連結到你的 CSS 樣式表 -->
  <style>
    /* 內嵌樣式，方便快速展示，建議將樣式移動到 style.css 中 */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f0f0f0;
    }

    header {
      background-color: #333;
      color: white;
      padding: 1em 0;
      text-align: center;
    }

    nav {
      background-color: #444;
      color: white;
      padding: 0.5em 0;
      text-align: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      padding: 0.5em 1em;
      display: inline-block;
    }

    .container {
      width: 90%;
      margin: 20px auto;
      background-color: white;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* 響應式欄位 */
      gap: 20px;
    }

    .product {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
      border-radius: 5px;
      background-color: #f9f9f9;
    }

    .product img {
      max-width: 100%;
      height: auto;
    }

    footer {
      background-color: #333;
      color: white;
      text-align: center;
      padding: 1em 0;
      position: fixed;  /* 保持在底部 */
      bottom: 0;
      width: 100%;
    }

    /* 可根據你的網站需求自訂更多樣式 */
  </style>
</head>
<body>

  <header>
    <h1>網站名稱 (替換成你的網站名稱)</h1>
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
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
            <h3><?php echo htmlspecialchars($product['title']); ?></h3>
            <p><a href="product_details.php?id=<?php echo htmlspecialchars($product['id']); ?>">查看詳情</a></p>
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