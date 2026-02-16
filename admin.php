<?php
// Página Admin para visualizar agendamentos
session_start();

// Incluir configuração de segurança admin
require_once 'config_admin.php';
require_once 'autoload.php';
require_once 'logs_auditoria.php';
require_once 'logs_auditoria.php';

// Verificar logout
if (isset($_GET['logout'])) {
    registrarLog('ADMIN_LOGOUT', 'Logout realizado');
    logoutAdmin();
    header('Location: admin.php');
    exit;
}

// Verificar permissão
if (!estaAutenticado()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha'])) {
        if (verificarSenhaAdmin($_POST['senha'])) {
            $_SESSION['admin_autenticado'] = true;
            registrarLog('ADMIN_LOGIN', 'Login bem-sucedido');
            header('Location: admin.php');
            exit;
        } else {
            registrarLog('ADMIN_LOGIN_FALHA', 'Tentativa de login com senha incorreta');
            $erro_login = "Senha incorreta!";
        }
    }
    
    if (!estaAutenticado()) {
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Painel Admin - Ink Agenda Pro</title>
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
                    width: 100%;
                }
                
                .login-form h1 {
                    text-align: center;
                    margin-bottom: 2rem;
                    background: linear-gradient(135deg, var(--ink-purple), var(--ink-blue));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }
                
                .form-group {
                    margin-bottom: 1.5rem;
                }
                
                .form-group label {
                    display: block;
                    margin-bottom: 0.5rem;
                    font-weight: 500;
                }
                
                .form-group input {
                    width: 100%;
                    padding: 0.75rem;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    background: var(--dark-bg);
                    color: var(--text-primary);
                    font-size: 1rem;
                }
                
                .form-group input:focus {
                    outline: none;
                    border-color: var(--ink-purple);
                    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
                }
                
                .btn-login {
                    width: 100%;
                    padding: 0.75rem;
                    background: var(--ink-purple);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.3s;
                }
                
                .btn-login:hover {
                    background: #7C3AED;
                    transform: translateY(-2px);
                }
                
                .error-login {
                    background: rgba(239, 68, 68, 0.1);
                    border: 1px solid #EF4444;
                    color: #EF4444;
                    padding: 1rem;
                    border-radius: 8px;
                    margin-bottom: 1rem;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <form method="POST" class="login-form">
                    <h1>🔐 Painel Admin</h1>
                    
                    <?php if (isset($erro_login)): ?>
                    <div class="error-login"><?php echo $erro_login; ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="senha">Senha de Acesso</label>
                        <input type="password" id="senha" name="senha" required autofocus>
                    </div>
                    
                    <button type="submit" class="btn-login">Entrar</button>
                    
                    <p style="text-align: center; margin-top: 2rem; color: var(--text-secondary); font-size: 0.9rem;">
                        Senha padrão: admin123 (mude em produção!)
                    </p>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Se chegou aqui, está autenticado
// Carregar agendamentos do banco
$agendamento = new Agendamento();
$contato = new Contato();

try {
    $agendamentos = $agendamento->obterTodos();
    $contatos = $contato->obterTodos();
} catch (Exception $e) {
    $agendamentos = [];
    $contatos = [];
    $erro_db = "Erro ao carregar dados: " . $e->getMessage();
}

// Função para fazer logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Ink Agenda Pro</title>
    <link rel="stylesheet" href="css/estile.css">
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--ink-purple), var(--ink-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-logout {
            padding: 0.75rem 1.5rem;
            background: var(--error);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, var(--ink-purple), var(--ink-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin: 3rem 0 1.5rem;
            color: var(--text-primary);
            border-bottom: 2px solid var(--ink-purple);
            padding-bottom: 0.5rem;
        }
        
        .table-container {
            overflow-x: auto;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: rgba(139, 92, 246, 0.1);
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        th {
            font-weight: 600;
            color: var(--ink-purple);
        }
        
        tr:hover {
            background: rgba(139, 92, 246, 0.05);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-new {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .empty-message {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }
        
        .btn-exportar {
            padding: 0.75rem 1.5rem;
            background: var(--ink-purple);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }
        
        .btn-exportar:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
            <div class="admin-header">
                <h1>📊 Painel de Administração</h1>
                <a href="?logout=1" class="btn-logout">Sair</a>
            </div>

        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($agendamentos); ?></div>
                <div class="stat-label">Agendamentos Realizados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($contatos); ?></div>
                <div class="stat-label">Mensagens de Contato</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($agendamentos) + count($contatos); ?></div>
                <div class="stat-label">Total de Requisições</div>
            </div>
        </div>

        <!-- Agendamentos -->
        <h2 class="section-title">📅 Agendamentos</h2>
        
        <?php if (!empty($agendamentos)): ?>
        <button class="btn-exportar" onclick="exportarCSV('agendamentos')">📥 Exportar como CSV</button>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Profissional</th>
                        <th>Data/Hora</th>
                        <th>Estilo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($agendamentos) as $agendamento): ?>
                    <tr>
                        <td>
                            <small><?php echo date('d/m/Y H:i', strtotime($agendamento['data_envio'])); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($agendamento['nome']); ?></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($agendamento['email']); ?>">
                                <?php echo htmlspecialchars($agendamento['email']); ?>
                            </a>
                        </td>
                        <td>
                            <a href="tel:<?php echo htmlspecialchars($agendamento['telefone']); ?>">
                                <?php echo htmlspecialchars($agendamento['telefone']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($agendamento['profissional']); ?></td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($agendamento['data'])); ?> 
                            às <?php echo htmlspecialchars($agendamento['hora']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($agendamento['estilo']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-message">
            <p>Nenhum agendamento realizado ainda.</p>
        </div>
        <?php endif; ?>

        <!-- Contatos -->
        <h2 class="section-title">💬 Mensagens de Contato</h2>
        
        <?php if (!empty($contatos)): ?>
        <button class="btn-exportar" onclick="exportarCSV('contatos')">📥 Exportar como CSV</button>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Assunto</th>
                        <th>Mensagem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($contatos) as $contato): ?>
                    <tr>
                        <td>
                            <small><?php echo date('d/m/Y H:i', strtotime($contato['data_envio'])); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($contato['nome']); ?></td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($contato['email']); ?>">
                                <?php echo htmlspecialchars($contato['email']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($contato['assunto']); ?></td>
                        <td>
                            <details>
                                <summary style="cursor: pointer; color: var(--ink-purple);">Ver mensagem</summary>
                                <p style="margin-top: 0.5rem;"><?php echo nl2br(htmlspecialchars($contato['mensagem'])); ?></p>
                            </details>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-message">
            <p>Nenhuma mensagem de contato recebida ainda.</p>
        </div>
        <?php endif; ?>

    </div>

    <script>
        function exportarCSV(tipo) {
            const table = document.querySelector('table');
            if (!table) return;

            let csv = [];
            table.querySelectorAll('th').forEach(th => {
                csv.push(th.textContent);
            });
            csv = [csv.join(',')];

            table.querySelectorAll('tbody tr').forEach(tr => {
                let row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push('"' + td.textContent.replace(/"/g, '""') + '"');
                });
                csv.push(row.join(','));
            });

            const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
            const link = document.createElement('a');
            link.setAttribute('href', encodeURI(csvContent));
            link.setAttribute('download', tipo + '_' + new Date().toISOString().split('T')[0] + '.csv');
            link.click();
        }
    </script>
</body>
</html>
