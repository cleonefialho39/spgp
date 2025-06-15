<?php
// Configuração de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define o caminho base do projeto
define('PROJECT_ROOT', '/var/www/html/spgp/');
define('BASE_URL', '/spgp/');

// Carrega as configurações
require_once PROJECT_ROOT . 'config/database.php';
require_once PROJECT_ROOT . 'includes/config_layout/estrutura_menu.php';

// Configurações da página
$pageTitle = "Administração de Menus";
$loadBootstrap = true;
$error = null;

/**
 * Normaliza caminhos para garantir formato consistente
 */
function normalizePath($path) {
    $path = trim($path);
    $path = preg_replace('#/+#', '/', $path);
    if (strpos($path, BASE_URL) !== 0) {
        $path = BASE_URL . ltrim($path, '/');
    }
    return $path;
}

/**
 * Lista recursivamente todas as páginas PHP dentro do diretório admin
 */
function listAdminPages() {
    $adminDir = PROJECT_ROOT . 'admin/';
    $pages = [];

    if (!is_dir($adminDir) || !is_readable($adminDir)) {
        error_log("Erro: Não foi possível acessar o diretório admin - " . $adminDir);
        return $pages;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($adminDir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace(PROJECT_ROOT, '', $file->getPathname());
            $pages[] = normalizePath($relativePath);
        }
    }

    sort($pages);
    return $pages;
}

try {
    $db = getDbConnection();

    // Categorias e itens de menu
    $categorias = $db->query("SELECT * FROM menu_categorias ORDER BY ordem, nome")->fetchAll();
    $itensPorCategoria = [];
    foreach ($categorias as $categoria) {
        $stmt = $db->prepare("SELECT * FROM menu_itens WHERE categoria_id = ? ORDER BY ordem, nome");
        $stmt->execute([$categoria['id']]);
        $itensPorCategoria[$categoria['id']] = $stmt->fetchAll();
    }

    // Listagem automática de páginas
    $paginasDisponiveis = listAdminPages();

} catch (Exception $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

// DEBUG - Mostra páginas encontradas
echo "<pre>Páginas detectadas:\n";
print_r($paginasDisponiveis);
echo "</pre>";

// Inclui o layout principal
include PROJECT_ROOT . 'includes/layout.php';
