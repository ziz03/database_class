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


function getProductSearchResults($conn, $table = 'products', $searchFields = ['name', 'description'], $keyword = '')
{
    
    if (!isset($_GET['keyword']) || empty(trim($keyword))) {
        $sql = "SELECT * FROM $table";
        return $conn->query($sql);
    }

    
    $searchClauses = [];
    $params = [];
    $types = '';

    foreach ($searchFields as $field) {
        $searchClauses[] = "$field LIKE ?";
        $params[] = "%" . trim($keyword) . "%";
        $types .= 's'; 
    }

    $sql = "SELECT * FROM $table WHERE " . implode(' OR ', $searchClauses);

    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    return false;
}


function displayProductsList($result, $noResultsMessage = '暫無商品', $keyword = '', $limit = 3)
{
    if ($result && $result->num_rows > 0) {
        // 計算總商品數
        $totalProducts = $result->num_rows;
        // 計數器
        $count = 0;
        
        while ($product = $result->fetch_assoc()) {
            // 如果設定了限制且已達到限制數量，則跳出迴圈
            if ($limit > 0 && $count >= $limit) {
                break;
            }
            
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
            // 增加計數器
            $count++;
        }

        // 顯示搜尋結果資訊
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center mt-3">';
            if ($limit > 0 && $totalProducts > $limit) {
                echo '<p>找到 ' . $totalProducts . ' 個符合 "' . htmlspecialchars($keyword) . '" 的商品，顯示前 ' . $limit . ' 個</p>';
            } else {
                echo '<p>找到 ' . $totalProducts . ' 個符合 "' . htmlspecialchars($keyword) . '" 的商品</p>';
            }
            echo '</div>';
        }
        
        // 如果有限制且總數超過限制，顯示「查看更多」按鈕
        if ($limit > 0 && $totalProducts > $limit) {
            echo '<div class="col-12 text-center mt-3">';
            echo '<a href="search.php?keyword=' . urlencode($keyword) . '" class="btn btn-outline-primary">查看全部結果</a>';
            echo '</div>';
        }
    } 
    else {
        // 沒有找到商品時的顯示
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center"><p>沒有找到符合 "' . htmlspecialchars($keyword) . '" 的商品</p></div>';
        } else {
            echo '<div class="col-12 text-center"><p>' . $noResultsMessage . '</p></div>';
        }
    }
}
