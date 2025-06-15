<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * Normaliza caminhos para garantir que comecem com /spgp/
 */
function normalizarCaminho($path) {
    $path = trim($path);
    
    // Garante que comece com /
    if (strpos($path, '/') !== 0) {
        $path = '/' . $path;
    }
    
    // Garante que tenha /spgp/
    if (strpos($path, '/spgp/') !== 0) {
        // Se já tiver spgp mas sem a barra inicial
        if (strpos($path, 'spgp/') === 1) {
            $path = '/' . $path;
        } else {
            $path = '/spgp' . $path;
        }
    }
    
    // Remove duplicações acidentais
    $path = preg_replace('#(/spgp)+#', '/spgp', $path);
    
    return $path;
}

/**
 * Carrega a estrutura do menu do banco de dados
 */
function carregarEstruturaMenu() {
    try {
        $db = getDbConnection();
        
        // Busca categorias ordenadas
        $categorias = $db->query("
            SELECT * FROM menu_categorias 
            ORDER BY ordem ASC, nome ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        // Busca itens para cada categoria
        $itensPorCategoria = [];
        foreach ($categorias as $categoria) {
            $stmt = $db->prepare("
                SELECT * FROM menu_itens 
                WHERE categoria_id = :categoria_id 
                ORDER BY ordem ASC, nome ASC
            ");
            $stmt->execute([':categoria_id' => $categoria['id']]);
            $itensPorCategoria[$categoria['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return [
            'categorias' => $categorias,
            'itens' => $itensPorCategoria
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao carregar menu: " . $e->getMessage());
        return [
            'categorias' => [],
            'itens' => []
        ];
    }
}

/**
 * Gera o HTML do menu com os caminhos corrigidos
 */
function gerarHtmlMenu($estrutura) {
    $html = '';
    $paginaAtual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    foreach ($estrutura['categorias'] as $categoria) {
        $html .= '<div class="menu-categoria">';
        $html .= '<h5 class="categoria-titulo">' . htmlspecialchars($categoria['nome']) . '</h5>';
        
        if (!empty($estrutura['itens'][$categoria['id']])) {
            $html .= '<ul class="menu-itens">';
            
            foreach ($estrutura['itens'][$categoria['id']] as $item) {
                $pagina = normalizarCaminho($item['pagina']);
                $classeAtivo = ($paginaAtual === $pagina) ? 'active' : '';
                
                $html .= '<li class="menu-item">';
                $html .= '<a href="' . htmlspecialchars($pagina) . '" class="menu-link ' . $classeAtivo . '">';
                $html .= htmlspecialchars($item['nome']);
                $html .= '</a>';
                $html .= '</li>';
            }
            
            $html .= '</ul>';
        } else {
            $html .= '<p class="sem-itens">Nenhum item nesta categoria</p>';
        }
        
        $html .= '</div>';
    }
    
    return $html;
}