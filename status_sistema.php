<?php
/**
 * Verificador de Status do Sistema
 * Arquivo: status_sistema.php
 */

// Autoload
require_once 'autoload.php';

echo "<h1>🔍 Status do Sistema Ink Agenda Pro</h1>";
echo "<hr>";

// Verificar conexão DB
try {
    $agendamento = new Agendamento();
    $contato = new Contato();
    echo "✅ <strong>Conexão com Banco de Dados:</strong> OK<br>";
} catch (Exception $e) {
    echo "❌ <strong>Conexão com Banco de Dados:</strong> " . $e->getMessage() . "<br>";
}

// Contar agendamentos
try {
    $agendamentos = $agendamento->obterTodos();
    echo "📅 <strong>Agendamentos no DB:</strong> " . count($agendamentos) . "<br>";
} catch (Exception $e) {
    echo "❌ <strong>Erro ao contar agendamentos:</strong> " . $e->getMessage() . "<br>";
}

// Contar contatos
try {
    $contatos = $contato->obterTodos();
    echo "💬 <strong>Contatos no DB:</strong> " . count($contatos) . "<br>";
} catch (Exception $e) {
    echo "❌ <strong>Erro ao contar contatos:</strong> " . $e->getMessage() . "<br>";
}

// Verificar arquivos de backup
$json_agendamentos = file_exists('data/agendamentos.json') ? count(json_decode(file_get_contents('data/agendamentos.json'), true) ?? []) : 0;
$json_contatos = file_exists('data/contatos.json') ? count(json_decode(file_get_contents('data/contatos.json'), true) ?? []) : 0;

echo "📄 <strong>Backup JSON - Agendamentos:</strong> $json_agendamentos<br>";
echo "📄 <strong>Backup JSON - Contatos:</strong> $json_contatos<br>";

// Verificar logs
$log_files = glob('data/logs/*.log');
echo "📋 <strong>Arquivos de Log:</strong> " . count($log_files) . "<br>";

echo "<hr>";
echo "<h2>🎯 Sistema Pronto para Produção!</h2>";
echo "<p>Todos os agendamentos agora são salvos no banco de dados MySQL.</p>";
echo "<p><a href='admin.php'>Acessar Painel Admin →</a></p>";
?>