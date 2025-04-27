<?php
require_once('database.php');

function check_role()
{
    if ($_SESSION['role'] == 'user') {
        header("Location: ../index.php");
    }
}
function logoutAndRedirect($redirectUrl = '../index.php')
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
    header("Location: $redirectUrl");
    exit();
}
function check_login()
{
    check_role();
    // 檢查是否有登入
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: ../login.php");
        exit;
    }

    // 取得使用者名字
    $name = $_SESSION['name'];
    return $name;
}

function get_user()
{
    global $conn;
    check_login();
    $sql = "SELECT email, name, role FROM user";
    $result = $conn->query($sql);
    //印出來看是啥
    // while ($row = $result->fetch_assoc()) {
    //     print_r($row);
    // }
    if ($result && $result->num_rows > 0) {
        $all_rows = $result->fetch_all(MYSQLI_ASSOC);
        // print_r($all_rows); // 印出全部資料
        return $all_rows;
    } else {
        return null; // 找不到這個 user
    }
}
