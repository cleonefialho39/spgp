<?php
require_once __DIR__ . '/config_layout/estrutura_menu.php';

$estruturaMenu = carregarEstruturaMenu();
?>

<aside class="sidebar">
    <nav class="menu-navegacao">
        <?php echo gerarHtmlMenu($estruturaMenu); ?>
    </nav>
</aside>