# 📧 Configuração de Envio de Email - Ink Agenda Pro

## 🔧 Instalação

### ✅ ÓTIMA NOTÍCIA - Sem Composer!

A aplicação agora usa a **função `mail()` nativa do PHP**. 

**Não precisa instalar Composer nem PHPMailer!** ✨

Apenas configure o arquivo `config_email.php` com suas credenciais.

---

## ⚙️ Configuração - 3 Passos Simples

### Passo 1: Abrir `config_email.php`

Arquivo localizado em: `c:\xampp\htdocs\app_agenda_2\config_email.php`

### Passo 2: Inserir seu Email e Senha

```php
'smtp_host' => 'smtp.gmail.com',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_user' => 'seu_email@gmail.com',      // COLOQUE SEU EMAIL
'smtp_password' => 'sua_senha_app',        // COLOQUE A SENHA DE APP
'from_email' => 'seu_email@gmail.com',     // MESMO EMAIL
'from_name' => 'Ink Agenda Pro',
'admin_email' => 'seu_email@gmail.com',    // ONDE RECEBER AGENDAMENTOS
'enabled' => true,                         // ATIVAR EMAILS
```

**Exemplo:**
```php
'smtp_user' => 'joao.soares@gmail.com',
'smtp_password' => 'abcd efgh ijkl mnop',  // Senha de App (sem espaços: abcdefghijklmnop)
'from_email' => 'joao.soares@gmail.com',
'admin_email' => 'joao.soares@gmail.com',
```

---

## 📧 Alternativa: Usando Outlook/Hotmail

```php
'smtp_host' => 'smtp-mail.outlook.com',
'smtp_port' => 587,
'smtp_secure' => 'tls',
'smtp_user' => 'seu_email@outlook.com',
'smtp_password' => 'sua_senha',
```

---

## 🚀 Testando a Configuração

1. Abra o navegador e acesse:
   ```
   http://localhost/app_agenda_2/index.php
   ```

2. Preencha um agendamento ou formulário de contato
# Passo 3: Testar a Configuração

1. Abra no navegador:
   ```
   http://localhost/app_agenda_2/test_email.php
   ```

2. Senha: `admin123`

3. Clique em "Testar Email de Agendamento"

4. Verifique se recebeu o email

---

## 📧 Para Gmail com Autenticação de Dois Fatoresolução:**
- Execute `composer install` na pasta do projeto
- Ou coloque PHPMailer manualmente em `vendor/autoload.php`

### ❌ "Falha na autenticação SMTP"

**Causa:** Email ou senha incorretos

**Solução:**
- Verifique a senha de app do Gmail (sem espaços)
- Certifique-se que a autenticação de 2 fatores está ativada

### ❌ "Connection timeout"

**Causa:** Firewall bloqueando conexão

**Solução:**
- Tente com `smtp_port => 465` e `smtp_secure => 'ssl'`
- Ou configure seu firewall/antivírus

### ❌ Emails não chegam mas sem erro

**Causa:** Emails podem estar na pasta Spam

**Solução:**
- Verifique a pasta Spam do Gmail
- # Se usar Gmail com 2FA:

1. Acesse: https://myaccount.google.com/apppasswords
2. Selecione "Mail" e "Windows Computer"
3. Copie a senha gerada (16 caracteres)
4. Cole em `config_email.php` no campo `'smtp_password'`

---

## 🚀 Usar no Localhost (Desenvolvimento)s, entre em contato!

---

**Bom uso! 🎨**
