// 等待文檔加載完成
document.addEventListener('DOMContentLoaded', function() {
    // 初始化所有 Bootstrap Tooltip
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl, {
            boundary: document.body // 確保 tooltip 不被容器限制
        })
    );

    // 切換 sidebar 收合
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    
    if (toggleBtn && sidebar && content) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("collapsed");
            
            // 當收合時，強制重新初始化 tooltip
            if (sidebar.classList.contains("collapsed")) {
                tooltipList.forEach(tooltip => {
                    tooltip.dispose();
                    new bootstrap.Tooltip(tooltip._element, {
                        placement: 'right',
                        trigger: 'hover',
                        boundary: document.body
                    });
                });
            }
        });
    }
    
    // 在小屏幕下自動收合側邊欄
    function checkWidth() {
        if (window.innerWidth < 768 && sidebar) {
            sidebar.classList.add('collapsed');
        } else if (sidebar && window.innerWidth >= 768) {
            // 可選：在大屏幕時展開側邊欄
            // sidebar.classList.remove('collapsed');
        }
    }
    
    // 初始檢查
    checkWidth();
    
    // 當窗口大小改變時檢查
    window.addEventListener('resize', checkWidth);
});