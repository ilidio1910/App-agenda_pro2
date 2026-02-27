<?php
/**
 * Configuração de Segurança Admin
 * Arquivo: config_admin.php
 */

// Hash da senha admin (gerado com password_hash)
// Senha padrão: "J1910@17p" - ALTERE IMEDIATAMENTE EM PRODUÇÃO!
define('ADMIN_PASSWORD_HASH', password_hash('J1910@17p', PASSWORD_DEFAULT));

// Função para verificar senha admin
function verificarSenhaAdmin($senha) {
    return password_verify($senha, ADMIN_PASSWORD_HASH);
}

// Função para verificar se usuário está autenticado
function estaAutenticado() {
    return isset($_SESSION['admin_autenticado']) && $_SESSION['admin_autenticado'] === true;
}

// Função para fazer logout
function logoutAdmin() {
    unset($_SESSION['admin_autenticado']);
    session_regenerate_id(true);
}
?>
