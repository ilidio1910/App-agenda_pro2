<?php
/**
 * Sistema de Logs de Auditoria
 * Arquivo: logs_auditoria.php
 */

// Função para registrar log
function registrarLog($acao, $detalhes = '', $usuario = 'Sistema') {
    $log_dir = dirname(__DIR__) . '/logs/';
    
    // Criar diretório se não existir
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $arquivo_log = $log_dir . date('Y-m-d') . '_auditoria.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $linha_log = "[$timestamp] [$ip] [$usuario] $acao: $detalhes" . PHP_EOL;
    
    file_put_contents($arquivo_log, $linha_log, FILE_APPEND | LOCK_EX);
}

// Função para obter logs recentes
function obterLogsRecentes($dias = 7) {
    $log_dir = dirname(__DIR__) . '/logs/';
    $logs = [];
    
    if (!is_dir($log_dir)) {
        return $logs;
    }
    
    for ($i = 0; $i < $dias; $i++) {
        $data = date('Y-m-d', strtotime("-$i days"));
        $arquivo = $log_dir . $data . '_auditoria.log';
        
        if (file_exists($arquivo)) {
            $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $logs = array_merge($logs, array_reverse($linhas));
        }
    }
    
    return array_slice($logs, 0, 100); // Últimos 100 logs
}
?>