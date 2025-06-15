<?php
function getDbConnection() {
    // Carrega variáveis de ambiente
    $envFile = __DIR__ . '/.env';
    if (!file_exists($envFile)) {
        throw new Exception("Arquivo .env não encontrado em: " . __DIR__);
    }

    $env = parse_ini_file($envFile);
    
    // Configurações padrão
    $config = [
        'host' => $env['DB_HOST'] ?? 'localhost',
        'dbname' => $env['DB_DATABASE'] ?? $env['DB_NAME'] ?? 'SPGP',
        'user' => $env['DB_USERNAME'] ?? $env['DB_USER'] ?? 'root',
        'pass' => $env['DB_PASSWORD'] ?? $env['DB_PASS'] ?? '',
        'charset' => $env['DB_CHARSET'] ?? 'utf8mb4'
    ];

    // DEBUG (remova após teste)
    error_log("Tentando conectar com: " . print_r($config, true));

    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro de conexão PDO: " . $e->getMessage());
        throw new Exception("Falha ao conectar ao banco de dados. Verifique as credenciais no arquivo .env");
    }
}