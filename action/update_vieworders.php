<?php
session_start();
require_once 'database.php';

// 只接受 POST 且內容為 JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '只接受 POST 請求']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? null;
$status = $data['status'] ?? null;

$valid_status = ['Processing', 'paid', 'shipped'];

if (!$order_id || !$status || !in_array($status, $valid_status)) {
    echo json_encode(['success' => false, 'message' => '參數錯誤']);
    exit;
}

// 防止未登入或權限檢查（視需求加）
// 例如：if (!isset($_SESSION['user_id'])) { ... }

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '資料庫更新失敗']);
}
