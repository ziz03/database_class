<?php
session_start();
require_once 'action/database.php';
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>所有產品</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/blackLOGO.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <!-- 字體 -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@300;400;500;600;700&family=Crimson+Text:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-cream: #faf8f3;
            --secondary-beige: #f4f1eb;
            --warm-white: #fffef9;
            --dark-brown: #3c3530;
            --medium-brown: #6b5b54;
            --light-brown: #a8968c;
            --sage-green: #9caa8a;
            --dusty-rose: #d4a574;
            --soft-shadow: 0 4px 20px rgba(60, 53, 48, 0.08);
            --hover-shadow: 0 8px 30px rgba(60, 53, 48, 0.15);
            --text-primary: #3c3530;
            --text-secondary: #6b5b54;
            --text-muted: #a8968c;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-cream) 0%, var(--warm-white) 100%);
            font-family: 'Noto Serif TC', serif;
            color: var(--text-primary);
            line-height: 1.7;
            min-height: 100vh;
        }

        .wrapper {
            position: relative;
        }

        /* 背景裝飾元素 */
        .wrapper::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(156, 170, 138, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(212, 165, 116, 0.03) 0%, transparent 50%);
            z-index: -1;
            pointer-events: none;
        }

        /* 主標題區域 */
        .hero-section {
            background: var(--warm-white);
            border-bottom: 1px solid rgba(156, 170, 138, 0.2);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="paper" width="100" height="100" patternUnits="userSpaceOnUse"><rect width="100" height="100" fill="%23faf8f3"/><path d="M0 0h1v100H0zm10 0h1v100h-1zm10 0h1v100h-1zm10 0h1v100h-1zm10 0h1v100h-1zm10 0h1v100h-1zm10 0h1v100h-1zm10 0h1v100h-1zm10 0h1v100h-1zm10 0h1v100h-1z" fill="rgba(156,170,138,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23paper)"/></svg>');
            opacity: 0.5;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 4rem 0 3rem;
        }

        .main-title {
            font-family: 'Crimson Text', serif;
            font-size: 3.5rem;
            font-weight: 600;
            color: var(--dark-brown);
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
            letter-spacing: 0.02em;
        }

        .main-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--sage-green), transparent);
        }

        .subtitle {
            text-align: center;
            color: var(--text-secondary);
            font-size: 1.1rem;
            font-weight: 300;
            margin-bottom: 2rem;
            letter-spacing: 0.05em;
        }

        /* 返回按鈕 */
        .back-button {
            background: var(--warm-white);
            border: 2px solid var(--sage-green);
            color: var(--medium-brown);
            padding: 0.75rem 2rem;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: var(--soft-shadow);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
        }

        .back-button:hover {
            background: var(--sage-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        /* 產品區域 */
        .products-section {
            padding: 4rem 0 5rem;
            background: transparent;
        }

        .section-divider {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--light-brown), transparent);
            margin: 3rem auto;
            opacity: 0.3;
        }

        /* 產品容器樣式化 */
        .products-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* 產品網格優化 */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-top: 3rem;
        }

        /* 如果需要覆蓋 Bootstrap 的網格 */
        .row.g-4 {
            --bs-gutter-x: 2.5rem;
            --bs-gutter-y: 2.5rem;
        }

        /* 空狀態設計 */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: var(--warm-white);
            border-radius: 20px;
            box-shadow: var(--soft-shadow);
            margin: 3rem auto;
            max-width: 500px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--light-brown);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: var(--medium-brown);
            font-family: 'Crimson Text', serif;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.1rem;
            line-height: 1.6;
        }

        /* 響應式設計 */
        @media (max-width: 768px) {
            .main-title {
                font-size: 2.5rem;
            }
            
            .hero-content {
                padding: 3rem 0 2rem;
            }
            
            .products-container {
                padding: 0 1rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 2rem;
            }
        }

        @media (max-width: 576px) {
            .main-title {
                font-size: 2rem;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .back-button {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        /* 頁面載入動畫 */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .fade-in-delay {
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        /* 文字選擇樣式 */
        ::selection {
            background: rgba(156, 170, 138, 0.3);
            color: var(--dark-brown);
        }

        /* 滾動條樣式 */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--secondary-beige);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--light-brown);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--medium-brown);
        }

        /* 裝飾性引號 */
        .decorative-quote {
            position: absolute;
            font-family: 'Crimson Text', serif;
            font-size: 8rem;
            color: rgba(156, 170, 138, 0.1);
            z-index: 1;
            pointer-events: none;
        }

        .quote-left {
            top: 20px;
            left: 20px;
        }

        .quote-right {
            bottom: 20px;
            right: 20px;
            transform: rotate(180deg);
        }
    </style>
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <?php include 'compoents/nav.php'; ?>
        
        <!-- 英雄區域 -->
        <section class="hero-section">
            <div class="decorative-quote quote-left">"</div>
            <div class="decorative-quote quote-right">"</div>
            
            <div class="hero-content">
                <div class="container">
                    <h1 class="main-title fade-in">
                        <?php
                        if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                            echo '搜尋結果';
                        } else {
                            echo '精選書籍';
                        }
                        ?>
                    </h1>
                    
                    <?php if (isset($_GET['keyword']) && !empty($_GET['keyword'])): ?>
                        <p class="subtitle fade-in">
                            關於「<?php echo htmlspecialchars($_GET['keyword']); ?>」的搜尋結果
                        </p>
                    <?php else: ?>
                        <p class="subtitle fade-in">
                            探索知識的無限可能，發現閱讀的美好時光
                        </p>
                    <?php endif; ?>

                    <?php if (isset($_GET['keyword'])): ?>
                        <div class="text-center fade-in-delay">
                            <a href="Allproduct.php" class="back-button">
                                <i class="bi bi-arrow-left"></i>
                                回到所有產品
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- 分隔線 -->
        <div class="section-divider"></div>

        <!-- 產品區域 -->
        <section class="products-section">
            <div class="products-container">
                <div class="row g-4 justify-content-center fade-in-delay">
                    <?php
                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    // 檢查是否有搜尋關鍵字
                    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
                    // 使用搜尋函數獲取結果
                    $totalProducts = 0; // 新增變數保存總產品數
                    $result = getProductSearchResults($conn, 'products', ['name', 'description'], $keyword, $page, 3, $totalProducts);
                    // 使用現有函數顯示產品列表
                    displayProductsList($result, '暫無商品', $keyword, 3, $page, $totalProducts);
                    ?>
                </div>
            </div>
        </section>

        <?php include 'compoents/footer.php'; ?>
    </div>

    <script>
        // 頁面載入動畫
        document.addEventListener('DOMContentLoaded', function() {
            // 為產品卡片添加交錯動畫
            const productCards = document.querySelectorAll('.row.g-4 > *');
            productCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease-out';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });

            // 平滑滾動
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });

        // 滾動時的視差效果
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const quotes = document.querySelectorAll('.decorative-quote');
            
            quotes.forEach(quote => {
                const speed = 0.5;
                quote.style.transform += ` translateY(${scrolled * speed}px)`;
            });
        });
    </script>
</body>

</html>