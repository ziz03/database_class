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
    $sql = "SELECT  name, role FROM user WHERE email = '$email'";
    ;
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
        // 移除計數器和重複的限制邏輯
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
                        <a href="product.php?product_id=<?= $product['id'] ?>"
                            class="btn btn-outline-primary btn-sm">查看詳情</a>
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

        // 顯示搜尋結果資訊
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center mt-3">';
            echo '<p>找到 ' . $totalProducts . ' 個符合 "' . htmlspecialchars($keyword) . '" 的商品，當前顯示第 ' . $page . ' 頁</p>';
            echo '</div>';
        }

        // 顯示分頁 - 使用正確的總產品數
        $totalPages = ceil($totalProducts / $limit);
        if ($totalPages > 1) {
            echo '<div class="col-12 text-center mt-3">';
            // 保留所有現有 GET 參數
            $queryParams = $_GET;
            
            for ($i = 1; $i <= $totalPages; $i++) {
                $queryParams['page'] = $i;
                $queryString = http_build_query($queryParams);
                
                $activeClass = ($i == $page) ? 'btn-primary' : 'btn-outline-primary';
                echo '<a href="?' . $queryString . '" class="btn ' . $activeClass . ' btn-sm mx-1">' . $i . '</a> ';
            }
            echo '</div>';
        }
    } else {
        // 沒有找到商品時的顯示
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center"><p>沒有找到符合 "' . htmlspecialchars($keyword) . '" 的商品</p></div>';
        } else {
            echo '<div class="col-12 text-center"><p>' . $noResultsMessage . '</p></div>';
        }
    }
}