<?php
session_start();
require_once 'database.php'; // 資料庫連線放在這個檔案

if (isset($_POST['account']) && isset($_POST['password'])) 
{
    $account = $_POST['account'];
    $password = $_POST['password'];

    // 使用 prepared statement 避免 SQL Injection
    $stmt = $conn->prepare("SELECT id, password, name, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $account);
    $stmt->execute();
    $result = $stmt->get_result();

    // 確認是否有該帳號
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // 驗證雜湊密碼
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            // 根據角色導向不同頁面
            if ($row['role'] === 'admin')
            {
                header("Location: ../admin/dashboard.php"); // 管理員頁面還沒do暫定這樣
            } 
            else 
            {
                header("Location: ../index2.php"); // 一般使用者
            }
            
            exit();
        } 
        else 
        {
            // 密碼錯誤，重定向並帶上錯誤訊息
            header("Location: ../login.php?error=密碼錯誤");
            exit();
        }
    } 
    else 
    {
        // 找不到帳號，重定向並帶上錯誤訊息
        header("Location: ../login.php?error=找不到帳號");
        exit();
    }

    $stmt->close();
} 
else
{
    // 未填寫帳號或密碼，重定向並帶上錯誤訊息
    header("Location: ../login.php?error=請填寫帳號和密碼");
    exit();
}

$conn->close();
?>