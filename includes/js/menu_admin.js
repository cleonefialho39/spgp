document.addEventListener('DOMContentLoaded', function() {
    // Inicializar modais de edição
    const initModal = (modalId, callback) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                callback(button, modal);
            });
        }
    };

    // Modal Editar Categoria
    initModal('modalEditarCategoria', (button, modal) => {
        modal.querySelector('#editarCategoriaId').value = button.dataset.id;
        modal.querySelector('#editarCategoriaNome').value = button.dataset.nome;
    });

    // Modal Editar Item
    initModal('modalEditarItem', (button, modal) => {
        modal.querySelector('#editarItemId').value = button.dataset.id;
        modal.querySelector('#editarItemNome').value = button.dataset.nome;
        modal.querySelector('#editarItemPagina').value = button.dataset.pagina;
        modal.querySelector('#editarItemCategoria').value = button.dataset.categoria;
    });

    // Habilitar/desabilitar campos baseado em categorias existentes
    const hasCategories = document.querySelector('[name="categoria_id"] option:not([value=""])');
    document.querySelectorAll('[name="categoria_id"], [name="nome_item"], [name="pagina"], [name="criar_item"]')
        .forEach(el => el.disabled = !hasCategories);
});