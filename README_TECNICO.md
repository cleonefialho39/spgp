# SPGP - Sistema de Parceiros e Gestão de Pagamentos

## 📁 Estrutura
- `public/`: arquivos acessíveis via navegador
- `app/`: regras de negócio, serviços e controladores
- `resources/views/`: layouts e telas
- `database/`: scripts SQL de criação
- `.env`: variáveis de ambiente

## ⚙️ Instalação

### Requisitos
- Apache 2.4+
- PHP 8+ com extensões `mysqli`, `ldap`
- MariaDB
- Composer

### Etapas
1. Execute `install.sh` no terminal Linux
2. Acesse `http://localhost/spgp/public`
3. Faça login (usuário cadastrado diretamente no banco)

## 👨‍💻 Desenvolvedores

### Funcionalidades
- Autenticação por sessão
- Cadastro de parceiros
- Geração de OS com PDF
- Envio de e-mail com comprovante
- Painel administrativo

### Banco de Dados
- `usuarios`, `parceiros`, `filiais`, `ordens_servico`, `pagamentos`

## ✉️ SMTP
Configure o arquivo `.env`:
```
SMTP_HOST=smtp.seudominio.com
SMTP_USER=email@seudominio.com
SMTP_PASS=senha
```

## 🛡 Segurança
- Sessões controladas
- Uploads validados
- Acesso protegido por login

## 🔧 Suporte
Para dúvidas técnicas, contate o administrador do sistema.

---

SPGP © 2025
