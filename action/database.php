<?php

$servername = 'localhost';  // 你的資料庫伺服器
$username = 'root';
$password = '';
$dbname = "class";    // 你的資料庫名稱

// 建立連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
  die("連接失敗: " . $conn->connect_error);
}

// 設定字元編碼 (建議)
$conn->set_charset("utf8"); // 或者 utf8mb4 如果你需要更多字符
