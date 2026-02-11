<?php
session_start();

// Incluir gerenciador de emails
require_once 'EmailManager.php';
$emailManager = new EmailManager();

// Validar os dados do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Coletar e sanitizar dados
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $style = trim($_POST['style'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validações básicas
    $erros = [];

    if (empty($name)) {
        $erros[] = "Nome é obrigatório";
    }

    if (empty($phone)) {
        $erros[] = "Telefone é obrigatório";
    } elseif (!preg_match('/^[0-9\(\)\s\-]{10,}$/', $phone)) {
        $erros[] = "Telefone inválido";
    }

    if (empty($email)) {
        $erros[] = "E-mail é obrigatório";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    }

    if (empty($artist)) {
        $erros[] = "Profissional é obrigatório";
    }

    if (empty($date)) {
        $erros[] = "Data é obrigatória";
    } elseif (strtotime($date) < strtotime('today')) {
        $erros[] = "A data não pode ser no passado";
    }

    if (empty($time)) {
        $erros[] = "Horário é obrigatório";
    }

    if (empty($style)) {
        $erros[] = "Estilo da tatuagem é obrigatório";
    }

    // Se houver erros, redirecionar com mensagem
    if (!empty($erros)) {
        $_SESSION['erro'] = implode(', ', $erros);
        header('Location: index.php');
        exit;
    }

    // Aqui você salvaria no banco de dados
    // Por enquanto, apenas exibir mensagem de sucesso
    
    // Salvar em um arquivo de log (opcional)
    $dados_agendamento = [
        'nome' => $name,
        'telefone' => $phone,
        'email' => $email,
        'profissional' => $artist,
        'data' => $date,
        'hora' => $time,
        'estilo' => $style,
        'descricao' => $description,
        'data_envio' => date('Y-m-d H:i:s')
    ];

    // Salvar em arquivo JSON (backup)
    $arquivo_agendamentos = 'agendamentos.json';
    $agendamentos = [];
    
    if (file_exists($arquivo_agendamentos)) {
        $agendamentos = json_decode(file_get_contents($arquivo_agendamentos), true) ?? [];
    }
    
    $agendamentos[] = $dados_agendamento;
    file_put_contents($arquivo_agendamentos, json_encode($agendamentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Enviar emails
    $emailManager->enviarConfirmacaoAgendamento($dados_agendamento);
    $emailManager->notificarAdminAgendamento($dados_agendamento);

    $_SESSION['sucesso'] = "Agendamento realizado com sucesso! Enviaremos um email de confirmação em breve.";
    header('Location: index.php');
    exit;

} else {
    // Se não for POST, redirecionar
    header('Location: index.php');
    exit;
}
?>
