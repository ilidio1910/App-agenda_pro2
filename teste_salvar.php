<?php
require_once 'autoload.php';
$a = new Agendamento();
$dados = [
    'nome' => 'Teste',
    'telefone' => '123456789',
    'email' => 'teste@teste.com',
    'profissional' => 'teste',
    'data' => '2026-02-20',
    'hora' => '10:00',
    'estilo' => 'teste',
    'descricao' => 'teste'
];
try {
    $id = $a->salvar($dados);
    echo "Salvo com ID: $id\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>