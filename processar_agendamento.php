<?php
session_start();

// Autoload das classes
require_once 'autoload.php';
require_once 'logs_auditoria.php';

// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Registrar que o script foi executado
registrarLog('PROCESSAR_INICIADO', 'Processar agendamento iniciado');

$emailManager = new EmailManager();
$agendamento = new Agendamento();

// Validar os dados do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['erro'] = "Erro de segurança: Token CSRF inválido.";
        header('Location: index.php');
        exit;
    }

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

    // Verificar se o horário já está disponível
    if (empty($erros)) {
        try {
            if (!$agendamento->verificarDisponibilidade($artist, $date, $time)) {
                $erros[] = "Horário indisponível para esse profissional nesta data";
            }
        } catch (Exception $e) {
            error_log("Erro ao verificar disponibilidade: " . $e->getMessage());
            $erros[] = "Erro ao verificar disponibilidade";
        }
    }

    // Se houver erros, redirecionar com mensagem
    if (!empty($erros)) {
        $_SESSION['erro'] = implode(', ', $erros);
        header('Location: index.php');
        exit;
    }

    // Preparar dados para salvar
    $dados_agendamento = [
        'nome' => $name,
        'telefone' => $phone,
        'email' => $email,
        'profissional' => $artist,
        'data' => $date,
        'hora' => $time,
        'estilo' => $style,
        'descricao' => $description
    ];

    try {
        // Salvar no banco de dados
        $id = $agendamento->salvar($dados_agendamento);

        // Adicionar ID aos dados para email
        $dados_agendamento['id'] = $id;
        $dados_agendamento['data_envio'] = date('Y-m-d H:i:s');

        // Registrar log de auditoria
        registrarLog('AGENDAMENTO_CRIADO', "Cliente: $name, Profissional: $artist, Data: $date $time");

        // Enviar emails
        $emailManager->enviarConfirmacaoAgendamento($dados_agendamento);
        $emailManager->notificarAdminAgendamento($dados_agendamento);

        $_SESSION['sucesso'] = "Agendamento realizado com sucesso! Enviaremos um email de confirmação em breve.";
        header('Location: index.php');
        exit;

    } catch (Exception $e) {
        error_log("Erro ao processar agendamento: " . $e->getMessage());
        $_SESSION['erro'] = "Erro ao processar agendamento: " . $e->getMessage();
        header('Location: index.php');
        exit;
    }

} else {
    // Se não for POST, redirecionar
    header('Location: index.php');
    exit;
}
?>