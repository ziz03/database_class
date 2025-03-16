<!DOCTYPE html>
<html>
<head>
    <title>顯示資料庫資料</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>資料庫資料</h1>

<?php

// 取得環境變數，如果沒設定就使用預設值
$host = getenv('MYSQL_HOST') ?: 'db'; // 使用 ?: 作為預設值
$username = 'php_docker';
$password = 'password';
$database = 'php_docker';

// 建立連線
$connect = mysqli_connect($host, $username, $password, $database);

// 檢查連線
if (!$connect) {
    die("連線失敗: " . mysqli_connect_error());
}

// 設定編碼 (建議，避免中文亂碼)
mysqli_set_charset($connect, "utf8mb4");

// 設定要查詢的資料表名稱
$table_name = "php_docker_table";

// 查詢語法
$query = "SELECT ID, Title, Body, Date_Created FROM `$table_name`";

// 執行查詢
$result = mysqli_query($connect, $query);

// 檢查查詢結果
if (!$result) {
    die("查詢失敗: " . mysqli_error($connect));
}

// 顯示資料
if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<thead><tr><th>ID</th><th>Title</th><th>Body</th><th>Date Created</th></tr></thead>";
    echo "<tbody>";

    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["ID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Title"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Body"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Date_Created"]) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>資料表沒有資料。</p>";
}

// 關閉連線
mysqli_close($connect);

?>

</body>
</html>