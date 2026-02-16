<?php
/**
 * Painel Administrativo - Contatos
 * Arquivo: src/admin/admin_contatos.php
 */

session_start();

// Autoload das classes
require_once dirname(dirname(dirname(__FILE__))) . '/autoload.php';

$contato = new Contato();
$todos_contatos = [];
$mensagem = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'marcar_lido') {
        try {
            $id = $_POST['id'];
            if ($contato->marcarComoLido($id)) {
                $mensagem = '<div class="alert alert-success">Contato marcado como lido!</div>';
            }
        } catch (Exception $e) {
            $mensagem = '<div class="alert alert-error">Erro: ' . $e->getMessage() . '</div>';
        }
    } elseif ($acao === 'deletar') {
        try {
            $id = $_POST['id'];
            if ($contato->deletar($id)) {
                $mensagem = '<div class="alert alert-success">Contato deletado com sucesso!</div>';
            }
        } catch (Exception $e) {
            $mensagem = '<div class="alert alert-error">Erro: ' . $e->getMessage() . '</div>';
        }
    }
}

// Obter contatos
try {
    $todos_contatos = $contato->obterTodos();
    $nao_lidos = $contato->obterNaoLidos();
} catch (Exception $e) {
    $mensagem = '<div class="alert alert-error">Erro ao carregar: ' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Contatos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-box { flex: 1; background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff; }
        .stat-box h3 { font-size: 24px; color: #333; margin-bottom: 5px; }
        .stat-box p { color: #666; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #333; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f9f9f9; }
        .btn { padding: 8px 12px; margin: 2px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-marcar { background-color: #17a2b8; color: white; }
        .btn-deletar { background-color: #dc3545; color: white; }
        .btn:hover { opacity: 0.8; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 90%; max-width: 600px; border-radius: 8px; max-height: 80vh; overflow-y: auto; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        .badge { display: inline-block; padding: 5px 10px; background-color: #dc3545; color: white; border-radius: 12px; font-size: 12px; }
        .badge-novo { background-color: #ff6b6b; }
        .lido { opacity: 0.7; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💬 Painel Administrativo - Contatos</h1>
        
        <?php echo $mensagem; ?>
        
        <div class="stats">
            <div class="stat-box">
                <h3><?php echo count($todos_contatos); ?></h3>
                <p>Total de Contatos</p>
            </div>
            <div class="stat-box">
                <h3><?php echo count($nao_lidos); ?></h3>
                <p>Contatos Não Lidos</p>
            </div>
        </div>

        <?php if (!empty($todos_contatos)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Assunto</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todos_contatos as $c): ?>
                        <tr class="<?php echo $c['lido'] == 0 ? '' : 'lido'; ?>">
                            <td><?php echo $c['id']; ?></td>
                            <td><?php echo htmlspecialchars($c['nome']); ?></td>
                            <td><?php echo htmlspecialchars($c['email']); ?></td>
                            <td><?php echo htmlspecialchars($c['telefone'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($c['assunto']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($c['data_envio'])); ?></td>
                            <td>
                                <?php if ($c['lido'] == 0): ?>
                                    <span class="badge badge-novo">Novo</span>
                                <?php else: ?>
                                    <span style="color: #28a745;">✓ Lido</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-marcar" onclick="abrirMensagem(<?php echo $c['id']; ?>, <?php echo htmlspecialchars(json_encode($c)); ?>)">👁️ Ver</button>
                                <form style="display:inline;" method="POST">
                                    <input type="hidden" name="acao" value="deletar">
                                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" class="btn btn-deletar" onclick="return confirm('Tem certeza?')">🗑️ Deletar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum contato encontrado.</p>
        <?php endif; ?>
    </div>

    <!-- Modal para Ver Mensagem -->
    <div id="modalMensagem" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharMensagem()">&times;</span>
            <h2>Mensagem de Contato</h2>
            
            <div style="margin-top: 20px; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                <p><strong>📧 De:</strong> <span id="msg_nome"></span> (<span id="msg_email"></span>)</p>
                <p><strong>📱 Telefone:</strong> <span id="msg_telefone"></span></p>
                <p><strong>⏰ Data:</strong> <span id="msg_data"></span></p>
                <p><strong>📝 Assunto:</strong> <span id="msg_assunto"></span></p>
                
                <div style="margin-top: 20px; padding: 15px; background: white; border-left: 4px solid #007bff; border-radius: 4px;">
                    <p><strong>Mensagem:</strong></p>
                    <p id="msg_texto" style="margin-top: 10px; line-height: 1.6; white-space: pre-wrap;"></p>
                </div>
            </div>

            <div style="margin-top: 20px; text-align: right;">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="acao" value="marcar_lido">
                    <input type="hidden" name="id" id="modal_id">
                    <button type="submit" class="btn" style="background-color: #28a745; color: white;">✓ Marcar como Lido</button>
                </form>
                <button class="btn" style="background-color: #6c757d; color: white;" onclick="fecharMensagem()">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        function abrirMensagem(id, dados) {
            document.getElementById('modal_id').value = id;
            document.getElementById('msg_nome').textContent = dados.nome;
            document.getElementById('msg_email').textContent = dados.email;
            document.getElementById('msg_telefone').textContent = dados.telefone || '-';
            document.getElementById('msg_data').textContent = new Date(dados.data_envio).toLocaleString('pt-BR');
            document.getElementById('msg_assunto').textContent = dados.assunto;
            document.getElementById('msg_texto').textContent = dados.mensagem;
            document.getElementById('modalMensagem').style.display = 'block';
        }

        function fecharMensagem() {
            document.getElementById('modalMensagem').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modalMensagem');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
