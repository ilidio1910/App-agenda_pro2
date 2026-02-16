<?php
session_start();

// Incluir gerenciador de emails e logs
require_once 'EmailManager.php';
require_once 'logs_auditoria.php';
$emailManager = new EmailManager();

// Validar os dados do formulário de contato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['erro'] = "Erro de segurança: Token CSRF inválido.";
        header('Location: index.php');
        exit;
    }
    
    // Coletar e sanitizar dados
    $name = trim($_POST['contactName'] ?? '');
    $email = trim($_POST['contactEmail'] ?? '');
    $subject = trim($_POST['contactSubject'] ?? '');
    $message = trim($_POST['contactMessage'] ?? '');

    // Validações básicas
    $erros = [];

    if (empty($name)) {
        $erros[] = "Nome é obrigatório";
    }

    if (empty($email)) {
        $erros[] = "E-mail é obrigatório";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    }

    if (empty($subject)) {
        $erros[] = "Assunto é obrigatório";
    }

    if (empty($message)) {
        $erros[] = "Mensagem é obrigatória";
    } elseif (strlen($message) < 10) {
        $erros[] = "Mensagem muito curta (mínimo 10 caracteres)";
    }

    // Se houver erros, redirecionar com mensagem
    if (!empty($erros)) {
        $_SESSION['erro'] = implode(', ', $erros);
        header('Location: index.php');
        exit;
    }

    // Salvar dados do contato
    $dados_contato = [
        'nome' => $name,
        'email' => $email,
        'assunto' => $subject,
        'mensagem' => $message,
        'data_envio' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    // Salvar em arquivo JSON
    $arquivo_contatos = 'data/contatos.json';
    $contatos = [];
    
    if (file_exists($arquivo_contatos)) {
        $contatos = json_decode(file_get_contents($arquivo_contatos), true) ?? [];
    }
    
    $contatos[] = $dados_contato;
    file_put_contents($arquivo_contatos, json_encode($contatos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Registrar log de auditoria
    registrarLog('CONTATO_ENVIADO', "Nome: $name, Assunto: $subject");

    // Enviar emails
    $emailManager->enviarConfirmacaoContato($dados_contato);
    $emailManager->notificarAdminContato($dados_contato);

    $_SESSION['sucesso'] = "Mensagem enviada com sucesso! Entraremos em contato em breve.";
    header('Location: index.php');
    exit;

} else {
    // Se não for POST, redirecionar
    header('Location: index.php');
    exit;
}
?>
