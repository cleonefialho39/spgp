document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const toggle = document.createElement('div');
    toggle.className = 'sidebar-toggle';
    sidebar.appendChild(toggle);

    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('collapsed');
        document.querySelector('.mainpage').classList.toggle('expanded');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });

    // Aplicar estado inicial
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        document.querySelector('.mainpage').classList.add('expanded');
    }
});