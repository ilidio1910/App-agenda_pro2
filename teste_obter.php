<?php
require_once 'autoload.php';
$a = new Agendamento();
$ags = $a->obterTodos();
echo 'Total: ' . count($ags) . PHP_EOL;
if (!empty($ags)) {
    print_r($ags[0]);
}
?>