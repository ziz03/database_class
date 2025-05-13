<?php
session_start();
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    echo '<script>alert("' . $error . '");</script>';
}
$step = isset($_SESSION['reset_email']) ? 'password' : 'email';
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>忘記密碼 | 人生研究室</title>
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
                    <!-- 左側表單 -->
                    <div class="col-md-6">
                        <?php if ($step === 'email'): ?>
                            <h4 class="mb-3">拯救你的密碼</h4>
                            <p class="mb-4 text-muted">請輸入您的帳號，協助我們確認。</p>
                            <form action="action/forgotPassword.php" method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">帳號（Email）</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="請輸入帳號(email)" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-dark btn-lg rounded-pill">送出</button>
                                    <a href="login.php" class="btn btn-outline-dark btn-lg rounded-pill">突然就想起來了</a>
                                </div>
                            </form>
                        <?php elseif ($step === 'password'): ?>
                            <h4 class="mb-3">請輸入新密碼</h4>
                            <p class="mb-4 text-muted">請輸入您的新密碼並再次確認。</p>
                            <form action="action/forgotPassword.php" method="post">
                                <div class="mb-3">
                                    <label for="password" class="form-label">新密碼</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm" class="form-label">確認密碼</label>
                                    <input type="password" class="form-control" id="confirm" name="confirm" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-dark btn-lg rounded-pill">更新密碼</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- 右側品牌介紹 -->
                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center p-5"
                        style="background-color: #faf8f5; color: #4a3f35;">
                        <i class="bi bi-book-half fs-2 mb-3" style="color: #6e5843;"></i>
                        <h3 class="fw-bold mb-2">人生研究室</h3>
                        <blockquote class="text-muted fst-italic text-center"
                            style="max-width: 300px; font-family: 'Noto Serif TC', serif;">
                            密碼會忘記，<br>
                            讀過的書卻會在舉手投足之間影響著，<br>
                            伴隨著流過的血液，在每一個鼻息之間。

                        </blockquote>

                        <blockquote class="text-muted fst-italic" style="max-width: 280px;">
                            “Knowing yourself is the beginning of all wisdom.” <br> - Aristotle
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