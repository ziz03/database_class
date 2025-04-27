<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="d-flex">

    <!-- Sidebar -->
    <nav class="bg-dark text-white p-3" id="sidebar">
        <!-- 收合按鈕 -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fs-4 sidebar-text">選單</span>
            <button id="toggleSidebar" class="btn btn-sm btn-light">
                ☰
            </button>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'dashboard.php') ? 'active bg-primary rounded' : ''; ?>" href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" title="首頁">
                    🏠
                    <span class="ms-2 sidebar-text">首頁</span>
                </a>
            </li>

            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'products.php') ? 'active bg-primary rounded' : ''; ?>" href="products.php" data-bs-toggle="tooltip" data-bs-target="#submenu1" aria-expanded="false" aria-controls="產品">
                    📄
                    <span class="ms-2 sidebar-text">產品</span>
                </a>
            </li>

            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center <?php echo ($current_page == 'changestatus.php') ? 'active bg-primary rounded' : ''; ?>" href="changestatus.php" data-bs-toggle="tooltip" data-bs-placement="right" title="更換權限">
                    📊
                    <span class="ms-2 sidebar-text">調整權限</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white d-flex align-items-center " href="..\index.php" data-bs-toggle="tooltip" data-bs-placement="right" title="看看前台">
                    😎
                    <span class="ms-2 sidebar-text">看看前台</span>
                </a>
            </li>
        </ul>
        <div class="logout-container mt-auto">
            <a class="nav-link text-white d-flex align-items-center" href="..\index.php" data-bs-toggle="tooltip" data-bs-placement="right" title="登出">
                
                <a class="nav-link" href="..\action\logout.php">🚪 登出</a> 
            </a>
        </div>

    </nav>