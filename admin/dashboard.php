<?php
session_start();
require_once('../action/common.php');
require_once 'compoents/breadcrumb.php';
$userName = check_login();
$user_texts = getaboutme();
// print_r($user_texts);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>後台</title>
    <link rel="icon" href="../image\blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/sidebar.css">
</head>

<body>

    <?php include('./compoents/sidebar.php'); ?>
    <!-- 主要內容區域 -->
    <div id="content" class="flex-grow-1 p-3">
        <!-- 麵包屑導航 -->
        <?php echo generate_breadcrumb($current_page); ?>

        <!-- 歡迎區域 -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0">後台管理系統</h1>
                <h5 class="text-muted">歡迎回來，<?php echo htmlspecialchars($userName); ?>！</h5>
            </div>
        </div>

        <!-- 個人資料卡片區域 -->
        <div class="row">
            <?php foreach ($user_texts as $user_text): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow" style="max-width: 300px; border: none; background-color: #212529; overflow: hidden;">
                        <!-- 上半部分放背景圖片 -->
                        <div class="card-header p-0 border-0 bg-dark">
                            <img src="../image/blackLOGO.png" class="card-img-top" alt="背景圖片" style="height: 300px; object-fit: cover;">
                        </div>

                        <!-- 中間放個人頭像 -->
                        <div class="position-relative bg-dark" style="margin-top: -70px; z-index: 2;">
                            <img src="<?= htmlspecialchars($user_text['img_url'] ?? '../image/default_avatar.jpg') ?>" class="rounded-circle mx-auto d-block border border-white"
                                style="width: 100px; height: 100px; object-fit: cover; border-width: 3px !important;">
                        </div>

                        <!-- 下半部分黑色區域放社交媒體連結和介紹 -->
                        <div class="card-body bg-dark text-white" style="padding-top: 40px;">
                            <!-- 添加個人介紹 -->
                            <div class="text-center mb-4">
                                <h5 class="mb-2"><?= htmlspecialchars($user_text['name'] ?? '用戶') ?></h5>
                                <p class="small mb-1"><?= htmlspecialchars($user_text['content'] ?? '') ?></p>
                                <p class="small"><?= htmlspecialchars($user_text['describe'] ?? '') ?></p>
                            </div>

                            <!-- GitHub 圖標連結置中 -->
                            <div class="d-flex justify-content-center">
                                <a href="<?= htmlspecialchars($user_text['github_url'] ?? '#') ?>" target="_blank">
                                    <button class="btn btn-light" style="width: 40px; height: 40px; padding: 6px 0;">
                                        <i class="bi bi-github"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


        <!-- <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">專案總數</h6>
                                        <h2 class="mb-0">12</h2>
                                    </div>
                                    <i class="bi bi-folder fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">完成任務</h6>
                                        <h2 class="mb-0">8</h2>
                                    </div>
                                    <i class="bi bi-check-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">待處理任務</h6>
                                        <h2 class="mb-0">4</h2>
                                    </div>
                                    <i class="bi bi-clock-history fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">貢獻次數</h6>
                                        <h2 class="mb-0">156</h2>
                                    </div>
                                    <i class="bi bi-graph-up fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="./js/sidebar.js"></script>
</body>


</html>