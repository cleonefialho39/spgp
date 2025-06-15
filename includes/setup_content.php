<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $env = [];

    // Autenticação
    $authType = $_POST['AUTH_TYPE'] ?? 'local';
    $env['AUTH_TYPE'] = $authType;

    // Banco de Dados
    $env['DB_HOST'] = $_POST['DB_HOST'] ?? '';
    $env['DB_DATABASE'] = $_POST['DB_DATABASE'] ?? '';
    $env['DB_USERNAME'] = $_POST['DB_USERNAME'] ?? '';
    $env['DB_PASSWORD'] = $_POST['DB_PASSWORD'] ?? '';
    $env['DB_CHARSET'] = $_POST['DB_CHARSET'] ?? 'utf8mb4';

    // LDAP
    if ($authType === 'ldap') {
        $env['LDAP_HOST'] = $_POST['LDAP_HOST'] ?? '';
        $env['LDAP_PORT'] = $_POST['LDAP_PORT'] ?? '389';
        $env['LDAP_DOMAIN'] = $_POST['LDAP_DOMAIN'] ?? '';
        $env['LDAP_BASE_DN'] = $_POST['LDAP_BASE_DN'] ?? '';
        $env['LDAP_ADMIN_USER'] = $_POST['LDAP_ADMIN_USER'] ?? '';
        $env['LDAP_ADMIN_PASS'] = $_POST['LDAP_ADMIN_PASS'] ?? '';
    }

    // SMTP
    if (isset($_POST['use_smtp']) && $_POST['use_smtp'] === 'on') {
        $env['SMTP_HOST'] = $_POST['SMTP_HOST'] ?? '';
        $env['SMTP_PORT'] = $_POST['SMTP_PORT'] ?? '587';
        $env['SMTP_USER'] = $_POST['SMTP_USER'] ?? '';
        $env['SMTP_PASS'] = $_POST['SMTP_PASS'] ?? '';
        $env['SMTP_FROM'] = $_POST['SMTP_FROM'] ?? '';
        $env['SMTP_NAME'] = $_POST['SMTP_NAME'] ?? 'SPGP';
    }

    // Gravar .env
    $output = "";
    foreach ($env as $key => $value) {
        $output .= "$key=\"$value\"\n";
    }

    $envPath = __DIR__ . '/../config/.env';
    if (file_put_contents($envPath, $output) !== false) {
        echo "<div class='alert alert-success'>Arquivo <code>.env</code> salvo com sucesso.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao salvar <code>.env</code>.</div>";
    }
}
?>

<h2>Configuração Inicial do SPGP</h2>

<form method="POST">
    <!-- Tipo de Autenticação -->
    <div class="mb-4">
        <label class="form-label">Tipo de Autenticação:</label>
        <select name="AUTH_TYPE" id="auth_type" class="form-select" onchange="toggleSections()">
            <option value="local">Local</option>
            <option value="ldap">LDAP</option>
        </select>
    </div>

    <!-- Banco de Dados -->
    <div class="card p-3 mb-4">
        <h5>Configuração do Banco de Dados</h5>
        <div class="mb-2"><label>DB_HOST: <input type="text" name="DB_HOST" class="form-control" required></label></div>
        <div class="mb-2"><label>DB_DATABASE: <input type="text" name="DB_DATABASE" class="form-control" required></label></div>
        <div class="mb-2"><label>DB_USERNAME: <input type="text" name="DB_USERNAME" class="form-control" required></label></div>
        <div class="mb-2"><label>DB_PASSWORD: <input type="password" name="DB_PASSWORD" class="form-control" required></label></div>
        <div class="mb-2"><label>DB_CHARSET: <input type="text" name="DB_CHARSET" value="utf8mb4" class="form-control" required></label></div>
    </div>

    <!-- LDAP -->
    <div class="card p-3 mb-4 d-none" id="ldap_section">
        <h5>Configuração LDAP</h5>
        <div class="mb-2"><label>LDAP_HOST: <input type="text" name="LDAP_HOST" class="form-control"></label></div>
        <div class="mb-2"><label>LDAP_PORT: <input type="text" name="LDAP_PORT" value="389" class="form-control"></label></div>
        <div class="mb-2"><label>LDAP_DOMAIN: <input type="text" name="LDAP_DOMAIN" class="form-control"></label></div>
        <div class="mb-2"><label>LDAP_BASE_DN: <input type="text" name="LDAP_BASE_DN" class="form-control"></label></div>
        <div class="mb-2"><label>LDAP_ADMIN_USER: <input type="text" name="LDAP_ADMIN_USER" class="form-control"></label></div>
        <div class="mb-2"><label>LDAP_ADMIN_PASS: <input type="password" name="LDAP_ADMIN_PASS" class="form-control"></label></div>
    </div>

    <!-- SMTP -->
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="use_smtp" name="use_smtp" onchange="toggleSections()">
        <label class="form-check-label" for="use_smtp">Usar SMTP</label>
    </div>

    <div class="card p-3 mb-4 d-none" id="smtp_section">
        <h5>Configuração SMTP</h5>
        <div class="mb-2"><label>SMTP_HOST: <input type="text" name="SMTP_HOST" class="form-control"></label></div>
        <div class="mb-2"><label>SMTP_PORT: <input type="text" name="SMTP_PORT" value="587" class="form-control"></label></div>
        <div class="mb-2"><label>SMTP_USER: <input type="text" name="SMTP_USER" class="form-control"></label></div>
        <div class="mb-2"><label>SMTP_PASS: <input type="password" name="SMTP_PASS" class="form-control"></label></div>
        <div class="mb-2"><label>SMTP_FROM: <input type="email" name="SMTP_FROM" class="form-control"></label></div>
        <div class="mb-2"><label>SMTP_NAME: <input type="text" name="SMTP_NAME" value="SPGP" class="form-control"></label></div>
    </div>

    <button type="submit" class="btn btn-primary">Salvar Configuração</button>
</form>

<script>
    function toggleSections() {
        const authType = document.getElementById('auth_type').value;
        const useSmtp = document.getElementById('use_smtp').checked;

        document.getElementById('ldap_section').classList.toggle('d-none', authType !== 'ldap');
        document.getElementById('smtp_section').classList.toggle('d-none', !useSmtp);
    }

    document.addEventListener('DOMContentLoaded', toggleSections);
</script>
