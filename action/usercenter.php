<?php
session_start();
require_once 'database.php'; // 資料庫連線放在這個檔案

if (isset($_POST['account']) && isset($_POST['editname'])) {
    $account = $_POST['account'];
    $password = $_POST['editname'];

    // 使用 prepared statement 避免 SQL Injection
    $stmt = $conn->prepare("SELECT name FROM user WHERE email = ?");
    $stmt->bind_param("s", $account);
    $stmt->execute();
    $result = $stmt->get_result();

    // 確認是否有該帳號
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // echo $row['name'];

        $stmt = $conn->prepare("UPDATE user SET name = ? WHERE email = ?");
        $stmt->bind_param("ss", $password, $account);
        $stmt->execute();
        $stmt->close();
        header("Location: ../index.php");
        exit();
    }
    $stmt->close();
} else {
    // 未填寫帳號或密碼，重定向並帶上錯誤訊息
    header("Location: ../login.php?error=請填寫帳號和密碼");
    exit();
}

$conn->close();
