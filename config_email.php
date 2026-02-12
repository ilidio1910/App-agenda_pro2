<?php
/**
 * Configurações de Email
 * Configure os dados SMTP aqui
 */

return [
    // Tipo de SMTP (gmail, outlook, custom, localhost)
    'smtp_host' => 'smtp.gmail.com',  // ou seu servidor SMTP
    'smtp_port' => 587,                // Porta do SMTP (587 para TLS, 465 para SSL)
    'smtp_secure' => 'tls',            // 'tls' ou 'ssl'
    'smtp_user' => 'ilidio.soares@gmail.com',  // Seu email (configure com seu email real)
    'smtp_password' => 'ulxqwtczyweqmjtg
',    // Sua senha ou senha de app (configure com sua senha)
    
    // Email remetente
    'from_email' => 'ilidio.soares@gmail.com',
    'from_name' => 'Ink Agenda Pro',
    
    // Email para receber contatos (onde você vai receber as mensagens)
    'admin_email' => 'ilidio.soares@gmai.com',
    
    // Ativar/desativar envio de emails
    'enabled' => true,
];
?>
