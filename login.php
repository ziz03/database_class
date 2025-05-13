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
    <title>登入 | 人生研究室</title>
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
                    <!-- 左側：表單區 -->
                    <div class="col-md-6">
                        <h4 class="mb-3">歡迎回來</h4>
                        <p class="mb-4 text-muted">請登入以繼續。</p>
                        <form action="action/login.php" method="post">
                            <div class="mb-3">
                                <label for="account" class="form-label">帳號（Email）</label>
                                <input type="text" class="form-control" id="account" name="account" placeholder="請輸入帳號"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">密碼</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="請輸入密碼" required>
                                <div class="mt-2 text-end">
                                    <a href="forgotpassword.php" style="font-size: 0.9rem; color: #7c5e47;">忘記密碼？</a>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-dark btn-lg rounded-pill">登入</button>
                                <a href="register.php" class="btn btn-outline-dark btn-lg rounded-pill">註冊新帳號</a>
                            </div>
                        </form>
                    </div>

                    <!-- 右側品牌介紹 -->
                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center p-5"
                        style="background-color: #faf8f5; color: #4a3f35;">
                        <i class="bi bi-book-half fs-2 mb-3" style="color: #6e5843;"></i>
                        <h3 class="fw-bold mb-2">人生研究室</h3>
                        <blockquote class="text-muted fst-italic text-center"
                            style="max-width: 300px; font-family: 'Noto Serif TC', serif;">
                            我們相信：<br>
                            「最深的感性，來自最深的知性。」<br>
                            透過閱讀與學習，為人生注入新的風景。
                        </blockquote>

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