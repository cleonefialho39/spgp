<?php
$path = __DIR__ . '/.env';
$conteudo = "TESTE=ok\n";

if (file_put_contents($path, $conteudo)) {
    echo "Arquivo gravado com sucesso em: $path";
} else {
    echo "❌ Falha ao gravar arquivo em: $path";
}
?>
