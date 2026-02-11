<?php
/**
 * Página de teste de configuração de email
 * Acesse: http://localhost/app_agenda_2/test_email.php
 */

session_start();

// Verificar senha admin
$senha_admin = "admin123";

if (!isset($_SESSION['admin_autenticado']) || !$_SESSION['admin_autenticado']) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha'])) {
        if ($_POST['senha'] === $senha_admin) {
            $_SESSION['admin_autenticado'] = true;
        }
    }
    
    if (!isset($_SESSION['admin_autenticado']) || !$_SESSION['admin_autenticado']) {
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Teste de Email - Admin</title>
            <link rel="stylesheet" href="css/estile.css">
            <style>
                .login-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                .login-form {
                    background: var(--card-bg);
                    padding: 3rem;
                    border-radius: 12px;
                    border: 1px solid var(--border);
                    max-width: 400px;
                }
                .login-form h1 {
                    text-align: center;
                    margin-bottom: 2rem;
                    background: linear-gradient(135deg, var(--ink-purple), var(--ink-blue));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }
                .form-group { margin-bottom: 1.5rem; }
                .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
                .form-group input { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px; background: var(--dark-bg); color: var(--text-primary); }
                .btn-login { width: 100%; padding: 0.75rem; background: var(--ink-purple); color: white; border: none; border-radius: 8px; cursor: pointer; }
            </style>
        </head>
        <body>
            <div class="login-container">
                <form method="POST" class="login-form">
                    <h1>🔐 Teste de Email</h1>
                    <div class="form-group">
                        <label>Senha Admin:</label>
                        <input type="password" name="senha" required autofocus>
                    </div>
                    <button type="submit" class="btn-login">Entrar</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Carregar configurações
$config = require 'config_email.php';
require_once 'EmailManager.php';

$resultado = null;
$tipo_teste = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teste'])) {
    $tipo_teste = $_POST['teste'];
    $emailManager = new EmailManager();
    
    if ($tipo_teste === 'agendamento') {
        $dados_teste = [
            'nome' => 'Cliente Teste',
            'email' => $_POST['email_teste'] ?? $config['admin_email'],
            'telefone' => '(11) 98765-4321',
            'profissional' => 'Ilidio Soares - Blackwork',
            'data' => date('Y-m-d'),
            'hora' => '14:00',
            'estilo' => 'Blackwork',
            'descricao' => 'Teste de agendamento',
            'data_envio' => date('Y-m-d H:i:s')
        ];
        
        if ($emailManager->enviarConfirmacaoAgendamento($dados_teste)) {
            $resultado = ['sucesso' => true, 'mensagem' => 'Email de agendamento enviado com sucesso!'];
        } else {
            $resultado = ['sucesso' => false, 'mensagem' => 'Erro ao enviar email. Verifique os logs.'];
        }
    } elseif ($tipo_teste === 'contato') {
        $dados_teste = [
            'nome' => 'Visitante Teste',
            'email' => $_POST['email_teste'] ?? $config['admin_email'],
            'assunto' => 'Teste de Contato',
            'mensagem' => 'Esta é uma mensagem de teste para verificar se o sistema de email está funcionando corretamente.',
            'data_envio' => date('Y-m-d H:i:s')
        ];
        
        if ($emailManager->enviarConfirmacaoContato($dados_teste)) {
            $resultado = ['sucesso' => true, 'mensagem' => 'Email de contato enviado com sucesso!'];
        } else {
            $resultado = ['sucesso' => false, 'mensagem' => 'Erro ao enviar email. Verifique os logs.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Email - Ink Agenda Pro</title>
    <link rel="stylesheet" href="css/estile.css">
    <style>
        .test-container { max-width: 800px; margin: 2rem auto; padding: 2rem; }
        .test-header { text-align: center; margin-bottom: 2rem; }
        .test-header h1 {
            background: linear-gradient(135deg, var(--ink-purple), var(--ink-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .config-box { background: var(--card-bg); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 2rem; }
        .config-item { padding: 0.5rem 0; border-bottom: 1px solid var(--border); }
        .config-item:last-child { border-bottom: none; }
        .config-label { color: var(--ink-purple); font-weight: bold; }
        .config-value { color: var(--text-secondary); word-break: break-all; }
        .test-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; }
        .btn-test { padding: 1rem; background: var(--ink-purple); color: white; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
        .btn-test:hover { transform: translateY(-2px); }
        .result-message { padding: 1rem; border-radius: 8px; margin-bottom: 2rem; }
        .result-success { background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); color: var(--success); }
        .result-error { background: rgba(239, 68, 68, 0.1); border: 1px solid var(--error); color: var(--error); }
        .email-input { margin-bottom: 1rem; }
        .email-input input { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px; background: var(--dark-bg); color: var(--text-primary); }
        .info-box { background: rgba(139, 92, 246, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid var(--ink-purple); }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>📧 Teste de Configuração de Email</h1>
            <p>Verifique se sua configuração de email está funcionando</p>
        </div>

        <div class="info-box">
            <p><strong>ℹ️ Informação:</strong> Esta página teste permite verificar se os emails estão sendo enviados corretamente.</p>
        </div>

        <!-- Configuração Atual -->
        <div class="config-box">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">⚙️ Configuração Atual</h3>
            <div class="config-item">
                <span class="config-label">Status:</span>
                <span class="config-value"><?php echo $config['enabled'] ? '✅ Ativado' : '❌ Desativado'; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">SMTP Host:</span>
                <span class="config-value"><?php echo $config['smtp_host']; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">SMTP Port:</span>
                <span class="config-value"><?php echo $config['smtp_port']; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">SMTP Secure:</span>
                <span class="config-value"><?php echo $config['smtp_secure']; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Usuário SMTP:</span>
                <span class="config-value"><?php echo $config['smtp_user']; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Email Remetente:</span>
                <span class="config-value"><?php echo $config['from_email']; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Email Admin:</span>
                <span class="config-value"><?php echo $config['admin_email']; ?></span>
            </div>
        </div>

        <!-- Resultado de Teste -->
        <?php if ($resultado): ?>
        <div class="result-message <?php echo $resultado['sucesso'] ? 'result-success' : 'result-error'; ?>">
            <?php echo $resultado['sucesso'] ? '✅' : '❌'; ?> 
            <?php echo $resultado['mensagem']; ?>
        </div>
        <?php endif; ?>

        <!-- Testes -->
        <h3>🧪 Escolha um Teste:</h3>
        
        <div class="email-input">
            <label>Email para receber teste (opcional):</label>
            <input type="email" id="email_teste" placeholder="seu_email@gmail.com" value="<?php echo $config['admin_email']; ?>">
        </div>

        <div class="test-buttons">
            <form method="POST" style="width: 100%;">
                <input type="hidden" name="teste" value="agendamento">
                <input type="hidden" name="email_teste" id="email_teste_agend">
                <button type="submit" class="btn-test" onclick="document.getElementById('email_teste_agend').value = document.getElementById('email_teste').value;">
                    📅 Testar Email de Agendamento
                </button>
            </form>
            
            <form method="POST" style="width: 100%;">
                <input type="hidden" name="teste" value="contato">
                <input type="hidden" name="email_teste" id="email_teste_cont">
                <button type="submit" class="btn-test" onclick="document.getElementById('email_teste_cont').value = document.getElementById('email_teste').value;">
                    💬 Testar Email de Contato
                </button>
            </form>
        </div>

        <div class="config-box">
            <h3 style="margin-top: 0;">📋 Checklist de Configuração:</h3>
            <ul style="list-style: none; padding: 0;">
                <li>✅ PHPMailer instalado via Composer</li>
                <li><?php echo file_exists(__DIR__ . '/vendor/autoload.php') ? '✅' : '❌'; ?> Arquivo vendor/autoload.php existe</li>
                <li><?php echo $config['enabled'] ? '✅' : '⚠️'; ?> Emails ativados em config_email.php</li>
                <li><?php echo $config['smtp_user'] !== 'seu_email@gmail.com' ? '✅' : '❌'; ?> Email SMTP configurado</li>
                <li><?php echo $config['smtp_password'] !== 'sua_senha_app' ? '✅' : '❌'; ?> Senha SMTP configurada</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="admin.php" style="color: var(--ink-purple); text-decoration: none;">← Voltar ao Painel Admin</a>
        </div>
    </div>
</body>
</html>
