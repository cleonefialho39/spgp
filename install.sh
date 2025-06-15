#!/bin/bash
echo "Iniciando instalação do SPGP..."

# Atualizar pacotes e instalar dependências
sudo apt update
sudo apt install -y apache2 php php-mysqli php-ldap php-mbstring php-xml libapache2-mod-php mariadb-server unzip wget

# Clonar ou copiar o projeto
echo "Extraindo arquivos..."
unzip spgp_final_completo_v2.zip -d /var/www/html/spgp

# Permissões
sudo chown -R www-data:www-data /var/www/html/spgp
sudo chmod -R 755 /var/www/html/spgp

# Ativar Apache e reiniciar
sudo systemctl enable apache2
sudo systemctl restart apache2

# Criar banco e tabelas
echo "Configurando banco de dados..."
sudo mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS spgp;
EOF

sudo mysql -u root spgp < /var/www/html/spgp/database/spgp_import.sql

echo "Instalação concluída. Acesse via http://localhost/spgp/public"
