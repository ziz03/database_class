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
        @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@300;400;500;600;700&display=swap');
        
        /* 現代文青書店風格 */
        .product-card {
            background: linear-gradient(135deg, #fefdfb 0%, #faf8f3 100%);
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(139, 125, 94, 0.12);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-family: 'Noto Serif TC', serif;
            color: #4a3f2a;
            overflow: hidden;
            position: relative;
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #d4af37, #c9a961, #b8956b);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 60px rgba(139, 125, 94, 0.25);
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .product-card .ratio {
            border-radius: 1.5rem 1.5rem 0 0;
            background: linear-gradient(45deg, #fcfaf7, #f8f5f0);
            position: relative;
            overflow: hidden;
        }

        .product-card img {
            padding: 2rem;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        .card-body {
            padding: 2rem 1.5rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .card-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: #2c2416;
            margin-bottom: 0.75rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price-text {
            color: #d4af37;
            font-weight: 700;
            font-size: 1.35rem;
            margin-bottom: 1rem;
            text-shadow: 0 1px 2px rgba(212, 175, 55, 0.1);
        }

        .product-desc {
            font-size: 0.9rem;
            color: #6b5d47;
            min-height: 3.5em;
            margin-bottom: 1.25rem;
            line-height: 1.5;
            font-weight: 300;
        }

        .product-meta {
            background: linear-gradient(135deg, #f7f4ea, #f1ede3);
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e8e2d5;
        }

        .product-meta span {
            display: block;
            font-size: 0.85rem;
            color: #7a6f59;
            margin-bottom: 0.25rem;
            font-weight: 400;
        }

        .product-meta span:last-child {
            margin-bottom: 0;
        }

        /* 現代化按鈕設計 */
        .btn-detail {
            font-family: 'Noto Serif TC', serif;
            font-size: 0.95rem;
            font-weight: 500;
            background: linear-gradient(135deg, #ffffff, #f8f6f2);
            color: #4a3f2a;
            border: 2px solid #d4af37;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .btn-detail::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-detail:hover {
            background: linear-gradient(135deg, #d4af37, #c9a961);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
            border-color: #d4af37;
        }

        .btn-detail:hover::before {
            left: 100%;
        }

        .btn-cart {
            font-family: 'Noto Serif TC', serif;
            font-size: 0.95rem;
            font-weight: 600;
            background: linear-gradient(135deg, #2c2416, #4a3f2a);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .btn-cart::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.5s ease;
        }

        .btn-cart:hover {
            background: linear-gradient(135deg, #d4af37, #b8956b);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(44, 36, 22, 0.3);
        }

        .btn-cart:hover::before {
            width: 200%;
            height: 200%;
        }

        .btn-cart:active {
            transform: translateY(0);
        }

        /* 搜尋結果提示 */
        .search-info {
            background: linear-gradient(135deg, #f7f4ea, #ede8d8);
            border: 1px solid #d4af37;
            border-radius: 1rem;
            padding: 1.5rem;
            color: #4a3f2a;
            font-weight: 500;
            text-align: center;
            margin: 2rem 0;
        }

        /* 分頁美化 */
        .pagination-container {
            background: linear-gradient(135deg, #fefdfb, #f7f4ea);
            border: 1px solid #e8e2d5;
            border-radius: 2rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(139, 125, 94, 0.1);
        }

        .pagination .page-link {
            background: linear-gradient(135deg, #ffffff, #f8f6f2);
            color: #4a3f2a;
            border: 1px solid #d4af37;
            border-radius: 50px;
            padding: 0.75rem 1.25rem;
            margin: 0 0.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            background: linear-gradient(135deg, #d4af37, #c9a961);
            color: #ffffff;
            transform: translateY(-2px);
            border-color: #d4af37;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #2c2416, #4a3f2a);
            border-color: #2c2416;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(44, 36, 22, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            background: #f0f0f0;
            color: #999;
            border-color: #ddd;
            cursor: not-allowed;
        }

        /* 跳轉頁碼輸入框 */
        .goto-form input {
            background: linear-gradient(135deg, #ffffff, #faf8f3);
            border: 2px solid #d4af37;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            color: #4a3f2a;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .goto-form input:focus {
            outline: none;
            border-color: #b8956b;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .goto-form button {
            background: linear-gradient(135deg, #d4af37, #c9a961);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .goto-form button:hover {
            background: linear-gradient(135deg, #2c2416, #4a3f2a);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(44, 36, 22, 0.3);
        }

        /* 無結果訊息 */
        .no-results {
            text-align: center;
            padding: 3rem 2rem;
            color: #6b5d47;
            font-size: 1.1rem;
            font-weight: 400;
        }

        /* 響應式優化 */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem 1.25rem;
            }
            
            .card-title {
                font-size: 1.1rem;
            }
            
            .price-text {
                font-size: 1.2rem;
            }
            
            .pagination-container {
                padding: 1.5rem;
            }
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
                    <div class="card product-card h-100">
                        <div class="ratio ratio-4x3">
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                                class="img-fluid" />
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>

                            <?php if (!empty($product['description'])): ?>
                                <p class="product-desc"><?= htmlspecialchars(mb_strimwidth($product['description'], 0, 60, '...')) ?></p>
                            <?php endif; ?>

                            <p class="price-text">NT$ <?= number_format($product['price']) ?></p>
                            
                            <!-- 美化的ISBN與分類資訊 -->
                            <div class="product-meta">
                                <span><strong>ISBN：</strong><?= htmlspecialchars($product['isbn']) ?></span>
                                <span><strong>分類：</strong><?= htmlspecialchars($product['classification']) ?></span>
                            </div>
                            
                            <div class="mt-auto d-grid gap-3">
                                <a href="product.php?product_id=<?= $product['id'] ?>" class="btn btn-detail">
                                    查看詳情
                                </a>
                                <form method="POST" action="action/cart.php" class="m-0">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-cart w-100">加入購物車</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

            // 搜尋提示
            if (isset($_GET['keyword']) && !empty(trim($keyword))) {
                echo '<div class="col-12">';
                echo '<div class="search-info">';
                echo '<p class="mb-0">🔍 找到 <strong>' . $totalProducts . '</strong> 個符合 "<em>' . htmlspecialchars($keyword) . '</em>" 的商品，當前顯示第 <strong>' . $page . '</strong> 頁</p>';
                echo '</div>';
                echo '</div>';
            }

            // 分頁
            $totalPages = ceil($totalProducts / $limit);
            if ($totalPages > 1): ?>
                <div class="col-12 mt-5">
                    <div class="pagination-container">
                        <div class="d-flex flex-column flex-md-row flex-wrap justify-content-center align-items-center gap-4">

                            <!-- 分頁按鈕 -->
                            <ul class="pagination mb-0 gap-1 justify-content-center flex-wrap">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=1<?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">第一頁</a>
                                </li>

                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= max(1, $page - 1) ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">上一頁</a>
                                </li>

                                <?php
                                $maxShow = 5;
                                $start = max(1, $page - floor($maxShow / 2));
                                $end = min($totalPages, $start + $maxShow - 1);
                                if ($end - $start < $maxShow - 1)
                                    $start = max(1, $end - $maxShow + 1);

                                for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= min($totalPages, $page + 1) ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">下一頁</a>
                                </li>

                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $totalPages ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">最後一頁</a>
                                </li>
                            </ul>

                            <!-- 跳轉頁碼 -->
                            <form class="goto-form d-flex align-items-center gap-2" onsubmit="return jumpToPage(event)">
                                <input type="number" id="gotoPage" min="1" max="<?= $totalPages ?>"
                                    class="form-control form-control-sm" placeholder="頁碼" style="width: 80px;">
                                <button class="btn btn-sm" type="submit">前往</button>
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
            echo '<div class="col-12">';
            echo '<div class="no-results">';
            if (isset($_GET['keyword']) && !empty(trim($keyword))) {
                echo '<p>📚 沒有找到符合 "' . htmlspecialchars($keyword) . '" 的商品</p>';
                echo '<p class="mt-2 text-muted">試試其他關鍵字或瀏覽我們的熱門書籍</p>';
            } else {
                echo '<p>' . $noResultsMessage . '</p>';
            }
            echo '</div>';
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
