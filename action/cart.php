<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $product_id = intval($_POST['product_id'] ?? 0);
    if ($product_id > 0) {
        $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $new_qty = $row['quantity'] + 1;
            $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $update->bind_param("ii", $new_qty, $row['id']);
            $update->execute();
            $update->close();
        } else {
            $add_at = date("Y-m-d H:i:s");
            $insert = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity, add_at) VALUES (?, ?, 1, ?)");
            $insert->bind_param("iis", $user_id, $product_id, $add_at);
            $insert->execute();
            $insert->close();
        }
        $stmt->close();
    }
    header("Location: ../cart.php");
    exit();
} elseif ($action === 'remove') {
    $cart_item_id = intval($_POST['cart_item_id'] ?? 0);
    if ($cart_item_id > 0) {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_item_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ../cart.php");
    exit();
} else {
    echo "無效的操作。";
}
?>
