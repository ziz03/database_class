<?php
session_start();
require_once 'database.php';

$step = 'email'; // 預設步驟
$error = '';

// 檢查是否是送出 email 的階段
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email); // 這裡綁定變數，"s" 代表 string
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['reset_email'] = $email;
        $step = 'password';
        header("Location: ../forgotPassword.php?step=password");
    } else {
        $error = '查無此帳號';
    }
}

// 檢查是否是送出新密碼的階段
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['confirm'])) {
    if ($_POST['password'] !== $_POST['confirm']) {
        header("Location: ../forgotPassword.php?error=密碼不一致&step=password");
        exit;
    } elseif (!isset($_SESSION['reset_email'])) {
        header("Location: ../forgotPassword.php?error=無效操作，請重新輸入 email");
        exit;
    } else {
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $_SESSION['reset_email']);
        $stmt->execute();
        unset($_SESSION['reset_email']);
        header("Location: ../login.php?reset=success");
        exit;
    }
}
