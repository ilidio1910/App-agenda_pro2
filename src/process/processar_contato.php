<?php
/**
 * Processador de Contato
 * Arquivo: src/process/processar_contato.php
 */

session_start();

// Autoload das classes
require_once dirname(dirname(dirname(__FILE__))) . '/autoload.php';

$emailManager = new EmailManager();
$contato = new Contato();

// Validar os dados do formulário de contato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Coletar e sanitizar dados
    $name = trim($_POST['contactName'] ?? '');
    $email = trim($_POST['contactEmail'] ?? '');
    $subject = trim($_POST['contactSubject'] ?? '');
    $message = trim($_POST['contactMessage'] ?? '');
    $phone = trim($_POST['contactPhone'] ?? '');

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
        header('Location: ../../index.php');
        exit;
    }

    // Preparar dados para salvar
    $dados_contato = [
        'nome' => $name,
        'email' => $email,
        'telefone' => $phone,
        'assunto' => $subject,
        'mensagem' => $message
    ];

    try {
        // Salvar no banco de dados
        $id = $contato->salvar($dados_contato);
        
        // Adicionar ID para email
        $dados_contato['id'] = $id;
        $dados_contato['data_envio'] = date('Y-m-d H:i:s');
        
        // Salvar em arquivo JSON como backup
        $arquivo_contatos = dirname(dirname(dirname(__FILE__))) . '/contatos.json';
        $contatos = [];
        
        if (file_exists($arquivo_contatos)) {
            $contatos = json_decode(file_get_contents($arquivo_contatos), true) ?? [];
        }
        
        $contatos[] = $dados_contato;
        file_put_contents($arquivo_contatos, json_encode($contatos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Enviar emails
        $emailManager->enviarConfirmacaoContato($dados_contato);
        $emailManager->notificarAdminContato($dados_contato);

        $_SESSION['sucesso'] = "Mensagem enviada com sucesso! Entraremos em contato em breve.";
        header('Location: ../../index.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao processar mensagem: " . $e->getMessage();
        header('Location: ../../index.php');
        exit;
    }

} else {
    // Se não for POST, redirecionar
    header('Location: ../../index.php');
    exit;
}
?>
