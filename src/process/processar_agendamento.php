<?php
/**
 * Processador de Agendamento
 * Arquivo: src/process/processar_agendamento.php
 */

session_start();

// Autoload das classes
require_once dirname(dirname(dirname(__FILE__))) . '/autoload.php';
require_once dirname(dirname(dirname(__FILE__))) . '/logs/logs_auditoria.php';

// $emailManager = new EmailManager(); // Removido - sistema de email desabilitado
$agendamento = new Agendamento();

// Validar os dados do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    registrarLog('PROCESSAR_POST', 'Dados recebidos: ' . json_encode($_POST));
    
    // Coletar e sanitizar dados
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $style = trim($_POST['style'] ?? '');
    $description = trim($_POST['description'] ?? '');

    registrarLog('PROCESSAR_DADOS', "Nome: $name, Email: $email, Data: $date, Hora: $time");

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
        registrarLog('PROCESSAR_ERROS_VALIDACAO', 'Erros: ' . implode(', ', $erros));
        $_SESSION['erro'] = implode(', ', $erros);
        header('Location: ../../index.php');
        exit;
    }

    registrarLog('PROCESSAR_VALIDACOES_OK', 'Validações básicas passaram');

    // Verificar se o horário já está disponível
    registrarLog('PROCESSAR_ANTES_DISP', 'Antes da verificação de disponibilidade, erros: ' . (empty($erros) ? 'vazio' : implode(', ', $erros)));
    if (empty($erros)) {
        try {
            registrarLog('PROCESSAR_VERIFICANDO_DISP', "Verificando: $artist, $date, $time");
            if (!$agendamento->verificarDisponibilidade($artist, $date, $time)) {
                $erros[] = "Horário indisponível para esse profissional nesta data";
            }
            registrarLog('PROCESSAR_DISP_OK', 'Disponibilidade verificada');
            registrarLog('PROCESSAR_SEM_ERROS_FINAIS', 'Passou todas as validações, indo para salvamento');
        } catch (Exception $e) {
            $erros[] = "Erro ao verificar disponibilidade: " . $e->getMessage();
        }
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

    registrarLog('PROCESSAR_SALVANDO', 'Tentando salvar agendamento');

    try {
        // Salvar no banco de dados
        $id = $agendamento->salvar($dados_agendamento);
        registrarLog('PROCESSAR_SALVO_BD', "Agendamento salvo com ID: $id");
        
        // Adicionar ID aos dados para email
        $dados_agendamento['id'] = $id;
        $dados_agendamento['data_envio'] = date('Y-m-d H:i:s');
        
        // Salvar em arquivo JSON como backup
        $arquivo_agendamentos = dirname(dirname(dirname(__FILE__))) . '/storage/agendamentos.json';
        registrarLog('PROCESSAR_JSON_INICIO', "Arquivo JSON: $arquivo_agendamentos");
        
        $agendamentos = [];
        
        if (file_exists($arquivo_agendamentos)) {
            $agendamentos = json_decode(file_get_contents($arquivo_agendamentos), true) ?? [];
            registrarLog('PROCESSAR_JSON_LIDO', 'JSON lido, ' . count($agendamentos) . ' agendamentos existentes');
        } else {
            registrarLog('PROCESSAR_JSON_NAO_EXISTE', 'Arquivo JSON não existe');
        }
        
        $agendamentos[] = $dados_agendamento;
        registrarLog('PROCESSAR_JSON_ADICIONADO', 'Novo agendamento adicionado ao array');
        
        $resultado_json = file_put_contents($arquivo_agendamentos, json_encode($agendamentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        registrarLog('PROCESSAR_JSON_SALVO', "JSON salvo, resultado: $resultado_json");

        // Sistema de email removido
        // $emailManager->enviarConfirmacaoAgendamento($dados_agendamento);
        // $emailManager->notificarAdminAgendamento($dados_agendamento);

        $_SESSION['sucesso'] = "Agendamento realizado com sucesso!";
        header('Location: ../../index.php');
        exit;
        
    } catch (Exception $e) {
        registrarLog('PROCESSAR_ERRO', 'Erro ao salvar: ' . $e->getMessage());
        $_SESSION['erro'] = "Erro ao processar agendamento: " . $e->getMessage();
        header('Location: ../../index.php');
        exit;
    }

} else {
    // Se não for POST, redirecionar
    header('Location: ../../index.php');
    exit;
}
?>
