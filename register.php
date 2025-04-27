<?php
// 放在 login.php 頁面的適當位置（通常在 <body> 標籤內的開始處）
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    echo '<script>alert("' . $error . '");</script>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

</head>

<body>
    <?php
    include 'compoents/nav.php';
    ?>

    <section name="login">
        <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center">
            <div class="card shadow w-100" style="max-width: 900px;">
                <div class="row g-0">

                    <!-- 左邊登入表單 -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="p-4 w-100" style="max-width: 400px;">
                            <h4 class="mb-3">歡迎加入</h4>
                            <hr>
                            <form action="action/register.php" method="post">
                                <div class="mb-3">
                                    <label for="account" class="form-label">帳號</label>
                                    <input type="text" class="form-control form-control-lg" id="account" name="account" placeholder="請輸入帳號(email)" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">名稱</label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="請輸入名稱(name)" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">密碼</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="請輸入密碼" required>
                                </div>
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-dark btn-lg">註冊</button>
                                </div>
                                <div class="d-grid mb-3">
                                    <a href="login.php" class="btn btn-outline-dark btn-lg">已有帳號?</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- 右邊品牌區塊 -->
                    <div class="col-md-6 bg-dark text-white d-flex align-items-center justify-content-center flex-column p-4">
                        <img src="https://sitestorage.notorious-2019.com/icon/NOTORIOUS_logo.svg" alt="NOTORIOUS" class="mb-3" style="max-width: 300px;">
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php
    include 'compoents/footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>