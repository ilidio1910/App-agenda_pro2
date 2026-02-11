# 📧 Configuração de Envio de Email - Ink Agenda Pro

## Pré-requisitos

- PHP 7.4+
- Composer instalado
- Uma conta Gmail ou outro servidor SMTP

---

## 🔧 Instalação do PHPMailer

### Opção 1: Usando Composer (Recomendado)

1. Abra o terminal/prompt na pasta do projeto:
```bash
cd c:\xampp\htdocs\app_agenda_2
```

2. Execute o comando para instalar PHPMailer:
```bash
composer install
```

Isso vai criar a pasta `vendor/` com todas as bibliotecas necessárias.

### Opção 2: Instalação Manual

Se não tiver Composer, [baixe PHPMailer aqui](https://github.com/PHPMailer/PHPMailer) e extraia na pasta do projeto.

---

## ⚙️ Configuração Gmail

### Passo 1: Ativar Autenticação de Dois Fatores
1. Acesse sua conta Google: https://myaccount.google.com
2. Vá para **Segurança** (à esquerda)
3. Ative **Autenticação de Dois Fatores**

### Passo 2: Gerar Senha de App
1. Em **Segurança**, vá para **Senhas de App**
2. Selecione "Mail" e "Windows Computer"
3. Copie a senha gerada (sem espaços)

### Passo 3: Configurar o Arquivo `config_email.php`

Abra o arquivo `config_email.php` e atualize:

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

3. Verifique se recebeu os emails em sua caixa de entrada

---

## 🔍 Resolvendo Problemas

### ❌ "Erro ao enviar email"

**Causa:** PHPMailer não está instalado

**Solução:**
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
- Marque como não-spam para futuras mensagens

---

## 📋 Templates de Email

Os emails são enviados em HTML com design bonito:

✅ **Email de confirmação para o cliente** - Quando agenda ou envia mensagem
✅ **Email de notificação para o admin** - Informando novo agendamento/contato

---

## 🔐 Segurança

- **Nunca comita `config_email.php` com credenciais reais no GitHub!**
- Use variáveis de ambiente em produção
- Mude a senha de admin em `admin.php`

---

## 📞 Suporte

Se tiver dúvidas, entre em contato!

---

**Bom uso! 🎨**
