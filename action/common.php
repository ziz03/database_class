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
    // 檢查是否有登入
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
        // print_r($all_rows); // 印出全部資料
        return $all_rows;
    } else {
        return null; // 找不到這個 user
    }
}
function get_user_byemail($email)
{
    global $conn;
    check_login();
    $sql = "SELECT  name, role FROM user WHERE email = '$email'";;
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $all_rows = $result->fetch_all(MYSQLI_ASSOC);
        print_r($all_rows); // 印出全部資料
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
    if ($result && $result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
            $image_url = $product['image_url'];
            if (strpos($image_url, '../') === 0) {
                $image_url = substr($image_url, 1);
            }
    ?>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <div class="card h-100 text-center" style="max-width: 300px; margin: 0 auto;">
                    <img src="<?= htmlspecialchars($image_url) ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text text-danger fw-bold">$<?= number_format($product['price']) ?></p>
                        <a href="product.php?product_id=<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm">查看詳情</a>
                        <form method="POST" action="action/cart.php" class="mt-2">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-sm">加入購物車</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php
        }

        // 搜尋提示
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center mt-3">';
            echo '<p>找到 ' . $totalProducts . ' 個符合 "' . htmlspecialchars($keyword) . '" 的商品，當前顯示第 ' . $page . ' 頁</p>';
            echo '</div>';
        }

        // 分頁
        $totalPages = ceil($totalProducts / $limit);
        if ($totalPages > 1): ?>
            <div class="col-12 mt-5">
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 p-3 rounded shadow-sm border bg-light">
                    <!-- 頁碼列 -->
                    <ul class="pagination gap-4 justify-content-center flex-wrap">
                        <!-- 首頁 -->
                        <li class="page-item me-2<?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-pill"
                                href="?page=1<?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">首頁</a>
                        </li>

                        <!-- 上一頁 -->
                        <li class="page-item me-2<?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-pill"
                                href="?page=<?= max(1, $page - 1) ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">上一頁</a>
                        </li>

                        <!-- 動態頁碼 -->
                        <?php
                        $maxShow = 5; // 最多顯示頁碼數
                        $start = max(1, $page - floor($maxShow / 2));
                        $end = min($totalPages, $start + $maxShow - 1);
                        if ($end - $start < $maxShow - 1)
                            $start = max(1, $end - $maxShow + 1);
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link rounded-pill"
                                    href="?page=<?= $i ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- 下一頁 -->
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-pill"
                                href="?page=<?= min($totalPages, $page + 1) ?><?= $keyword ? '&keyword=' . urlencode($keyword) : '' ?>">下一頁</a>
                        </li>
                    </ul>

                    <!-- 跳轉頁碼 -->
                    <form class="d-flex align-items-center gap-2 ms-3" onsubmit="return jumpToPage(event)">
                        <!-- <label class="mb-3">跳至：</label> -->
                        <input type="number" id="gotoPage" min="1" max="<?= $totalPages ?>" class="form-control form-control-sm mb-3"
                            placeholder="頁碼" style="width: 80px;">
                        <button class="btn btn-sm btn-outline-primary rounded-pill mb-3" type="submit">前往</button>
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
<?php endif;
    } else {
        // 無商品
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center"><p>沒有找到符合 "' . htmlspecialchars($keyword) . '" 的商品</p></div>';
        } else {
            echo '<div class="col-12 text-center"><p>' . $noResultsMessage . '</p></div>';
        }
    }
}


function getaboutme()
{
    global $conn;
    $sql = "SELECT ut.*, u.name 
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
