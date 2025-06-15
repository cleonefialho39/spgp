<?php
/**********************************************
 * CONFIGURAÇÕES INICIAIS E CONEXÃO COM BANCO *
 **********************************************/
define('PROJECT_ROOT', '/var/www/html/spgp/');
require_once PROJECT_ROOT . 'config/database.php';
require_once PROJECT_ROOT . 'includes/layout.php';

try {
    $pdo = getDbConnection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

/********************************
 * FUNÇÕES DE MANIPULAÇÃO DE DADOS *
 ********************************/

// Seção: Operações com Categorias
function criarCategoria($nome, $ordem, $pdo) {
    $sql = "INSERT INTO menu_categorias (nome, ordem) VALUES (:nome, :ordem)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':nome' => $nome, ':ordem' => $ordem]);
}

function editarCategoria($id, $novoNome, $novaOrdem, $pdo) {
    $sql = "UPDATE menu_categorias SET nome = :nome, ordem = :ordem WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':nome' => $novoNome, ':ordem' => $novaOrdem, ':id' => $id]);
}

function excluirCategoria($id, $pdo) {
    $pdo->beginTransaction();
    try {
        $sqlItens = "DELETE FROM menu_itens WHERE categoria_id = :id";
        $stmtItens = $pdo->prepare($sqlItens);
        $stmtItens->execute([':id' => $id]);
        
        $sqlCat = "DELETE FROM menu_categorias WHERE id = :id";
        $stmtCat = $pdo->prepare($sqlCat);
        $stmtCat->execute([':id' => $id]);
        
        return $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Seção: Operações com Itens de Menu
function criarItemMenu($categoria_id, $nome, $pagina, $ordem, $pdo) {
    $sql = "INSERT INTO menu_itens (categoria_id, nome, pagina, ordem) VALUES (:categoria_id, :nome, :pagina, :ordem)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':categoria_id' => $categoria_id,
        ':nome' => $nome,
        ':pagina' => $pagina,
        ':ordem' => $ordem
    ]);
}

function editarItemMenu($id, $categoria_id, $nome, $pagina, $ordem, $pdo) {
    $sql = "UPDATE menu_itens SET categoria_id = :categoria_id, nome = :nome, pagina = :pagina, ordem = :ordem WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':categoria_id' => $categoria_id,
        ':nome' => $nome,
        ':pagina' => $pagina,
        ':ordem' => $ordem
    ]);
}

function excluirItemMenu($id, $pdo) {
    $sql = "DELETE FROM menu_itens WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $id]);
}

/****************************
 * PROCESSAMENTO DE FORMULÁRIOS *
 ****************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        try {
            switch ($_POST['acao']) {
                case 'criar_categoria':
                    criarCategoria($_POST['nome'], $_POST['ordem'], $pdo);
                    break;
                case 'editar_categoria':
                    editarCategoria($_POST['id'], $_POST['novo_nome'], $_POST['nova_ordem'], $pdo);
                    break;
                case 'excluir_categoria':
                    excluirCategoria($_POST['id'], $pdo);
                    break;
                case 'criar_item':
                    criarItemMenu($_POST['categoria_id'], $_POST['nome'], $_POST['pagina'], $_POST['ordem'], $pdo);
                    break;
                case 'editar_item':
                    editarItemMenu($_POST['id'], $_POST['categoria_id'], $_POST['nome'], $_POST['pagina'], $_POST['ordem'], $pdo);
                    break;
                case 'excluir_item':
                    excluirItemMenu($_POST['id'], $pdo);
                    break;
            }
            header("Location: " . basename($_SERVER['PHP_SELF']));
            exit();
        } catch (PDOException $e) {
            $erro = "Erro ao processar ação: " . $e->getMessage();
        }
    }
}

/*********************
 * CONSULTA DE DADOS *
 *********************/
$categorias = $pdo->query("SELECT * FROM menu_categorias ORDER BY ordem, nome")->fetchAll(PDO::FETCH_ASSOC);
$itens = $pdo->query("
    SELECT mi.*, mc.nome as categoria_nome 
    FROM menu_itens mi 
    JOIN menu_categorias mc ON mi.categoria_id = mc.id 
    ORDER BY mc.ordem, mc.nome, mi.ordem, mi.nome
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Menus - SPGP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos para as seções */
        .section-title {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-left: 4px solid #0d6efd;
            margin: 20px 0 15px 0;
            font-weight: 600;
        }
        
        .section-card {
            border-left: 3px solid #0d6efd;
            margin-bottom: 20px;
        }
        
        .section-categories {
            border-left-color: #198754;
        }
        
        .section-items {
            border-left-color: #6f42c1;
        }
        
        /* Outros estilos */
        .sortable-handle { cursor: move; }
        .sortable-ghost { opacity: 0.7; background: #e9f7fe; }
    </style>
</head>
<body>
    <?php include PROJECT_ROOT . 'includes/layout.php'; ?>
    
    <div class="container py-4">
        <!-- Cabeçalho -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-menu-button-wide"></i> Gerenciamento de Menus
            </h1>
        </div>
        
        <!-- Seção: Mensagens de Erro -->
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars($erro); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Seção: Categorias -->
            <div class="col-lg-6 mb-4">
                <div class="section-title">
                    <i class="bi bi-tags"></i> Gerenciamento de Categorias
                </div>
                
                <div class="card section-card section-categories">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Lista de Categorias</h2>
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
                            <i class="bi bi-plus"></i> Nova Categoria
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <div class="list-group sortable-categories">
                            <?php if (empty($categorias)): ?>
                                <div class="text-muted text-center py-3">Nenhuma categoria cadastrada</div>
                            <?php else: ?>
                                <?php foreach ($categorias as $categoria): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center" data-id="<?php echo $categoria['id']; ?>">
                                    <div class="d-flex align-items-center">
                                        <span class="sortable-handle me-3"><i class="bi bi-grip-vertical"></i></span>
                                        <div>
                                            <div><?php echo htmlspecialchars($categoria['nome']); ?></div>
                                            <small class="text-muted">Ordem: <?php echo $categoria['ordem']; ?></small>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary edit-category" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editCategoryModal"
                                                data-id="<?php echo $categoria['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($categoria['nome']); ?>"
                                                data-order="<?php echo $categoria['ordem']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="acao" value="excluir_categoria">
                                            <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Tem certeza? Todos os itens desta categoria serão excluídos.')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Seção: Itens de Menu -->
            <div class="col-lg-6 mb-4">
                <div class="section-title">
                    <i class="bi bi-list-ul"></i> Gerenciamento de Itens de Menu
                </div>
                
                <div class="card section-card section-items">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Lista de Itens</h2>
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#newItemModal">
                            <i class="bi bi-plus"></i> Novo Item
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <div class="list-group sortable-items">
                            <?php if (empty($itens)): ?>
                                <div class="text-muted text-center py-3">Nenhum item de menu cadastrado</div>
                            <?php else: ?>
                                <?php foreach ($itens as $item): ?>
                                <div class="list-group-item" data-id="<?php echo $item['id']; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="sortable-handle me-3"><i class="bi bi-grip-vertical"></i></span>
                                            <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                        </div>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($item['categoria_nome']); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted"><?php echo htmlspecialchars($item['pagina']); ?></small>
                                        <small class="text-muted">Ordem: <?php echo $item['ordem']; ?></small>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-outline-primary edit-item"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editItemModal"
                                                data-id="<?php echo $item['id']; ?>"
                                                data-categoria="<?php echo $item['categoria_id']; ?>"
                                                data-nome="<?php echo htmlspecialchars($item['nome']); ?>"
                                                data-pagina="<?php echo htmlspecialchars($item['pagina']); ?>"
                                                data-ordem="<?php echo $item['ordem']; ?>">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="acao" value="excluir_item">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Tem certeza que deseja excluir este item?')">
                                                <i class="bi bi-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modais (agrupados em uma seção visual) -->
    <div class="modals-section">
        <!-- Modal: Nova Categoria -->
        <div class="modal fade" id="newCategoryModal" tabindex="-1" aria-hidden="true">
            <!-- Conteúdo do modal mantido igual -->
        </div>

        <!-- Modal: Editar Categoria -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
            <!-- Conteúdo do modal mantido igual -->
        </div>

        <!-- Modal: Novo Item -->
        <div class="modal fade" id="newItemModal" tabindex="-1" aria-hidden="true">
            <!-- Conteúdo do modal mantido igual -->
        </div>

        <!-- Modal: Editar Item -->
        <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
            <!-- Conteúdo do modal mantido igual -->
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Inicialização dos modais de edição
        document.querySelectorAll('.edit-category').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('editCategoryId').value = this.dataset.id;
                document.getElementById('editCategoryName').value = this.dataset.name;
                document.getElementById('editCategoryOrder').value = this.dataset.order;
            });
        });

        document.querySelectorAll('.edit-item').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('editItemId').value = this.dataset.id;
                document.getElementById('editItemCategory').value = this.dataset.categoria;
                document.getElementById('editItemName').value = this.dataset.nome;
                document.getElementById('editItemPage').value = this.dataset.pagina;
                document.getElementById('editItemOrder').value = this.dataset.ordem;
            });
        });

        // Ordenação com SortableJS
        new Sortable(document.querySelector('.sortable-categories'), {
            handle: '.sortable-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                console.log('Nova posição:', evt.newIndex);
                // Implementar AJAX para salvar nova ordem
            }
        });

        new Sortable(document.querySelector('.sortable-items'), {
            handle: '.sortable-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                console.log('Nova posição:', evt.newIndex);
                // Implementar AJAX para salvar nova ordem
            }
        });
    </script>
</body>
</html>