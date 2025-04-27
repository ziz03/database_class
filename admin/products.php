<?php
session_start();
require_once('../action/common.php');
require_once 'compoents/breadcrumb.php';
$userName = check_login();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>products</title>
    <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/sidebar.css">
</head>

<body>
    <?php include('./compoents/sidebar.php'); ?>
    <div class="content-wrapper flex-grow-1 p-3">
        <!-- 麵包屑導航 -->
        <?php echo generate_breadcrumb($current_page); ?>

        <!-- 這裡將會是各頁面的主要內容 -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="./js/sidebar.js"></script>
</body>

</html>