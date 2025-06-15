<div class="container-fluid py-4">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <h2 class="mb-4">Gerenciamento de Menu</h2>
    
    <!-- Formulário de Categoria -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Adicionar Categoria</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="nome_categoria" 
                               placeholder="Nome da categoria" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" name="criar_categoria" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Adicionar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Formulário de Item -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Adicionar Item de Menu</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Categoria</label>
                        <select class="form-select" name="categoria_id" required <?= empty($categorias) ? 'disabled' : '' ?>>
                            <?php if (empty($categorias)): ?>
                                <option value="">Nenhuma categoria disponível</option>
                            <?php else: ?>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Nome do Item</label>
                        <input type="text" class="form-control" name="nome_item" required <?= empty($categorias) ? 'disabled' : '' ?>>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Página</label>
                        <select class="form-select" name="pagina" required <?= empty($categorias) ? 'disabled' : '' ?>>
                            <option value="">Selecione...</option>
                            <?php foreach ($paginas as $pagina): ?>
                                <option value="<?= htmlspecialchars($pagina) ?>"><?= htmlspecialchars($pagina) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" name="criar_item" class="btn btn-primary w-100 mt-4" <?= empty($categorias) ? 'disabled' : '' ?>>
                            <i class="fas fa-plus-circle"></i> Adicionar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Lista de Itens Cadastrados -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Itens Cadastrados</h5>
        </div>
        <div class="card-body">
            <?php if (empty($categorias)): ?>
                <div class="alert alert-info text-center">
                    Nenhuma categoria cadastrada. Adicione uma categoria para começar.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Categoria</th>
                                <th>Itens</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td class="align-middle">
                                        <strong><?= htmlspecialchars($categoria['nome']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if (empty($itensPorCategoria[$categoria['id']])): ?>
                                            <span class="text-muted">Nenhum item nesta categoria</span>
                                        <?php else: ?>
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($itensPorCategoria[$categoria['id']] as $item): ?>
                                                    <li class="py-1 d-flex align-items-center">
                                                        <i class="fas fa-link text-secondary me-2"></i>
                                                        <span class="fw-bold"><?= htmlspecialchars($item['nome']) ?></span>
                                                        <span class="text-muted ms-2"><?= htmlspecialchars($item['pagina']) ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column gap-2">
                                            <!-- Ações para Categoria -->
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="categoria_id" value="<?= $categoria['id'] ?>">
                                                <button type="submit" name="excluir_categoria" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Tem certeza? Todos os itens desta categoria serão removidos!')">
                                                    <i class="fas fa-trash"></i> Excluir Categoria
                                                </button>
                                            </form>
                                            
                                            <!-- Ações para Itens -->
                                            <?php if (!empty($itensPorCategoria[$categoria['id']])): ?>
                                                <?php foreach ($itensPorCategoria[$categoria['id']] as $item): ?>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                            <button type="submit" name="excluir_item" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Tem certeza que deseja excluir este item?')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                        <span><?= htmlspecialchars($item['nome']) ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>