<?php
session_start();

if (isset($_POST['account']) && isset($_POST['password'])) {
    $account = $_POST['account'];
    $password = $_POST['password'];
    if ($account == 'admin' && $password == '123456') {
        header('Location: ../index.php');
        $_SESSION['loggedin'] = true;
        exit();
        echo '登入成功';
    } else {
        echo '帳號或密碼錯誤';
    }
}
