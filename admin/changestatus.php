<?php
session_start();
require_once('../action/common.php');
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';

$userName = check_login();

// ✅ 新增使用者功能
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // 密碼加密
    $role = trim($_POST['role']);

    if ($name && $email && $password && $role) {
        $sql = "INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        if ($stmt->execute()) {
            $_SESSION['message'] = "使用者新增成功！";
        } else {
            $_SESSION['message'] = "新增失敗，請稍後再試。";
        }
    } else {
        $_SESSION['message'] = "請填寫所有欄位。";
    }
    header("Location: changestatus.php");
    exit;
}

// ✅ 刪除使用者功能
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $sql = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "使用者刪除成功！";
    } else {
        $_SESSION['message'] = "刪除失敗，請稍後再試。";
    }
    header("Location: changestatus.php");
    exit;
}

// ✅ 權限更新
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
    <link rel="icon" href="../image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Serif TC', serif;
            background-color: #f8f8f8;
        }

        .content-wrapper {
            background: #fff;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            vertical-align: middle !important;
            padding: 1rem;
        }

        .btn-outline-success {
            border-color: #6e7a59;
            color: #6e7a59;
        }

        .btn-outline-success:hover {
            background-color: #6e7a59;
            color: white;
        }

        .btn-outline-danger {
            border-color: #b04a4a;
            color: #b04a4a;
        }

        .btn-outline-danger:hover {
            background-color: #b04a4a;
            color: white;
        }

        .btn-outline-primary {
            border-color: #5a6d8d;
            color: #5a6d8d;
        }

        .btn-outline-primary:hover {
            background-color: #5a6d8d;
            color: white;
        }

        .table th {
            background-color: #e0f2f1;
            color: #37474f;
        }

        .form-select-sm,
        .form-control-sm {
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>

<body>
    <?php include('./compoents/sidebar.php'); ?>

    <div id="content" class="flex-grow-1 p-3">
        <?php echo generate_breadcrumb($current_page); ?>

        <div class="container mt-5">
            <h2 class="mb-4">使用者資料</h2>

            <!-- ✅ 新增使用者區塊 -->
            <div class="card mb-4">
              <div class="card-header rounded-top" style="background-color: #e0f2f1; color: #37474f;">新增使用者</div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control" placeholder="姓名" required>
                        </div>
                        <div class="col-md-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col-md-3">
                            <input type="password" name="password" class="form-control" placeholder="密碼" required>
                        </div>
                        <div class="col-md-2">
                            <select name="role" class="form-select" required>
                                <option value="" disabled selected>選擇角色</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" name="add_user" class="btn btn-outline-success w-100">新增</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ✅ 使用者列表 -->
            <table class="table table-bordered table-hover align-middle text-center rounded">
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>Email</th>
                        <th>角色</th>
                        <th>更換權限</th>
                        <th>刪除</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($user): ?>
                        <?php foreach ($user as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['name']); ?></td>
                                <td><?= htmlspecialchars($u['email']); ?></td>
                                <td><?= htmlspecialchars($u['role']); ?></td>
                                <td>
                                    <form method="POST" class="d-flex align-items-center justify-content-center">
                                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($u['id']) ?>">
                                        <select name="new_role" class="form-select form-select-sm me-2" required>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?= htmlspecialchars($role) ?>" <?= ($role === $u['role']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($role) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_role" class="btn btn-outline-primary btn-sm">更新</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('確定要刪除這個使用者嗎？');">
                                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($u['id']) ?>">
                                        <button type="submit" name="delete_user" class="btn btn-outline-danger btn-sm">刪除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">查無資料</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <script>
            alert("<?= htmlspecialchars($_SESSION['message']) ?>");
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./js/sidebar.js"></script>
</body>

</html>
