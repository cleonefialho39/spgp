# Projeto SPGP (Sistema de Parceiros e Gestão de Pagamentos)

Este projeto foi desenvolvido em PHP puro, sem frameworks como Laravel. Ele tem como objetivo cadastrar parceiros, gerar ordens de serviço, controlar pagamentos e permitir autenticação via LDAP ou local, com funcionalidades administrativas completas.

---

## 1. Requisitos do Servidor

Antes de começar, garanta que seu servidor tenha:

* Apache 2.4+
* PHP 8+ (com extensão `ldap` habilitada)
* Composer
* MariaDB

### Instalação de dependências via Composer

```bash
composer require vlucas/phpdotenv
composer require phpmailer/phpmailer
```

---

## 2. Estrutura de Pastas

Baseada no diagrama fornecido:

```
spgp/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   └── Helpers/
├── config/
├── database/migrations/
├── public/
├── resources/views/
├── routes/
├── storage/pdfs/
├── .env
├── composer.json
└── README.md
```

---

## 3. Configuração do Sistema

### .env (exemplo)

```
DB_HOST=localhost
DB_DATABASE=spgp
DB_USERNAME=root
DB_PASSWORD=senha

AUTH_TYPE=ldap # ou local

LDAP_HOST=ldap.exemplo.com
LDAP_PORT=389
LDAP_DOMAIN=EXEMPLO
LDAP_BASE_DN=DC=exemplo,DC=com
LDAP_ADMIN_USER=admin
LDAP_ADMIN_PASS=senha

SMTP_HOST=smtp.exemplo.com
SMTP_PORT=587
SMTP_USER=envia@exemplo.com
SMTP_PASS=senhasmtp
SMTP_FROM=envia@exemplo.com
SMTP_NAME=SPGP
```

### Configurações no Apache

Certifique-se de apontar o DocumentRoot para `public/index.php`.

---

## 4. Publicação do Projeto

### 1. Clone ou envie os arquivos para o servidor

```bash
git clone https://seurepositorio.com/spgp.git /var/www/html/spgp
```

### 2. Configure permissões

```bash
sudo chown -R www-data:www-data /var/www/html/spgp
sudo chmod -R 755 /var/www/html/spgp
```

### 3. Configure o banco de dados

* Crie o banco de dados `spgp`
* Execute os scripts em `database/migrations/`

### 4. Edite o arquivo `.env`

### 5. Acesse via navegador

```
http://SEU_DOMINIO/spgp/public
```

---

## 5. Funcionalidades do Sistema

### Cadastro de Parceiros

* Dados obrigatórios e adicionais
* Cadastro no LDAP
* Envio de e-mail com senha

### Cadastro de Filiais

* Nome, abreviação e localização

### Geração de Ordem de Serviço

* Sequencial: `ANO + ABREV + SEQ`
* PDF gerado e enviado ao parceiro e pagadores

### Pagamento

* Upload de comprovante
* Marcar como pago
* E-mail com comprovante

### Administração

* Níveis de acesso: admin, emissor, pagador, e-mail
* Upload de logo

### Relatórios

* Filtros por filial, período, status
* Exportação PDF/Excel

### Armazenamento

* Histórico completo de OSs, pagamentos, cadastros

---

## Observações Finais

* O sistema está modularizado para facilitar manutenção.
* Todos os dados sensíveis são mantidos no `.env`
* PDF gerado com biblioteca nativa + FPDF (pode ser incluído via Composer ou script manual).

Para dúvidas ou contribuições, entre em contato com o desenvolvedor responsável.

---

**Fim do guia de instalação e publicação.**
