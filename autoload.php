<?php
/**
 * Autoloader - Carrega automaticamente as classes do projeto
 */

define('APP_PATH', dirname(__FILE__));
define('SRC_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'src');
define('CLASSES_PATH', SRC_PATH . DIRECTORY_SEPARATOR . 'classes');
define('CONFIG_PATH', SRC_PATH . DIRECTORY_SEPARATOR . 'config');
define('ADMIN_PATH', SRC_PATH . DIRECTORY_SEPARATOR . 'admin');
define('PROCESS_PATH', SRC_PATH . DIRECTORY_SEPARATOR . 'process');
define('MIGRATIONS_PATH', SRC_PATH . DIRECTORY_SEPARATOR . 'migrations');
define('PUBLIC_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'public');

/**
 * Função para auto-carregar as classes
 */
function autoload($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    $paths = [
        CLASSES_PATH . DIRECTORY_SEPARATOR . $class . '.php',
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            include_once $file;
            return true;
        }
    }
    
    return false;
}

// Registrar o autoloader
spl_autoload_register('autoload');

// Incluir arquivo de configuração do banco de dados
require_once CONFIG_PATH . DIRECTORY_SEPARATOR . 'Database.php';
?>
