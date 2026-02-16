<?php
/**
 * Painel Administrativo - Agendamentos
 * Arquivo: src/admin/admin_db.php
 */

session_start();

// Autoload das classes
require_once dirname(dirname(dirname(__FILE__))) . '/autoload.php';

$agendamento = new Agendamento();
$todos_agendamentos = [];
$mensagem = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'atualizar') {
        try {
            $id = $_POST['id'];
            $dados = [
                ':nome' => $_POST['nome'],
                ':telefone' => $_POST['telefone'],
                ':email' => $_POST['email'],
                ':profissional' => $_POST['profissional'],
                ':data' => $_POST['data'],
                ':hora' => $_POST['hora'],
                ':estilo' => $_POST['estilo'],
                ':descricao' => $_POST['descricao'],
                ':status' => $_POST['status']
            ];
            
            if ($agendamento->atualizar($id, $dados)) {
                $mensagem = '<div class="alert alert-success">Agendamento atualizado com sucesso!</div>';
            }
        } catch (Exception $e) {
            $mensagem = '<div class="alert alert-error">Erro: ' . $e->getMessage() . '</div>';
        }
    } elseif ($acao === 'deletar') {
        try {
            $id = $_POST['id'];
            if ($agendamento->deletar($id)) {
                $mensagem = '<div class="alert alert-success">Agendamento deletado com sucesso!</div>';
            }
        } catch (Exception $e) {
            $mensagem = '<div class="alert alert-error">Erro: ' . $e->getMessage() . '</div>';
        }
    }
}

// Obter agendamentos
try {
    $todos_agendamentos = $agendamento->obterTodos();
} catch (Exception $e) {
    $mensagem = '<div class="alert alert-error">Erro ao carregar: ' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Agendamentos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #333; color: white; padding: 15px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        tr:hover { background-color: #f9f9f9; }
        .btn { padding: 8px 12px; margin: 2px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-editar { background-color: #007bff; color: white; }
        .btn-deletar { background-color: #dc3545; color: white; }
        .btn:hover { opacity: 0.8; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: white; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 90%; max-width: 500px; border-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        textarea { min-height: 80px; }
        .status-confirmado { color: #28a745; font-weight: bold; }
        .status-cancelado { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Painel Administrativo - Agendamentos</h1>
        
        <?php echo $mensagem; ?>
        
        <div style="margin-bottom: 20px;">
            <p><strong>Total de agendamentos:</strong> <?php echo count($todos_agendamentos); ?></p>
        </div>

        <?php if (!empty($todos_agendamentos)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Profissional</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Estilo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todos_agendamentos as $ag): ?>
                        <tr>
                            <td><?php echo $ag['id']; ?></td>
                            <td><?php echo htmlspecialchars($ag['nome']); ?></td>
                            <td><?php echo htmlspecialchars($ag['email']); ?></td>
                            <td><?php echo htmlspecialchars($ag['telefone']); ?></td>
                            <td><?php echo htmlspecialchars($ag['profissional']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($ag['data'])); ?></td>
                            <td><?php echo $ag['hora']; ?></td>
                            <td><?php echo htmlspecialchars($ag['estilo']); ?></td>
                            <td>
                                <span class="status-<?php echo $ag['status']; ?>">
                                    <?php echo ucfirst($ag['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-editar" onclick="abrirEdicao(<?php echo $ag['id']; ?>, <?php echo htmlspecialchars(json_encode($ag)); ?>)">✏️ Editar</button>
                                <form style="display:inline;" method="POST">
                                    <input type="hidden" name="acao" value="deletar">
                                    <input type="hidden" name="id" value="<?php echo $ag['id']; ?>">
                                    <button type="submit" class="btn btn-deletar" onclick="return confirm('Tem certeza?')">🗑️ Deletar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum agendamento encontrado.</p>
        <?php endif; ?>
    </div>

    <!-- Modal de Edição -->
    <div id="modalEdicao" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharEdicao()">&times;</span>
            <h2>Editar Agendamento</h2>
            <form method="POST">
                <input type="hidden" name="acao" value="atualizar">
                <input type="hidden" name="id" id="editar_id">

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" id="editar_nome" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="editar_email" required>
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="telefone" id="editar_telefone" required>
                </div>

                <div class="form-group">
                    <label>Profissional</label>
                    <input type="text" name="profissional" id="editar_profissional" required>
                </div>

                <div class="form-group">
                    <label>Data</label>
                    <input type="date" name="data" id="editar_data" required>
                </div>

                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora" id="editar_hora" required>
                </div>

                <div class="form-group">
                    <label>Estilo</label>
                    <input type="text" name="estilo" id="editar_estilo" required>
                </div>

                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" id="editar_descricao"></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="editar_status" required>
                        <option value="confirmado">Confirmado</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="concluido">Concluído</option>
                    </select>
                </div>

                <button type="submit" class="btn" style="background-color: #28a745; color: white; width: 100%;">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script>
        function abrirEdicao(id, dados) {
            document.getElementById('editar_id').value = id;
            document.getElementById('editar_nome').value = dados.nome;
            document.getElementById('editar_email').value = dados.email;
            document.getElementById('editar_telefone').value = dados.telefone;
            document.getElementById('editar_profissional').value = dados.profissional;
            document.getElementById('editar_data').value = dados.data;
            document.getElementById('editar_hora').value = dados.hora;
            document.getElementById('editar_estilo').value = dados.estilo;
            document.getElementById('editar_descricao').value = dados.descricao;
            document.getElementById('editar_status').value = dados.status;
            document.getElementById('modalEdicao').style.display = 'block';
        }

        function fecharEdicao() {
            document.getElementById('modalEdicao').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modalEdicao');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
