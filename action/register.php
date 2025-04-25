<?php
session_start();
require_once '../database.php'; // 連線資料庫

// 檢查是否從表單送出資料
if (isset($_POST['account'], $_POST['password'], $_POST['name'])) 
{
    $account = trim($_POST['account']);
    $password = $_POST['password'];
    $name = trim($_POST['name']);

    // 簡單驗證
    if (empty($account) || empty($password) || empty($name)) 
    {
        header("Location: ../register.php?error=請填寫所有欄位");
        exit();
    }

    // 檢查帳號是否已存在
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $account);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) 
    {
        // 帳號已存在
        $stmt->close();
        header("Location: ../register.php?error=此帳號已被註冊");
        exit();
    }
    $stmt->close();

    // 加密密碼
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 新增使用者到資料庫
    $stmt = $conn->prepare("INSERT INTO user (email, password, name, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
    $stmt->bind_param("sss", $account, $hashed_password, $name);

    if ($stmt->execute()) 
    {
        // 自動登入
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['name'] = $name;
        $_SESSION['role'] = 'user';

        $stmt->close();
        $conn->close();
        header("Location: ../index2.php"); // 回首頁
        exit();
    } 
    else 
    {
        $stmt->close();
        $conn->close();
        header("Location: ../register.php?error=註冊失敗，請稍後再試");
        exit();
    }
}
else 
{
    header("Location: ../register.php?error=請透過表單註冊");
    exit();
}
