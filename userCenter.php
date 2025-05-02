<?php 
session_start();
require_once('action/common.php');
$userName=check_login();
// get_user_byemail($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員中心</title>
    <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

</head>

<body>
    <?php
    include 'compoents/nav.php';
    ?>

    <section name="edit">
    <h1>歡迎<?php echo htmlspecialchars($userName);?></h1>
        <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center">
            <div class="card shadow w-100" style="max-width: 900px;">
                <div class="row g-0">

                    <!-- 左邊登入表單 -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="p-4 w-100" style="max-width: 400px;">
                            <h4 class="mb-3">更改名稱</h4>
                            <hr>
                            <form action="action/usercenter.php" method="post">
                                <div class="mb-3">
                                    <label for="account" class="form-label">帳號</label>
                                    <input type="text" class="form-control form-control-lg" id="account" name="account" placeholder="請輸入帳號(email)" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editname" class="form-label">新名字</label>
                                    <input type="text" class="form-control form-control-lg" id="editname" name="editname" placeholder="請輸入新名字" required>
                                </div>
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-dark btn-lg">更改名字</button>
                                </div>
                                <div class="d-grid mb-3">
                                    <a href="register.php" class="btn btn-outline-dark btn-lg">註冊</a>
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