<?php
define('PROJECT_ROOT', '/var/www/html/spgp/');

// Carrega as configurações
require_once PROJECT_ROOT . 'config/database.php';
require_once PROJECT_ROOT . 'includes/layout.php';

// Estabelece conexão com o banco de dados
try {
    $pdo = getDbConnection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Funções para manipulação de categorias
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
        // Primeiro exclui todos os itens da categoria
        $sqlItens = "DELETE FROM menu_itens WHERE categoria_id = :id";
        $stmtItens = $pdo->prepare($sqlItens);
        $stmtItens->execute([':id' => $id]);
        
        // Depois exclui a categoria
        $sqlCat = "DELETE FROM menu_categorias WHERE id = :id";
        $stmtCat = $pdo->prepare($sqlCat);
        $stmtCat->execute([':id' => $id]);
        
        return $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Funções para manipulação de itens de menu
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

// Processamento dos formulários
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

// Obter todas as categorias e itens para exibição
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
        .sortable-handle { cursor: move; }
        .sortable-ghost { opacity: 0.5; background: #c8ebfb; }
    </style>
</head>
<body>
   <div class="container mt-4">
        <h1 class="mb-4">Gerenciamento de Menus</h1>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0">Categorias</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-4">
                            <input type="hidden" name="acao" value="criar_categoria">
                            <div class="row g-2">
                                <div class="col-md-7">
                                    <input type="text" name="nome" class="form-control" placeholder="Nome da categoria" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="ordem" class="form-control" placeholder="Ordem" value="0" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success w-100">Criar</button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="list-group sortable-categories">
                            <?php foreach ($categorias as $categoria): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center" data-id="<?php echo $categoria['id']; ?>">
                                <div>
                                    <span class="sortable-handle me-2">☰</span>
                                    <?php echo htmlspecialchars($categoria['nome']); ?>
                                    <small class="text-muted ms-2">Ordem: <?php echo $categoria['ordem']; ?></small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-category" 
                                            data-id="<?php echo $categoria['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($categoria['nome']); ?>"
                                            data-order="<?php echo $categoria['ordem']; ?>">
                                        Editar
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="acao" value="excluir_categoria">
                                        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Tem certeza? Todos os itens desta categoria serão excluídos.')">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0">Itens de Menu</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-4">
                            <input type="hidden" name="acao" value="criar_item">
                            <div class="row g-2 mb-2">
                                <div class="col-md-5">
                                    <select name="categoria_id" class="form-select" required>
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo htmlspecialchars($cat['nome']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="nome" class="form-control" placeholder="Nome do item" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="ordem" class="form-control" placeholder="Ordem" value="0" min="0" required>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-9">
                                    <input type="text" name="pagina" class="form-control" placeholder="Página (ex: /spgp/admin/pagina.php)" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success w-100">Adicionar</button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="list-group sortable-items">
                            <?php foreach ($itens as $item): ?>
                            <div class="list-group-item" data-id="<?php echo $item['id']; ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($item['categoria_nome']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?php echo htmlspecialchars($item['pagina']); ?></small>
                                    <small class="text-muted">Ordem: <?php echo $item['ordem']; ?></small>
                                </div>
                                <div class="mt-2 btn-group w-100">
                                    <button class="btn btn-sm btn-outline-primary edit-item flex-grow-1"
                                            data-id="<?php echo $item['id']; ?>"
                                            data-categoria="<?php echo $item['categoria_id']; ?>"
                                            data-nome="<?php echo htmlspecialchars($item['nome']); ?>"
                                            data-pagina="<?php echo htmlspecialchars($item['pagina']); ?>"
                                            data-ordem="<?php echo $item['ordem']; ?>">
                                        Editar
                                    </button>
                                    <form method="POST" class="flex-grow-1">
                                        <input type="hidden" name="acao" value="excluir_item">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" 
                                                onclick="return confirm('Tem certeza que deseja excluir este item?')">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>