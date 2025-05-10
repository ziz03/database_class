<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$recipient_name = $_POST['recipient_name'];
$address = $_POST['address'];
$phone = $_POST['recipient_phone'];
$total_price = $_POST['total_price'];

$conn->begin_transaction();  
try {
    // 1. 先檢查購物車中所有商品的庫存是否足夠
    $insufficient_stock_items = [];
    
    $check_stock_sql = "SELECT ci.product_id, ci.quantity, p.stock, p.name, p.price 
                        FROM cart_items ci
                        JOIN products p ON ci.product_id = p.id
                        WHERE ci.user_id = ?";
    $stmt = $conn->prepare($check_stock_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    $cart_items = [];
    
    while ($item = $cart_result->fetch_assoc()) {
        $cart_items[] = $item;
        
        // 檢查庫存是否足夠
        if ($item['quantity'] > $item['stock']) {
            $insufficient_stock_items[] = [
                'product_name' => $item['name'],
                'requested' => $item['quantity'],
                'available' => $item['stock']
            ];
        }
    }
    $stmt->close();
    
    // 如果有任何商品庫存不足，則阻止下單
    if (!empty($insufficient_stock_items)) {
        $error_message = "以下商品庫存不足，請調整購買數量：<br>";
        foreach ($insufficient_stock_items as $item) {
            $error_message .= "- {$item['product_name']} - 需求數量：{$item['requested']}，可用庫存：{$item['available']}<br>";
        }
        throw new Exception($error_message);
    }
    
    // 2. 建立訂單
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, created_at, recipient_name, recipient_address, recipient_phone) VALUES (?, ?, 'pending', NOW(), ?, ?, ?)");
    $stmt->bind_param("idsss", $user_id, $total_price, $recipient_name, $address, $phone);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();
    
    // 3. 寫入 order_items 並更新庫存
    $stmt_insert = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    
    foreach ($cart_items as $item) {
        // 再次檢查庫存（雙重保險，使用 FOR UPDATE 鎖定行）
        $check_again_sql = "SELECT stock FROM products WHERE id = ? FOR UPDATE";
        $stmt_check = $conn->prepare($check_again_sql);
        $stmt_check->bind_param("i", $item['product_id']);
        $stmt_check->execute();
        $current_stock_result = $stmt_check->get_result();
        $current_stock = $current_stock_result->fetch_assoc()['stock'];
        $stmt_check->close();
        
        if ($current_stock < $item['quantity']) {
            throw new Exception("商品「{$item['name']}」庫存已變更，目前庫存為 {$current_stock}，請重新調整購買數量。");
        }
        
        // 寫入訂單項目
        $stmt_insert->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt_insert->execute();
        
        // 更新商品庫存
        $stmt_update_stock->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt_update_stock->execute();
    }
    
    $stmt_insert->close();
    $stmt_update_stock->close();
    
    // 4. 清空購物車
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    
    $conn->commit();
    
    echo "
    <html>
        <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
    <body>
    <script>
    Swal.fire({
        icon: 'success',
        title: '訂單已送出',
        showConfirmButton: false,
        timer: 2000
    });
    setTimeout(function() {
        window.location.href = '../index.php';
    }, 2000);
    </script>
    </body>
    </html>
    ";
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    $error_message = $e->getMessage();
    
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'error',
        title: '訂單失敗',
        html: " . json_encode($error_message) . ",
        confirmButtonText: '返回購物車'
    }).then(() => {
        window.location.href = '../cart.php';
    });
    </script>
    </body>
    </html>";
    exit();
}
?>