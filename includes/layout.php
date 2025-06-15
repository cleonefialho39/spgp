<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'SPGP'; ?></title>
    <link rel="stylesheet" href="/spgp/includes/css/layout.css">
     <link rel="stylesheet" href="/spgp/includes/css/sidebar.css">
    <link rel="stylesheet" href="/spgp/includes/css/footer.css">
    <?php if (isset($loadBootstrap) && $loadBootstrap): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php endif; ?>

    <style>
    /* Reset adicional */
    * {
        box-sizing: border-box;
    }
    
    /* Ajustes finos */
    .mainpage .container-fluid {
        padding: 0;
    }
    
    /* Melhor espaçamento para conteúdo */
    .menu-admin-container h2 {
        margin: 20px 0 15px 0;
        font-size: 1.3rem;
    }
    
    /* Formulários mais compactos */
    .form-group {
        margin-bottom: 15px;
    }
</style>
    
</head>
<body>
    <div class="container-wrapper">
        
<?php include_once 'header.php'; ?>
<button id="sidebarToggle" class="sidebar-toggle-btn">☰ Menu</button>

<!-- Botão para abrir/fechar a sidebar -->
<button id="sidebarToggle" class="sidebar-toggle-btn">☰ Menu</button>

        
        <div class="content-wrapper">
            <?php include_once 'sidebar.php'; ?>
            
            <main class="mainpage">
                <?php if (isset($includeContent)): ?>
                    <?php include_once $includeContent; ?>
                <?php elseif (isset($content)): ?>
                    <?php echo $content; ?>
                <?php else: ?>
                    <!-- Conteúdo padrão quando nenhum específico é definido -->
                    <?php if (basename($_SERVER['PHP_SELF']) === 'menus.php'): ?>
                        <div class="container-fluid py-4 menu-admin-container">
                            <!-- [Todo o conteúdo HTML de administração de menus aqui...] -->
                            <?php include 'menu_admin_content.php'; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
        
        <?php include_once 'footer.php'; ?>
    </div>

    <script src="/spgp/includes/js/footer-toggle.js"></script>
    <?php if (isset($loadBootstrap) && $loadBootstrap): ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/spgp/includes/js/menu_admin.js"></script>
    <?php endif; ?>
    <script src="/spgp/includes/js/sidebar-toggle.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.querySelector(".sidebar");
    toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("active");
    });
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.querySelector(".sidebar");
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("active");
        });
    }
});
</script>

</body>
</html>