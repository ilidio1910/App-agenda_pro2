# ✅ Checklist de Deploy Seguro — Ink Agenda Pro

## 1. Preparar o Servidor
- [ ] Atualize o SO (`apt upgrade`/`yum update`) e instale o stack (Apache/Nginx + PHP 8.x + MariaDB/MySQL).
- [ ] Crie um usuário não-root (ex.: `inkapp`) e use `sudo` apenas quando necessário.
- [ ] Habilite firewall básico (UFW/Firewalld) permitindo apenas HTTP(S) + SSH.

## 2. Configurar Banco de Dados
- [ ] Crie o banco `app_agenda` e um usuário dedicado:
  ```sql
  CREATE DATABASE app_agenda CHARACTER SET utf8mb4;
  CREATE USER 'ink_user'@'localhost' IDENTIFIED BY 'senha_forte';
  GRANT SELECT, INSERT, UPDATE, DELETE ON app_agenda.* TO 'ink_user'@'localhost';
  FLUSH PRIVILEGES;
  ```
- [ ] Rode migrations ou o script SQL inicial (não deixe o código criar tabelas automaticamente em produção).
- [ ] Configure backup automatizado do banco (mysqldump + cron ou serviço gerenciado).

## 3. Gerenciar Secrets
- [ ] Crie um arquivo `.env` (fora do versionamento) com:
  - `APP_ENV=production`
  - `INK_DB_HOST/USER/PASS/NAME`
  - `ADMIN_PASSWORD_HASH` (gerar via `php -r "echo password_hash('Senha', PASSWORD_DEFAULT);"`)
  - `ALLOW_DB_AUTO_MIGRATE=false`
  - Configurações de e-mail/logs se aplicável
- [ ] Adicione `.env` ao `.gitignore` e defina permissões `600`.

## 4. Empacotamento e Upload
- [ ] Limpe arquivos de teste/logs locais.
- [ ] Gere um pacote (zip/tar) ou use `git clone`/`pull` no servidor.
- [ ] Garanta que `public/` é o único exposto via web; `src/`, `storage/`, `logs/` devem ficar fora da raiz pública.
- [ ] Ajuste permissões: `chown -R inkapp:www-data` e `find storage logs -type d -exec chmod 770 {} \;`.

## 5. Configurar o Servidor Web
- [ ] Apache: criar `VirtualHost` com `DocumentRoot /var/www/inkagenda/public` e bloquear `../src` via `<Directory>`.
- [ ] Nginx: `root /var/www/inkagenda/public;` + regras `location ~ /(src|storage|logs)/ { deny all; }`.
- [ ] Ative HTTPS (Let’s Encrypt) e force redirect HTTP→HTTPS.
- [ ] Defina cabeçalhos de segurança básicos (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`).

## 6. PHP e Logs
- [ ] Em produção, defina `display_errors = Off` e `log_errors = On` no php.ini.
- [ ] Configure rotação (`logrotate`) para `storage/logs/*.log` e proteja o diretório com permissões restritas.
- [ ] Verifique se `storage/agendamentos.json` e `contatos.json` não são acessíveis via web e entram nos backups.

## 7. Testes Pós-Deploy
- [ ] Acesse `https://seu-dominio.com` e valide:
  - formulário de agendamento → registro no banco e JSON backup
  - formulário de contato → registro no banco/JSON
  - login admin e listagem de agendamentos/contatos
- [ ] Teste CSRF/erros simulando formulário inválido; verifique mensagens amigáveis.
- [ ] Revise logs (`storage/logs`) para garantir ausência de erros inesperados.

## 8. Automação e Monitoramento
- [ ] Configure cron para limpar logs antigos/rotacionar backups.
- [ ] Considere um script de deploy (bash/Ansible/GitHub Actions) para padronizar envios futuros.
- [ ] Adicione uptime monitoring e alertas (StatusCake, UptimeRobot, etc.).

Seguindo esta checklist você reduz exposições, mantém o app estável e documenta cada passo do deploy. Ajuste conforme a hospedagem escolhida (shared, VPS, container).