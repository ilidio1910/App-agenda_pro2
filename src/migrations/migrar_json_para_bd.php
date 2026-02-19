<?php
/**
 * Script de Migração: JSON → Banco de Dados
 * Arquivo: src/migrations/migrar_json_para_bd.php
 * Execute este arquivo uma única vez para migrar todos os agendamentos
 */

// Autoload das classes
require_once dirname(dirname(dirname(__FILE__))) . '/autoload.php';

$agendamento = new Agendamento();
$arquivo_json = dirname(dirname(dirname(__FILE__))) . '/storage/agendamentos.json';

// Verificar se o arquivo JSON existe
if (!file_exists($arquivo_json)) {
    die("❌ Erro: Arquivo 'agendamentos.json' não encontrado!");
}

// Ler dados do JSON
$dados_json = file_get_contents($arquivo_json);
$agendamentos = json_decode($dados_json, true);

if (empty($agendamentos)) {
    die("❌ Erro: Arquivo JSON vazio ou inválido!");
}

echo "<h1>🔄 Migração de Dados: JSON → Banco de Dados</h1>";
echo "<p>Iniciando migração de " . count($agendamentos) . " agendamentos...</p>";
echo "<hr>";

$sucesso = 0;
$erros = 0;

foreach ($agendamentos as $index => $ag) {
    try {
        // Preparar dados (remover data_envio pois será criada automaticamente)
        $dados = [
            'nome' => $ag['nome'] ?? '',
            'telefone' => $ag['telefone'] ?? '',
            'email' => $ag['email'] ?? '',
            'profissional' => $ag['profissional'] ?? '',
            'data' => $ag['data'] ?? date('Y-m-d'),
            'hora' => $ag['hora'] ?? '10:00',
            'estilo' => $ag['estilo'] ?? '',
            'descricao' => $ag['descricao'] ?? ''
        ];
        
        // Salvar no banco
        $id = $agendamento->salvar($dados);
        echo "✅ Agendamento #" . ($index + 1) . " importado com ID: {$id} - {$ag['nome']}<br>";
        $sucesso++;
        
    } catch (Exception $e) {
        echo "❌ Erro ao importar agendamento #" . ($index + 1) . ": " . $e->getMessage() . "<br>";
        $erros++;
    }
}

echo "<hr>";
echo "<h2>📊 Resumo da Migração</h2>";
echo "<p><strong>✅ Importados:</strong> {$sucesso}</p>";
echo "<p><strong>❌ Erros:</strong> {$erros}</p>";
echo "<p><strong>📁 Total:</strong> " . count($agendamentos) . "</p>";

if ($erros === 0) {
    echo "<p style='color: green; font-weight: bold;'>🎉 Migração concluída com sucesso!</p>";
    echo "<p><a href='../admin/admin_db.php'>Ver agendamentos no painel admin →</a></p>";
} else {
    echo "<p style='color: red;'>⚠️ Alguns agendamentos tiveram problemas. Verifique os erros acima.</p>";
}
?>
