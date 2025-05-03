<?php
session_start();
require_once 'database.php'; // 確保這裡有連線到你的資料庫

if (!isset($_SESSION['user_id'])) 
{
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $action = $_POST['action'] ?? '';
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($action === 'add' && $product_id > 0) 
    {
        // 查看是否已經有相同商品在購物車中
        $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) 
        {
            // 商品已存在，數量 +1
            $cart_id = $row['id'];
            $new_qty = $row['quantity'] + 1;

            $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $update->bind_param("ii", $new_qty, $cart_id);
            $update->execute();
            $update->close();
        } 
        else 
        {
            // 新增商品到購物車
            $quantity = 1;
            $add_at = date("Y-m-d H:i:s");

            $insert = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity, add_at) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iiis", $user_id, $product_id, $quantity, $add_at);
            $insert->execute();
            $insert->close();
        }

        $stmt->close();
        header("Location: cart.php"); // 導向購物車頁面
        exit();
    } 
    else 
    {
        echo "無效的請求。";
    }
}
?>
