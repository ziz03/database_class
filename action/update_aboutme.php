<?php
session_start();
require_once '../action/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['content']) && isset($_POST['describe']) && isset($_POST['github_url'])) {
    $id = $_POST['id'];
    $name = $_POST['name']; // 暫時保留，可能後續會用到
    $content = $_POST['content'];
    $describe = $_POST['describe'];
    $github = $_POST['github_url'];
    // 處理圖片上傳
    $img_url = null;
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../image/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = basename($_FILES['img']['name']);
        $target_path = $upload_dir . uniqid() . "_" . $filename;

        if (move_uploaded_file($_FILES['img']['tmp_name'], $target_path)) {
            $img_url = $target_path;
        }
    }

    // 如果沒更新圖片，維持原本的 img_url
    if (!$img_url) {
        $stmt = $conn->prepare("SELECT img_url FROM user_texts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($img_url);
        $stmt->fetch();
        $stmt->close();
    }
    
    // 修改這部分 - 移除 name 欄位，因為資料表中沒有此欄位
    $stmt = $conn->prepare("UPDATE user_texts SET content = ?, `describe` = ?, github_url = ?, img_url = ? WHERE id = ?");
    if ($stmt === false) {
        die("準備語句失敗: " . $conn->error);
    }
    $stmt->bind_param("ssssi", $content, $describe, $github, $img_url, $id);
    $stmt->execute();
    $conn->close();
    header("Location: ../admin/changeaboutme.php");
    exit;
}else {
    $_SESSION['message'] = "請填寫所有欄位。";
    header("Location: ../admin/changeaboutme.php");
    exit;
}