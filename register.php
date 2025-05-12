<?php
session_start();
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    echo '<script>alert("' . $error . '");</script>';
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>註冊 | 人生研究室</title>
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f7f6f2;
            font-family: 'Noto Serif TC', serif;
            color: #3e3e3e;
        }

        .custom-card {
            background: #faf8f5;
            border: 1px solid #eae5df;
            border-radius: 1.5rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }

        .form-control {
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .btn-dark {
            background-color: #3e3e3e;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-dark:hover {
            background-color: #000;
        }

        .btn-outline-dark:hover {
            background-color: #3e3e3e;
            color: white;
        }

        h1,
        h4 {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'compoents/nav.php'; ?>

    <section class="py-5">
        <div class="container py-4">
            <div class="custom-card p-5 mx-auto" style="max-width: 960px;">
                <div class="row g-4 align-items-center">
                    <!-- 左側：註冊表單 -->
                    <div class="col-md-6">
                        <h4 class="mb-3">歡迎加入</h4>
                        <p class="mb-4 text-muted">建立新帳號開始探索。</p>
                        <form action="action/register.php" method="post">
                            <div class="mb-3">
                                <label for="account" class="form-label">帳號（Email）</label>
                                <input type="text" class="form-control" id="account" name="account" placeholder="請輸入帳號"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">名稱</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="請輸入名稱"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">密碼</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="請輸入密碼" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-dark btn-lg rounded-pill">註冊</button>
                                <a href="login.php" class="btn btn-outline-dark btn-lg rounded-pill">已有帳號？前往登入</a>
                            </div>
                        </form>
                    </div>

                    <!-- 右側：品牌介紹區 -->
                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center p-5"
                        style="background-color: #faf8f5; color: #4a3f35;">
                        <i class="bi bi-book-half fs-2 mb-3" style="color: #6e5843;"></i>
                        <h3 class="fw-bold mb-2">人生研究室</h3>
                        <p class="text-center mb-4" style="max-width: 300px;">
                            人生的起點不是誕生，<br>
                            而是與好書結緣的那一刻。<br>
                            
                        </p>
                        <blockquote class="text-muted fst-italic" style="max-width: 280px;">
                            “The cost of ignorance is always higher than the price of knowledge.” <br>- Margaret Atwood
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'compoents/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>