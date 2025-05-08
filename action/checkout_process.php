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
    header('Location: ../order_success.php');
    exit();
} catch (Exception $e) {
    $conn->rollback();
    echo "訂單失敗：" . $e->getMessage();
}
?>
