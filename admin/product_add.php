<?php
session_start();
require_once '../action/database.php';
require_once 'compoents/breadcrumb.php';
require_once('../action/common.php');
$userName = check_login();


// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_url = "";

    // 圖片處理邏輯
    if (!empty($_FILES['product_image']['name'])) {
        // 有上傳文件
        $upload_dir = "../image/";

        // 確保目錄存在
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // 生成唯一檔名
        $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;

        // 檢查是否為圖片
        $valid_extensions = array("jpg", "jpeg", "png", "gif", "webp");
        if (in_array(strtolower($file_extension), $valid_extensions)) {
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                // 上傳成功，設置圖片URL
                $image_url =  $upload_dir . $new_filename;
            } else {
                $error = "圖片上傳失敗，請再試一次。";
            }
        } else {
            $error = "只允許上傳 JPG, JPEG, PNG, GIF 或 WEBP 圖片。";
        }
    } elseif (!empty($_POST['image_url'])) {
        // 沒有上傳文件但有提供圖片URL
        $image_url = trim($_POST['image_url']);
    }

    // 驗證和插入資料庫
    if ($name && $price > 0 && $stock >= 0 && !empty($image_url)) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image_url);
        $stmt->execute();
        $stmt->close();

        $success = "商品新增成功！";
    } else {
        $error = isset($error) ? $error : "請填寫完整且有效的資料，並上傳圖片或提供圖片網址。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增商品</title>
    <link rel="icon" href="../image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <?php include('compoents/sidebar.php'); ?>
    <div class="content-wrapper flex-grow-1 p-3">
        <!-- 麵包屑導航 -->
        <?php echo generate_breadcrumb($current_page); ?>

        <div class="container mt-5">
            <h2>新增商品</h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">商品名稱</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">商品描述</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">價格 (元)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">庫存數量</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">上傳商品圖片</label>
                    <input type="file" name="product_image" class="form-control" accept="image/*">
                    <div class="form-text">支援的格式：JPG, JPEG, PNG, GIF, WEBP</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">或輸入圖片網址</label>
                    <input type="text" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                    <div class="form-text">如果已上傳圖片，此欄位可留空</div>
                </div>

                <div class="mb-3">
                    <div id="image-preview" class="d-none">
                        <label class="form-label">圖片預覽</label>
                        <div class="border p-2 text-center">
                            <img id="preview-img" src="#" alt="圖片預覽" style="max-height: 200px; max-width: 100%;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">新增商品</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="js/sidebar.js"></script>

    <script>
        // 圖片預覽功能
        document.querySelector('input[name="product_image"]').addEventListener('change', function(e) {
            const preview = document.getElementById('preview-img');
            const previewContainer = document.getElementById('image-preview');

            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                }

                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.classList.add('d-none');
            }
        });

        // 圖片網址預覽功能
        document.querySelector('input[name="image_url"]').addEventListener('input', function(e) {
            const preview = document.getElementById('preview-img');
            const previewContainer = document.getElementById('image-preview');

            if (this.value.trim() !== '') {
                preview.src = this.value;
                previewContainer.classList.remove('d-none');

                // 檢查圖片是否能載入
                preview.onerror = function() {
                    previewContainer.classList.add('d-none');
                }
            } else {
                previewContainer.classList.add('d-none');
            }
        });
    </script>
</body>

</html>