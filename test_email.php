<?php
/**
 * Página de teste de configuração de email
 * Acesse: http://localhost/app_agenda_2/test_email.php
 * 
 * ARQUIVO SIMPLIFICADO - SEM COMPOSER!
 */

session_start();

$config = require 'config_email.php';
require_once 'EmailManager.php';

$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_teste = $_POST['teste'] ?? null;
    $email_teste = $_POST['email_teste'] ?? $config['admin_email'];
    
    $emailManager = new EmailManager();
    
    if ($tipo_teste === 'agendamento') {
        $dados = [
            'nome' => 'Cliente Teste',
            'email' => $email_teste,
            'telefone' => '(11) 98765-4321',
            'profissional' => 'Ilidio Soares - Blackwork',
            'data' => date('Y-m-d'),
            'hora' => '14:00',
            'estilo' => 'Blackwork',
            'descricao' => 'Teste de agendamento',
            'data_envio' => date('Y-m-d H:i:s')
        ];
        
        if ($emailManager->enviarConfirmacaoAgendamento($dados)) {
            $resultado = ['sucesso' => true, 'msg' => '✅ Email de agendamento enviado para ' . $email_teste];
        } else {
            $resultado = ['sucesso' => false, 'msg' => '❌ Erro ao enviar. Certifique-se que o email está habilitado no XAMPP/PHP'];
        }
    } elseif ($tipo_teste === 'contato') {
        $dados = [
            'nome' => 'Visitante Teste',
            'email' => $email_teste,
            'assunto' => 'Teste de Contato',
            'mensagem' => 'Esta é uma mensagem de teste para verificar se o sistema está funcionando.',
            'data_envio' => date('Y-m-d H:i:s')
        ];
        
        if ($emailManager->enviarConfirmacaoContato($dados)) {
            $resultado = ['sucesso' => true, 'msg' => '✅ Email de contato enviado para ' . $email_teste];
        } else {
            $resultado = ['sucesso' => false, 'msg' => '❌ Erro ao enviar. Certifique-se que o email está habilitado no XAMPP/PHP'];
        }
    } elseif ($tipo_teste === 'admin_notif') {
        $dados = [
            'nome' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'telefone' => '(11) 98765-4321',
            'profissional' => 'Ilidio Soares - Blackwork',
            'data' => date('Y-m-d'),
            'hora' => '14:00',
            'estilo' => 'Blackwork',
            'descricao' => 'Teste de notificação ao admin',
            'data_envio' => date('Y-m-d H:i:s')
        ];
        
        if ($emailManager->notificarAdminAgendamento($dados)) {
            $resultado = ['sucesso' => true, 'msg' => '✅ Notificação de agendamento enviada para ' . $config['admin_email']];
        } else {
            $resultado = ['sucesso' => false, 'msg' => '❌ Erro ao enviar notificação'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Email</title>
    <link rel="stylesheet" href="css/estile.css">
    <style>
        .test-container { max-width: 800px; margin: 2rem auto; padding: 2rem; }
        .test-header { text-align: center; margin-bottom: 2rem; }
        .test-header h1 { background: linear-gradient(135deg, var(--ink-purple), var(--ink-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .config-box { background: var(--card-bg); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 2rem; }
        .config-item { padding: 0.5rem 0; border-bottom: 1px solid var(--border); }
        .config-item:last-child { border-bottom: none; }
        .config-label { color: var(--ink-purple); font-weight: bold; }
        .config-value { color: var(--text-secondary); word-break: break-all; }
        .test-buttons { display: grid; gap: 1rem; margin-bottom: 2rem; }
        .btn-test { padding: 1rem; background: var(--ink-purple); color: white; border: none; border-radius: 8px; cursor: pointer; width: 100%; }
        .btn-test:hover { transform: translateY(-2px); }
        .result { padding: 1rem; border-radius: 8px; margin-bottom: 2rem; }
        .result.ok { background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); color: var(--success); }
        .result.err { background: rgba(239, 68, 68, 0.1); border: 1px solid var(--error); color: var(--error); }
        .email-input { margin-bottom: 1rem; }
        .email-input input { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px; background: var(--dark-bg); color: var(--text-primary); }
        .info-box { background: rgba(139, 92, 246, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid var(--ink-purple); }
        .status { font-size: 0.9rem; }
        .status.enabled { color: var(--success); }
        .status.disabled { color: var(--error); }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>📧 Teste de Email - Ink Agenda Pro</h1>
            <p>Verifique se sua configuração está funcionando</p>
        </div>

        <div class="info-box">
            <p><strong>ℹ️ Como funciona:</strong> Este teste envia emails reais usando a configuração em <strong>config_email.php</strong></p>
        </div>

        <!-- Configuração Atual -->
        <div class="config-box">
            <h3 style="margin-top: 0;">⚙️ Configuração Atual</h3>
            <div class="config-item">
                <span class="config-label">Status:</span>
                <span class="status <?php echo $config['enabled'] ? 'enabled' : 'disabled'; ?>">
                    <?php echo $config['enabled'] ? '✅ Ativado' : '❌ Desativado'; ?>
                </span>
            </div>
            <div class="config-item">
                <span class="config-label">Método:</span>
                <span class="config-value">📬 mail() - Função nativa do PHP</span>
            </div>
            <div class="config-item">
                <span class="config-label">Email Remetente:</span>
                <span class="config-value"><?php echo $config['from_email']; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Nome Remetente:</span>
                <span class="config-value"><?php echo $config['from_name']; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Email Admin (notificações):</span>
                <span class="config-value"><?php echo $config['admin_email']; ?></span>
            </div>
        </div>

        <!-- Resultado -->
        <?php if ($resultado): ?>
        <div class="result <?php echo $resultado['sucesso'] ? 'ok' : 'err'; ?>">
            <?php echo $resultado['msg']; ?>
        </div>
        <?php endif; ?>

        <!-- Testes -->
        <h3>🧪 Escolha um Teste:</h3>
        
        <div class="email-input">
            <label>Email para receber teste:</label>
            <input type="email" id="email_teste" placeholder="seu_email@gmail.com" value="<?php echo $config['admin_email']; ?>">
        </div>

        <div class="test-buttons">
            <form method="POST">
                <input type="hidden" name="teste" value="agendamento">
                <input type="hidden" name="email_teste" id="email_agend">
                <button type="submit" class="btn-test" onclick="document.getElementById('email_agend').value = document.getElementById('email_teste').value;">
                    📅 Testar Email de Agendamento (Cliente)
                </button>
            </form>
            
            <form method="POST">
                <input type="hidden" name="teste" value="contato">
                <input type="hidden" name="email_teste" id="email_cont">
                <button type="submit" class="btn-test" onclick="document.getElementById('email_cont').value = document.getElementById('email_teste').value;">
                    💬 Testar Email de Contato (Cliente)
                </button>
            </form>

            <form method="POST">
                <input type="hidden" name="teste" value="admin_notif">
                <input type="hidden" name="email_teste">
                <button type="submit" class="btn-test">
                    🔔 Testar Notificação ao Admin
                </button>
            </form>
        </div>

        <div class="config-box">
            <h3 style="margin-top: 0;">✅ Checklist de Configuração:</h3>
            <ul style="list-style: none; padding: 0;">
                <li>✅ Sem dependências externas (usa mail() do PHP)</li>
                <li><?php echo $config['enabled'] ? '✅' : '⚠️'; ?> Emails ativados em config_email.php</li>
                <li><?php echo $config['from_email'] !== 'seu_email@gmail.com' ? '✅' : '❌'; ?> Email remetente configurado</li>
                <li><?php echo $config['admin_email'] !== 'seu_email@gmail.com' ? '✅' : '⚠️'; ?> Email admin configurado</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="admin.php" style="color: var(--ink-purple); text-decoration: none;">← Voltar ao Painel Admin</a>
        </div>
    </div>
</body>
</html>
