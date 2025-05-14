<?php
session_start();
require_once '../action/database.php';
require_once('../action/common.php');
require_once 'compoents/breadcrumb.php';
$username = check_login();
$user_text = getaboutme();
// print_r($user_text);
if(isset($_SESSION["message"])){
    echo "<script>alert('" . $_SESSION["message"] . "');</script>";
}
$index = 1;
?>


<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>調整關於我</title>
    <link rel="icon" href="../image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <?php include('compoents/sidebar.php'); ?>
    <div class="content-wrapper flex-grow-1 p-3">
        <?php echo generate_breadcrumb($current_page); ?>
        <h2 class="mt-5 mb-5">開發人員列表</h2>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>流水號 </th>
                    <th>名字</th>
                    <th>簡介</th>
                    <th>描述</th>
                    <th>mail</th>
                    <th>github</th>
                    <th>img_URL</th>
                    <th>動作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_text as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($index++) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['content']) ?></td>
                        <td><?= htmlspecialchars($user['describe']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['github_url']) ?></td>
                        <td><?= htmlspecialchars($user['img_url']) ?></td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-primary me-4"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal"
                                data-user='<?= json_encode($user, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>'>
                                修改
                            </button>
                            <a class="btn btn-danger " href="delete_aboutme.php?id=<?= $user['id'] ?>">刪除
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">修改使用者</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <!-- 可放修改表單 -->
                    <form id="editForm" method="POST" action="../action/update_aboutme.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="modalUserId">
                        <div class="mb-3">
                            <label for="modalUserName" class="form-label">名字</label>
                            <input type="text" class="form-control" name="name" id="modalUserName">
                            <label for="content" class="form-label">簡介</label>
                            <input type="text" class="form-control" name="content" id="content">
                            <label for="describe" class="form-label">描述</label>
                            <input type="text" class="form-control" name="describe" id="describe">
                            <label for="github" class="form-label">github</label>
                            <input type="text" class="form-control" name="github_url" id="github_url">
                            <label for="img" class="form-label">img</label>
                            <input type="file" class="form-control" name="img" id="img">
                            <img id="imgPreview" src="" alt="預覽圖片" class="img-fluid mb-2">
                        </div>
                        <button type="submit" class="btn btn-success ">儲存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php if (isset($_SESSION['message'])): ?>
        <script>
            alert("<?= htmlspecialchars($_SESSION['message']) ?>");
        </script>
    <?php unset($_SESSION['message']);
    endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/sidebar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userData = JSON.parse(button.getAttribute('data-user'));
                console.log(userData);

                // 將 userData 資料填入 modal 的欄位
                document.getElementById('modalUserId').value = userData.id;
                document.getElementById('modalUserName').value = userData.name;
                document.getElementById('content').value = userData.content;
                document.getElementById('describe').value = userData.describe;
                document.getElementById('github_url').value = userData.github_url;
                document.getElementById('imgPreview').src = userData.img_url;
            });
        });
    </script>
</body>

</html>