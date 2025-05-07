<?php
session_start();
require_once('../action/common.php');
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';

$userName = check_login();


// 處理權限更新
if (isset($_POST['update_role']) && isset($_POST['user_id']) && isset($_POST['new_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = trim($_POST['new_role']);

    $sql = "UPDATE user SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "權限更新成功！";
    } else {
        $_SESSION['message'] = "更新失敗，請稍後再試。";
    }

    header("Location: changestatus.php");
    exit;
}

// 撈取使用者清單
function get_user_list($conn) {
    $sql = "SELECT id, name, email, role FROM user";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// 撈取所有角色清單（從資料庫中 DISTINCT）
function get_roles($conn) {
    $sql = "SELECT DISTINCT role FROM user";
    $result = $conn->query($sql);
    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['role'];
    }
    return $roles;
}

$user = get_user_list($conn);
$roles = get_roles($conn);
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>調整權限</title>
    <link rel="icon" href="https://sitestorage.notorious-2019.com/icon/icon_logo.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
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
                        <td>
                            <form method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($u['id']) ?>">
                                <select name="new_role" class="form-select form-select-sm me-2" required>
                                    <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role) ?>"
                                        <?= ($role === $u['role']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-primary btn-sm">更新</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">查無資料</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 使用 JavaScript alert 顯示訊息 -->
    <?php if (isset($_SESSION['message'])): ?>
    <script>
    alert("<?= htmlspecialchars($_SESSION['message']) ?>");
    </script>
    <?php
        unset($_SESSION['message']);
    endif;
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="./js/sidebar.js"></script>

</body>

</html>