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
/**
 * 显示搜索表单
 * 
 * @param string $formClass 表单的额外CSS类名
 * @param string $inputClass 输入框的额外CSS类名
 * @param string $buttonClass 按钮的额外CSS类名
 * @param string $placeholder 搜索框的占位文本
 * @param string $buttonText 搜索按钮文本
 * @return void 直接输出HTML
 */
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

/**
 * 获取商品搜索结果
 * 
 * @param mysqli $conn 数据库连接
 * @param string $table 表名
 * @param array $searchFields 要搜索的字段数组，例如['name', 'description']
 * @param string $keyword 搜索关键词
 * @return mysqli_result|false 搜索结果集或false
 */
function getProductSearchResults($conn, $table = 'products', $searchFields = ['name', 'description'], $keyword = '')
{
    // 如果没有提供关键词或关键词为空，返回所有产品
    if (!isset($_GET['keyword']) || empty(trim($keyword))) {
        $sql = "SELECT * FROM $table";
        return $conn->query($sql);
    }

    // 构建搜索SQL
    $searchClauses = [];
    $params = [];
    $types = '';

    foreach ($searchFields as $field) {
        $searchClauses[] = "$field LIKE ?";
        $params[] = "%" . trim($keyword) . "%";
        $types .= 's'; // 假设所有字段都是字符串类型
    }

    $sql = "SELECT * FROM $table WHERE " . implode(' OR ', $searchClauses);

    // 准备和执行语句
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // 动态绑定参数
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    return false;
}

/**
 * 显示商品列表
 * 
 * @param mysqli_result $result MySQL查询结果
 * @param string $noResultsMessage 无结果时显示的消息
 * @param string $keyword 搜索关键词（用于显示搜索结果消息）
 * @return void 直接输出HTML
 */
function displayProductsList($result, $noResultsMessage = '暫無商品', $keyword = '')
{
    // 检查是否有产品
    if ($result && $result->num_rows > 0) {
        // 遍历所有产品
        while ($product = $result->fetch_assoc()) {
            // 处理图片URL，移除前缀 "../"
            $image_url = $product['image_url'];
            // 检查并移除 "../" 前缀
            if (strpos($image_url, '../') === 0) {
                $image_url = substr($image_url, 1); // 移除前三个字符 "../"
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

        // 如果是搜索结果且有关键词，显示结果数量
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center mt-3">';
            echo '<p>找到 ' . $result->num_rows . ' 個符合 "' . htmlspecialchars($keyword) . '" 的商品</p>';
            echo '</div>';
        }
    } else {
        // 没有产品时显示提示
        if (isset($_GET['keyword']) && !empty(trim($keyword))) {
            echo '<div class="col-12 text-center"><p>沒有找到符合 "' . htmlspecialchars($keyword) . '" 的商品</p></div>';
        } else {
            echo '<div class="col-12 text-center"><p>' . $noResultsMessage . '</p></div>';
        }
    }
}
