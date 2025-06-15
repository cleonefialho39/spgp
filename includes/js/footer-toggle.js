document.addEventListener('DOMContentLoaded', function() {
    const footer = document.getElementById('footer');
    
    if (!footer) return;
    
    footer.addEventListener('click', function() {
        this.classList.toggle('collapsed');
        
        // Salvar preferência do usuário
        const isCollapsed = this.classList.contains('collapsed');
        localStorage.setItem('footerCollapsed', isCollapsed);
    });
    
    // Aplicar estado salvo ao carregar
    const savedState = localStorage.getItem('footerCollapsed');
    if (savedState === 'true') {
        footer.classList.add('collapsed');
    }
});