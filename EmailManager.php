<?php
/**
 * Classe para gerenciar envio de emails
 * Usa PHPMailer
 */

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailManager {
    private $config;
    private $mailer;

    public function __construct() {
        $this->config = require 'config_email.php';
        $this->inicializarMailer();
    }

    private function inicializarMailer() {
        // Verificar se PHPMailer está disponível
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $this->mailer = new PHPMailer(true);

            try {
                // Configurar SMTP
                $this->mailer->isSMTP();
                $this->mailer->Host = $this->config['smtp_host'];
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $this->config['smtp_user'];
                $this->mailer->Password = $this->config['smtp_password'];
                $this->mailer->SMTPSecure = $this->config['smtp_secure'];
                $this->mailer->Port = $this->config['smtp_port'];
                $this->mailer->CharSet = 'UTF-8';
                $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * Enviar email de confirmação de agendamento
     */
    public function enviarConfirmacaoAgendamento($dados) {
        if (!$this->config['enabled'] || !$this->mailer) {
            return true; // Retornar true mesmo se desativado
        }

        try {
            // Email para o cliente
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($dados['email'], $dados['nome']);

            $assunto = "✅ Agendamento Confirmado - Ink Agenda Pro";
            
            $corpo = "
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #8B5CF6, #3B82F6); color: white; padding: 20px; border-radius: 8px; text-align: center; }
                    .content { background: #f5f5f5; padding: 20px; margin-top: 20px; border-radius: 8px; }
                    .details { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #8B5CF6; }
                    .details p { margin: 5px 0; }
                    .label { font-weight: bold; color: #8B5CF6; }
                    .footer { text-align: center; margin-top: 30px; color: #999; font-size: 0.9rem; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🎨 Seu Agendamento Foi Confirmado!</h1>
                    </div>
                    
                    <div class='content'>
                        <p>Olá <strong>" . htmlspecialchars($dados['nome']) . "</strong>,</p>
                        
                        <p>Seu agendamento foi realizado com sucesso! 🎉</p>
                        
                        <div class='details'>
                            <p><span class='label'>📅 Data:</span> " . date('d/m/Y', strtotime($dados['data'])) . "</p>
                            <p><span class='label'>⏰ Horário:</span> " . htmlspecialchars($dados['hora']) . "</p>
                            <p><span class='label'>👨‍🎨 Profissional:</span> " . htmlspecialchars($dados['profissional']) . "</p>
                            <p><span class='label'>🎨 Estilo:</span> " . htmlspecialchars($dados['estilo']) . "</p>
                        </div>
                        
                        <p>Em breve entraremos em contato para confirmar todos os detalhes.</p>
                        
                        <p>Qualquer dúvida, nos procure!</p>
                        
                        <p>Obrigado! ❤️</p>
                    </div>
                    
                    <div class='footer'>
                        <p>© 2024 Ink Agenda Pro - Todos os direitos reservados</p>
                        <p>Não responda este email</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $this->mailer->Subject = $assunto;
            $this->mailer->Body = $corpo;
            $this->mailer->isHTML(true);
            $this->mailer->AltBody = "Seu agendamento foi confirmado! Profissional: " . $dados['profissional'] . " - Data: " . $dados['data'] . " - Hora: " . $dados['hora'];

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email de agendamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de notificação ao admin sobre novo agendamento
     */
    public function notificarAdminAgendamento($dados) {
        if (!$this->config['enabled'] || !$this->mailer) {
            return true;
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->config['admin_email']);

            $assunto = "🔔 Novo Agendamento - " . htmlspecialchars($dados['nome']);
            
            $corpo = "
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #8B5CF6; color: white; padding: 20px; border-radius: 8px; text-align: center; }
                    .content { background: #f5f5f5; padding: 20px; margin-top: 20px; border-radius: 8px; }
                    .details { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #8B5CF6; }
                    .details p { margin: 5px 0; }
                    .label { font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>🔔 Novo Agendamento Recebido</h2>
                    </div>
                    
                    <div class='content'>
                        <div class='details'>
                            <p><span class='label'>Nome:</span> " . htmlspecialchars($dados['nome']) . "</p>
                            <p><span class='label'>E-mail:</span> " . htmlspecialchars($dados['email']) . "</p>
                            <p><span class='label'>Telefone:</span> " . htmlspecialchars($dados['telefone']) . "</p>
                            <p><span class='label'>Profissional:</span> " . htmlspecialchars($dados['profissional']) . "</p>
                            <p><span class='label'>Data:</span> " . date('d/m/Y', strtotime($dados['data'])) . "</p>
                            <p><span class='label'>Horário:</span> " . htmlspecialchars($dados['hora']) . "</p>
                            <p><span class='label'>Estilo:</span> " . htmlspecialchars($dados['estilo']) . "</p>
                            <p><span class='label'>Descrição:</span> " . htmlspecialchars($dados['descricao']) . "</p>
                            <p><span class='label'>Data do Agendamento:</span> " . date('d/m/Y H:i:s') . "</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ";

            $this->mailer->Subject = $assunto;
            $this->mailer->Body = $corpo;
            $this->mailer->isHTML(true);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao notificar admin sobre agendamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de confirmação de contato
     */
    public function enviarConfirmacaoContato($dados) {
        if (!$this->config['enabled'] || !$this->mailer) {
            return true;
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($dados['email'], $dados['nome']);

            $assunto = "✅ Recebemos sua mensagem - Ink Agenda Pro";
            
            $corpo = "
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #8B5CF6, #3B82F6); color: white; padding: 20px; border-radius: 8px; text-align: center; }
                    .content { background: #f5f5f5; padding: 20px; margin-top: 20px; border-radius: 8px; }
                    .details { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #8B5CF6; }
                    .details p { margin: 5px 0; }
                    .label { font-weight: bold; color: #8B5CF6; }
                    .footer { text-align: center; margin-top: 30px; color: #999; font-size: 0.9rem; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>✅ Mensagem Recebida!</h1>
                    </div>
                    
                    <div class='content'>
                        <p>Olá <strong>" . htmlspecialchars($dados['nome']) . "</strong>,</p>
                        
                        <p>Obrigado por entrar em contato conosco! 📧</p>
                        
                        <p>Recebemos sua mensagem com o assunto: <strong>" . htmlspecialchars($dados['assunto']) . "</strong></p>
                        
                        <p>Nossa equipe irá analisá-la e responderá em breve.</p>
                        
                        <div class='details'>
                            <p><strong>Sua Mensagem:</strong></p>
                            <p>" . nl2br(htmlspecialchars($dados['mensagem'])) . "</p>
                        </div>
                        
                        <p>Valorizamos seu contato! ❤️</p>
                    </div>
                    
                    <div class='footer'>
                        <p>© 2024 Ink Agenda Pro - Todos os direitos reservados</p>
                        <p>Não responda este email</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $this->mailer->Subject = $assunto;
            $this->mailer->Body = $corpo;
            $this->mailer->isHTML(true);
            $this->mailer->AltBody = "Recebemos sua mensagem: " . $dados['assunto'];

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email de contato: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notificar admin sobre novo contato
     */
    public function notificarAdminContato($dados) {
        if (!$this->config['enabled'] || !$this->mailer) {
            return true;
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->config['admin_email']);

            $assunto = "💬 Nova mensagem de contato - " . htmlspecialchars($dados['assunto']);
            
            $corpo = "
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #8B5CF6; color: white; padding: 20px; border-radius: 8px; text-align: center; }
                    .content { background: #f5f5f5; padding: 20px; margin-top: 20px; border-radius: 8px; }
                    .details { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #8B5CF6; }
                    .details p { margin: 5px 0; }
                    .label { font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>💬 Nova Mensagem de Contato</h2>
                    </div>
                    
                    <div class='content'>
                        <div class='details'>
                            <p><span class='label'>Nome:</span> " . htmlspecialchars($dados['nome']) . "</p>
                            <p><span class='label'>E-mail:</span> <a href='mailto:" . htmlspecialchars($dados['email']) . "'>" . htmlspecialchars($dados['email']) . "</a></p>
                            <p><span class='label'>Assunto:</span> " . htmlspecialchars($dados['assunto']) . "</p>
                            <p><span class='label'>Data:</span> " . date('d/m/Y H:i:s') . "</p>
                            <p><span class='label'>Mensagem:</span></p>
                            <p>" . nl2br(htmlspecialchars($dados['mensagem'])) . "</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ";

            $this->mailer->Subject = $assunto;
            $this->mailer->Body = $corpo;
            $this->mailer->isHTML(true);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Erro ao notificar admin sobre contato: " . $e->getMessage());
            return false;
        }
    }
}
?>
