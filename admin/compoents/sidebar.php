<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- 整體佈局容器 -->
<div class="d-flex wrapper">
    <!-- Sidebar -->
    <nav class="bg-dark text-white" id="sidebar">
        <!-- 收合按鈕 -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fs-4 sidebar-text">選單</span>
            <button id="toggleSidebar" class="btn btn-sm btn-light">
                ☰
            </button>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'dashboard.php') ? 'active bg-primary rounded' : ''; ?>"
                    href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" title="首頁">
                    🏠
                    <span class="ms-2 sidebar-text">首頁</span>
                </a>
            </li>

            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'products.php') ? 'active bg-primary rounded' : ''; ?>"
                    data-bs-toggle="collapse" href="#submenu1" role="button" aria-expanded="false"
                    aria-controls="submenu1">
                    📄
                    <span class="ms-2 sidebar-text">產品</span>
                    <i class="ms-auto bi bi-chevron-down sidebar-text"></i>
                </a>
                <div class="collapse <?php echo ($current_page == 'products.php') ? 'show' : ''; ?>" id="submenu1">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-4 sidebar-text">
                        <li><a href="products.php"
                                class="nav-link text-white <?php echo ($current_page == 'products.php') ? 'fw-bold' : ''; ?>">商品管理列表</a>
                        </li>
                        <li><a href="product_add.php" class="nav-link text-white">新增商品</a></li>

                        <li> <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'orders.php') ? 'active bg-primary rounded' : ''; ?>"
                                href="view_order.php" data-bs-toggle="tooltip" data-bs-placement="right" title="查看訂單">
                                查看訂單
                            </a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'changestatus.php') ? 'active bg-primary rounded' : ''; ?>"
                    href="changestatus.php" data-bs-toggle="tooltip" data-bs-placement="right" title="調整權限">
                    📊
                    <span class="ms-2 sidebar-text">帳號資訊</span>
                </a>
            </li>
            <!-- <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'changeaboutme.php') ? 'active bg-primary rounded' : ''; ?>"
                    href="changeaboutme.php" data-bs-toggle="tooltip" data-bs-placement="right" title="調整關於我">
                    🍟
                    <span class="ms-2 sidebar-text">調整關於我</span>
                </a>
            </li> -->
            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center " href="..\index.php" data-bs-toggle="tooltip"
                    data-bs-placement="right" title="看看前台">
                    😎
                    <span class="ms-2 sidebar-text">看看前台</span>
                </a>
            </li>
        </ul>
        <div class="logout-container mt-auto">
            <a class="nav-link text-white d-flex align-items-center" href="..\action\logout.php" data-bs-toggle="tooltip"
                data-bs-placement="right" title="登出">
                🚪
                <span class="ms-2 sidebar-text">登出</span>
            </a>
        </div>
    </nav>