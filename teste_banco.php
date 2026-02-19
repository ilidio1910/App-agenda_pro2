<?php
/**
 * Arquivo de teste para verificar conexão com o banco de dados
 */

// Autoload das classes
require_once 'autoload.php';

echo "<h1>Teste de Conexão com Banco de Dados</h1>";

try {
    // Tenta verificar se a conexão existe
    global $pdo;
    
    if ($pdo) {
        echo "<p style='color: green;'><strong>✅ Conexão com banco de dados estabelecida!</strong></p>";
        
        // Tenta listar as tabelas
        $tables = $pdo->query("SHOW TABLES FROM " . DB_NAME)->fetchAll();
        echo "<p><strong>Tabelas criadas:</strong></p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . reset($table) . "</li>";
        }
        echo "</ul>";
        
        // Tenta contar os agendamentos
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM agendamentos");
        $result = $stmt->fetch();
        echo "<p><strong>Total de agendamentos:</strong> " . $result['total'] . "</p>";
        
        // Mostra os últimos agendamentos
        $ultimosAgendamentos = $pdo->query("SELECT * FROM agendamentos ORDER BY data_envio DESC LIMIT 5")->fetchAll();
        echo "<p><strong>Últimos 5 agendamentos:</strong></p>";
        if (!empty($ultimosAgendamentos)) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Data</th><th>Hora</th><th>Profissional</th></tr>";
            foreach ($ultimosAgendamentos as $agendamento) {
                echo "<tr>";
                echo "<td>" . $agendamento['id'] . "</td>";
                echo "<td>" . $agendamento['nome'] . "</td>";
                echo "<td>" . $agendamento['email'] . "</td>";
                echo "<td>" . $agendamento['data'] . "</td>";
                echo "<td>" . $agendamento['hora'] . "</td>";
                echo "<td>" . $agendamento['profissional'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>Nenhum agendamento encontrado ainda.</p>";
        }
        
    } else {
        echo "<p style='color: red;'><strong>❌ Erro: Conexão com banco de dados não estabelecida!</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Erro ao conectar:</strong> " . $e->getMessage() . "</p>";
}
?>
