<?php
session_start();
require_once('../action/common.php');
require_once 'compoents/breadcrumb.php';
$userName = check_login();
$user = get_user();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>調整權限</title>
    <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/sidebar.css">
</head>

<body>
    <?php include('./compoents/sidebar.php'); ?>

    <div class="content-wrapper flex-grow-1 p-3">
        <!-- 麵包屑導航 -->
        <?php echo generate_breadcrumb($current_page); ?>

        <div class="container mt-5">

            <h2 class="mb-4">使用者資料</h2>

            <table class="table table-bordered table-hover">
                <thead class="table-info">
                    <tr>
                        <th>姓名</th>
                        <th>Email</th>
                        <th>角色</th>
                        <th>更換權限</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($user): ?>
                        <?php foreach ($user as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['role']); ?></td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">查無資料</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="./js/sidebar.js"></script>

</body>

</html>