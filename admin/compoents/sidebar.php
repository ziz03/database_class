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
            <a class="nav-link text-white d-flex align-items-center" href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" title="首頁">
                🏠
                <span class="ms-2 sidebar-text">首頁</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center" href="#submenu1" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false" aria-controls="submenu1">
                📄
                <span class="ms-2 sidebar-text">產品</span>
            </a>

        </li>

        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center" href="#" data-bs-toggle="tooltip" data-bs-placement="right" title="統計">
                📊
                <span class="ms-2 sidebar-text">統計</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a class="nav-link text-white d-flex align-items-center" href="#" data-bs-toggle="tooltip" data-bs-placement="right" title="聊天">
                💬
                <span class="ms-2 sidebar-text">聊天</span>
            </a>
        </li>
    </ul>
    <div class="logout-container mt-auto">
                <a class="nav-link text-white d-flex align-items-center" href="..\index2.php" data-bs-toggle="tooltip" data-bs-placement="right" title="登出">
                    🚪
                    <span class="ms-2 sidebar-text">登出</span>
                </a>
            </div>

</nav>