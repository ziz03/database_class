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

// 開始交易
$conn->begin_transaction();

try {
    // 建立訂單
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, created_at, recipient_name, recipient_address, recipient_phone) VALUES (?, ?, 'pending', NOW(), ?, ?, ?)");
    $stmt->bind_param("dssss", $user_id, $total_price, $recipient_name, $address, $phone);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // 抓購物車內容
    $stmt = $conn->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // 寫入 order_items
    $stmt_insert = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
    while ($row = $result->fetch_assoc()) {
        $stmt_insert->bind_param("iii", $order_id, $row['product_id'], $row['quantity']);
        $stmt_insert->execute();
    }
    $stmt->close();
    $stmt_insert->close();

    // 清空購物車
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
</html> ";
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
        text: " . json_encode($error_message) . ",
        confirmButtonText: '返回首頁'
    }).then(() => {
        window.location.href = '../index.php';
    });
    </script>
    </body>
</html>";
    exit();
}
?>