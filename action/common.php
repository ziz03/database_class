<?php

function check_login()
{
    // 檢查是否有登入
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: ../login.php");
        exit;
    }

    // 取得使用者名字
    $name = $_SESSION['name'];
    return $name;
}
