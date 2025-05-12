<?php
/**
 * 生成麵包屑導航
 * 
 * @param string $current_page 當前頁面文件名
 * @return string 包含麵包屑HTML的字串
 */
function generate_breadcrumb($current_page) {
    // 定義頁面標題和層級結構
    $pages = [
        'dashboard.php' => ['標題' => '首頁', '父頁面' => null],
        'products.php' => ['標題' => '產品管理', '父頁面' => 'dashboard.php'],
        'changestatus.php' => ['標題' => '調整權限', '父頁面' => 'dashboard.php'],
        'edit_product.php' => ['標題' => '編輯產品', '父頁面' => 'products.php'],
        'statistics.php' => ['標題' => '統計資料', '父頁面' => 'dashboard.php'],
        'chat.php' => ['標題' => '聊天室', '父頁面' => 'dashboard.php'],
        'product_add.php' => ['標題' => '新增產品', '父頁面' => 'dashboard.php'],
        'changeaboutme.php' => ['標題' => '調整關於我', '父頁面' => 'dashboard.php'],

        
        // 可以根據需要添加更多頁面
    ];
    
    // 如果當前頁面不在定義的結構中，返回空字串
    if (!isset($pages[$current_page])) {
        return '';
    }
    
    $breadcrumb = '<nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-light p-2 rounded">';
    
    // 構建麵包屑路徑
    $path = [];
    $page = $current_page;
    
    // 向上追溯層級結構
    while ($page !== null) {
        if (isset($pages[$page])) {
            array_unshift($path, [
                'url' => $page,
                'title' => $pages[$page]['標題']
            ]);
            $page = $pages[$page]['父頁面'];
        } else {
            break;
        }
    }
    
    // 生成HTML
    $count = count($path);
    foreach ($path as $index => $item) {
        if ($index === $count - 1) {
            // 當前頁面（最後一個元素）是活動項
            $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($item['title']) . '</li>';
        } else {
            // 其他頁面是可點擊的連結
            $breadcrumb .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['title']) . '</a></li>';
        }
    }
    
    $breadcrumb .= '</ol></nav>';
    
    return $breadcrumb;
}
?>