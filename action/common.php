<?php
require_once('database.php');


function logoutAndRedirect($redirectUrl = '../index.php')
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
    header("Location: $redirectUrl");
    exit();
}
function check_login()
{

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        if ($_SESSION['role'] == 'user') {
            header("Location: ../index.php");
        }
        header("Location: ../login.php");
        exit;
    }
    return $_SESSION['name'];
}


function get_user()
{
    global $conn;
    check_login();
    $sql = "SELECT email, name, role FROM user";
    $result = $conn->query($sql);
    //印出來看是啥
    // while ($row = $result->fetch_assoc()) {
    //     print_r($row);
    // }
    if ($result && $result->num_rows > 0) {
        $all_rows = $result->fetch_all(MYSQLI_ASSOC);
        // print_r($all_rows); 
        return $all_rows;
    } else {
        return null; // 找不到這個 user
    }
}
function get_user_byemail($email)
{
    global $conn;
    check_login();
    $sql = "SELECT  name, role FROM user WHERE email = '$email'";
    ;
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $all_rows = $result->fetch_all(MYSQLI_ASSOC);
        print_r($all_rows);
        return $all_rows;
    } else {
        return null; // 找不到這個 user
    }
}

function displaySearchForm($formClass = '', $inputClass = '', $buttonClass = 'btn-outline-primary', $placeholder = '搜尋商品名稱或描述', $buttonText = '搜尋')
{
    $keyword = isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '';
    ?>
    <div class="row mb-10">
        <div class="col-md-12 mx-auto">
            <form method="GET" class="d-flex <?php echo $formClass; ?>">
                <input class="form-control me-2 <?php echo $inputClass; ?>" type="search" name="keyword"
                    placeholder="<?php echo $placeholder; ?>" aria-label="Search" value="<?php echo $keyword; ?>">
                <button class="btn <?php echo $buttonClass; ?>" type="submit"><?php echo $buttonText; ?></button>
            </form>
        </div>
    </div>
    <?php
}


function getProductSearchResults($conn, $table = 'products', $searchFields = ['name', 'description'], $keyword = '', $page = 1, $limit = 4, &$totalProducts = 0)
{
    // 計算開始的商品數量
    $offset = ($page - 1) * $limit;
    // 移除調試代碼
    // echo '屌你老母' . $offset;

    // 計算總產品數
    if (empty(trim($keyword))) {
        $countSql = "SELECT COUNT(*) as total FROM $table";
        $countStmt = $conn->prepare($countSql);
        $countStmt->execute();
    } else {
        $searchClauses = [];
        $countParams = [];
        $types = '';

        foreach ($searchFields as $field) {
            $searchClauses[] = "$field LIKE ?";
            $countParams[] = "%" . trim($keyword) . "%";
            $types .= 's';
        }

        $countSql = "SELECT COUNT(*) as total FROM $table WHERE " . implode(' OR ', $searchClauses);
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param($types, ...$countParams);
        $countStmt->execute();
    }

    $countResult = $countStmt->get_result();
    $row = $countResult->fetch_assoc();
    $totalProducts = $row['total'];
    $countStmt->close();

    // 其餘原有代碼不變
    if (empty(trim($keyword))) {
        $sql = "SELECT * FROM $table LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    $searchClauses = [];
    $params = [];
    $types = '';

    foreach ($searchFields as $field) {
        $searchClauses[] = "$field LIKE ?";
        $params[] = "%" . trim($keyword) . "%";
        $types .= 's';
    }

    $sql = "SELECT * FROM $table WHERE " . implode(' OR ', $searchClauses) . " LIMIT ?, ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $params[] = $offset;
        $params[] = $limit;
        $stmt->bind_param($types . 'ii', ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    return false;
}



function displayProductsList($result, $noResultsMessage = '暫無商品', $keyword = '', $limit = 3, $page = 1, $totalProducts = 0)
{
    ?>
    <style>
        /* 文青柔和色調 */
        .product-card {
            background-color: #fefaf3;
            /* 淡奶油色 */
            border: 1px solid #e2d9c3;
            /* 淡棕色邊框 */
            border-radius: 1rem;
            box-shadow: 0 3px 10px rgba(183, 166, 138, 0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            font-family: 'Noto Serif TC', serif;
            color: #5a5236;
            /* 深棕色字 */
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(183, 166, 138, 0.4);
        }

        .product-card .ratio {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            background-color: #fcf9f1;
            /* 淺米白 */
        }

        .product-card img {
            padding: 1.5rem;
            object-fit: contain;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: #6e5a31;
            /* 溫暖深棕 */
            margin-bottom: 0.4rem;
        }

        .price-text {
            color: #8b7d5e;
            /* 柔和棕 */
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .btn-custom-outline {
            font-family: 'Noto Serif TC', serif;
            font-size: 0.9rem;
            background-color: #f7f4ea;
            color: #7c6f4f;
            border: 1.5px solid #d6cba2;
            border-radius: 2rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-custom-outline:hover {
            background-color: #a79d7e;
            color: #fefaf3;
            border-color: #958c6b;
        }

        .btn-custom-solid {
            font-family: 'Noto Serif TC', serif;
            font-size: 0.9rem;
            background-color: #958c6b;
            color: #fefaf3;
            border: none;
            border-radius: 2rem;
            transition: background-color 0.3s ease;
        }

        .btn-custom-solid:hover {
            background-color: #7e7658;
        }

        .product-desc {
            font-size: 0.85rem;
            color: #7f7b68;
            min-height: 3em;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
    </style>

    <div class="row g-4 justify-content-center">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                $image_url = $product['image_url'];
                if (strpos($image_url, '../') === 0) {
                    $image_url = substr($image_url, 1);
                }
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100 shadow-sm overflow-hidden">
                        <div class="ratio ratio-4x3 rounded-top">
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                                class="img-fluid" />
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>

                            <?php if (!empty($product['description'])): ?>
                                <p class="product-desc"><?= htmlspecialchars(mb_strimwidth($product['description'], 0, 60, '...')) ?>
                                </p>
                            <?php endif; ?>

                            <p class="price-text">$<?= number_format($product['price']) ?></p>
                            <div class="mt-auto d-grid gap-2">
                                <a href="product.php?product_id=<?= $product['id'] ?>" class="btn btn-custom-outline">
                                    查看詳情
                                </a>
                                <form method="POST" action="action/cart.php" class="m-0">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-custom-solid">加入購物車</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

            // 搜尋提示
            if (isset($_GET['keyword']) && !empty(trim($keyword))) {
                echo '<div class="col-12 text-center mt-4">';
                echo '<p>找到 ' . $totalProducts . ' 個符合 "' . htmlspecialchars($keyword) . '" 的商品，當前顯示第 ' . $page . ' 頁</p>';
                echo '</div>';
            }

            // 分頁
            $totalPages = ceil($totalProducts / $limit);
            if ($totalPages > 1): ?>
                <div class="col-12 mt-5">
                    <div class="d-flex flex-column flex-md-row flex-wrap justify-content-center align-items-center gap-4 p-4 rounded-4 shadow-sm border"
                        style="background-color: #f7f3ef; border-color: #e2dcd5;">

                        <!-- 分頁按鈕 -->
                        <ul class="pagination mb-0 gap-2 justify-content-center flex-wrap">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link rounded-pill px-3 border-0" style="background-color: #e7e2da; color: #4a4a4a;"
                                    href="?page=1<?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">第一頁</a>
                            </li>

                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link rounded-pill px-3 border-0" style="background-color: #e7e2da; color: #4a4a4a;"
                                    href="?page=<?= max(1, $page - 1) ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">上一頁</a>
                            </li>

                            <?php
                            $maxShow = 5;
                            $start = max(1, $page - floor($maxShow / 2));
                            $end = min($totalPages, $start + $maxShow - 1);
                            if ($end - $start < $maxShow - 1)
                                $start = max(1, $end - $maxShow + 1);

                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link rounded-pill px-3 border-0 <?= ($i == $page) ? 'text-white' : '' ?>"
                                        style="<?= ($i == $page) ? 'background-color: #6c8b74;' : 'background-color: #e7e2da; color: #4a4a4a;' ?>"
                                        href="?page=<?= $i ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link rounded-pill px-3 border-0" style="background-color: #e7e2da; color: #4a4a4a;"
                                    href="?page=<?= min($totalPages, $page + 1) ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">下一頁</a>
                            </li>
                        </ul>

                        <!-- 跳轉頁碼 -->
                        <form class="d-flex align-items-center gap-2" onsubmit="return jumpToPage(event)">
                            <input type="number" id="gotoPage" min="1" max="<?= $totalPages ?>"
                                class="form-control form-control-sm rounded-pill border-0 shadow-sm" placeholder="頁碼"
                                style="width: 80px; background-color: #fff8f0;">
                            <button class="btn btn-sm rounded-pill shadow-sm" style="background-color: #6c8b74; color: #fff;"
                                type="submit">前往</button>
                            <?php
                            foreach ($_GET as $key => $value) {
                                if ($key !== 'page' && $key !== 'gotoPage') {
                                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                                }
                            }
                            ?>
                        </form>
                    </div>
                </div>

                <script>
                    function jumpToPage(e) {
                        e.preventDefault();
                        const input = document.getElementById('gotoPage');
                        const page = parseInt(input.value);
                        if (!isNaN(page) && page >= 1 && page <= <?= $totalPages ?>) {
                            const urlParams = new URLSearchParams(window.location.search);
                            urlParams.set('page', page);
                            window.location.href = '?' + urlParams.toString();
                        } else {
                            alert("請輸入 1 到 <?= $totalPages ?> 的頁碼");
                        }
                    }
                </script>
                <?php
            endif;
        } else {
            echo '<div class="col-12 text-center mt-4">';
            if (isset($_GET['keyword']) && !empty(trim($keyword))) {
                echo '<p>沒有找到符合 "' . htmlspecialchars($keyword) . '" 的商品</p>';
            } else {
                echo '<p>' . $noResultsMessage . '</p>';
            }
            echo '</div>';
        }
        ?>
    </div>
    <?php
}






function getaboutme()
{
    global $conn;
    $sql = "SELECT ut.*, u.name, u.email
            FROM user_texts ut 
            JOIN user u ON ut.userId = u.id";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL prepare failed: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $user_text = [];
    while ($row = $result->fetch_assoc()) {
        $user_text[] = $row;
    }
    return $user_text;
}
