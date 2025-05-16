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
    $classification = trim($_POST['classification']);
    $isbn = trim($_POST['isbn']);
    $image_url = "";

    // 圖片處理邏輯
    if (!empty($_FILES['product_image']['name'])) {
        $upload_dir = "../image/";
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $valid_extensions = ["jpg", "jpeg", "png", "gif", "webp"];

        if (in_array($file_extension, $valid_extensions)) {
            $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $new_filename;
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                $image_url = $target_file;
            } else {
                $error = "圖片上傳失敗，請再試一次。";
            }
        } else {
            $error = "只允許上傳 JPG, JPEG, PNG, GIF 或 WEBP 圖片。";
        }
    } elseif (!empty($_POST['image_url'])) {
        $image_url = trim($_POST['image_url']);
    }

    // 驗證與插入
    if ($name && $price > 0 && $stock >= 0 && !empty($image_url) && $classification && $isbn) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, classification, isbn, image_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssdisss", $name, $description, $price, $stock, $classification, $isbn, $image_url);
        $stmt->execute();
        $stmt->close();
        $success = "商品新增成功！";
    } else {
        $error = $error ?? "請填寫完整且有效的資料，並上傳圖片或提供圖片網址。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>新增商品</title>
    <link rel="icon" href="../image/blackLOGO.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #fefcf6;
            font-family: 'Noto Serif TC', serif;
        }

        h2 {
            font-weight: 600;
            color: #4a4a4a;
            margin-bottom: 1.5rem;
        }

        label.form-label {
            font-weight: 500;
            color: #555;
        }

        .btn-primary {
            background-color: rgb(142, 185, 150);
            border: none;
        }

        .btn-primary:hover {
            background-color: rgb(129, 181, 162);
        }

        .alert-success {
            background-color: #e6f4ea;
            color: #276749;
            border: 1px solid #a3d9a5;
        }

        .alert-danger {
            background-color: #fdecea;
            color: #b03a2e;
            border: 1px solid #e4a19b;
        }

        #image-preview img {
            border-radius: 6px;
            object-fit: contain;
        }

        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include('compoents/sidebar.php'); ?>
    <div id="content" class="flex-grow-1 p-4">
        <?php echo generate_breadcrumb($current_page); ?>

        <div class="container py-4 px-5 bg-white shadow-sm rounded-3" style="max-width: 720px;">
            <h2 class="text-center">新增商品</h2>

            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center" role="alert"><?= htmlspecialchars($success) ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger text-center" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="mb-4">
                    <label for="name" class="form-label">商品名稱</label>
                    <input type="text" id="name" name="name" class="form-control form-control-lg" required
                        placeholder="請輸入商品名稱" />
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">商品描述</label>
                    <textarea id="description" name="description" rows="4" class="form-control" required
                        placeholder="請輸入商品描述"></textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="price" class="form-label">價格 (元)</label>
                        <input type="number" id="price" name="price" step="10" min="0" class="form-control" required
                            placeholder="例如 350" />
                    </div>
                    <div class="col-md-6">
                        <label for="stock" class="form-label">庫存數量</label>
                        <input type="number" id="stock" name="stock" min="0" class="form-control" required
                            placeholder="請輸入庫存數量" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="classification">分類</label>
                    <input
                        type="text"
                        id="classification"
                        name="classification"
                        class="form-control"
                        required
                        placeholder="請輸入商品分類"
                    />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="isbn">ISBN</label>
                    <input
                        type="text"
                        id="isbn"
                        name="isbn"
                        class="form-control"
                        required
                        placeholder="請輸入ISBN"
                    />
                    <div class="form-text">ISBN格式請正確填寫</div>
                </div>

                <div class="mb-4">
                    <label for="product_image" class="form-label">上傳商品圖片</label>
                    <input type="file" id="product_image" name="product_image" class="form-control" accept="image/*" />
                    <div class="form-text">支援格式：JPG, JPEG, PNG, GIF, WEBP</div>
                </div>

                <div class="mb-4">
                    <label for="image_url" class="form-label">或輸入圖片網址</label>
                    <input type="url" id="image_url" name="image_url" class="form-control"
                        placeholder="https://example.com/image.jpg" />
                    <div class="form-text">如果已上傳圖片，此欄位可留空</div>
                </div>

                <div id="image-preview" class="mb-4 d-none">
                    <label class="form-label">圖片預覽</label>
                    <div class="border rounded p-3 text-center bg-light">
                        <img id="preview-img" src="#" alt="圖片預覽" style="max-height: 220px; max-width: 100%;" />
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold">新增商品</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>
    <script src="js/sidebar.js"></script>

    <script>
        // 圖片上傳預覽
        document.getElementById('product_image').addEventListener('change', function () {
            const preview = document.getElementById('preview-img');
            const previewContainer = document.getElementById('image-preview');

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.classList.add('d-none');
                preview.src = '#';
            }
        });

        // 圖片網址預覽
        document.getElementById('image_url').addEventListener('input', function () {
            const preview = document.getElementById('preview-img');
            const previewContainer = document.getElementById('image-preview');

            if (this.value.trim() !== '') {
                preview.src = this.value;
                previewContainer.classList.remove('d-none');
                preview.onerror = function () {
                    previewContainer.classList.add('d-none');
                };
            } else {
                previewContainer.classList.add('d-none');
                preview.src = '#';
            }
        });
    </script>
</body>

</html>
