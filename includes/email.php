<?php
require_once 'config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Função para enviar email com os dados do formulário usando PHPMailer
function enviarEmail($dados, $caminhoArquivo, $nomeArquivo) {
    try {
        // Verificar se as configurações SMTP estão definidas
        if (SMTP_USERNAME === 'seu_email@gmail.com' || SMTP_PASSWORD === 'sua_senha_app') {
            error_log('ERRO: Configurações SMTP não foram definidas. Configure as credenciais no config.php');
            return false;
        }
        
        $mail = new PHPMailer(true);
        
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0; // Desabilitar debug em produção
        
        // Remetente e destinatário
        $mail->setFrom(EMAIL_FROM, 'Sistema de Currículos SESAP');
        $mail->addAddress(EMAIL_TO);
        $mail->addReplyTo($dados['email'], $dados['nome']);
        
        // Anexar arquivo se existir
        if (file_exists($caminhoArquivo)) {
            $mail->addAttachment($caminhoArquivo, $nomeArquivo);
        }
        
        // Configurar como HTML
        $mail->isHTML(true);
        $mail->Subject = 'Novo Currículo Recebido - ' . $dados['nome'];
        
        // Corpo do email em HTML
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 20px; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #2c3e50; }
                .value { margin-left: 10px; }
                .footer { background: #ecf0f1; padding: 15px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Novo Currículo Recebido</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>Nome:</span>
                        <span class='value'>" . htmlspecialchars($dados['nome']) . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>E-mail:</span>
                        <span class='value'>" . htmlspecialchars($dados['email']) . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Telefone:</span>
                        <span class='value'>" . htmlspecialchars($dados['telefone']) . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Cargo Desejado:</span>
                        <span class='value'>" . htmlspecialchars($dados['cargo_desejado']) . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Escolaridade:</span>
                        <span class='value'>" . htmlspecialchars($dados['escolaridade']) . "</span>
                    </div>";
        
        if (!empty($dados['observacoes'])) {
            $htmlBody .= "
                    <div class='field'>
                        <span class='label'>Observações:</span>
                        <div class='value'>" . nl2br(htmlspecialchars($dados['observacoes'])) . "</div>
                    </div>";
        }
        
        $htmlBody .= "
                    <div class='field'>
                        <span class='label'>IP de Envio:</span>
                        <span class='value'>" . getUserIP() . "</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Data/Hora:</span>
                        <span class='value'>" . date('d/m/Y') . " às " . date('H:i:s') . "</span>
                    </div>
                </div>
                <div class='footer'>
                    <p>Este email foi enviado automaticamente pelo sistema de cadastro de currículos.</p>
                </div>
            </div>
        </body>
        </html>";
        
        
        // Definir o corpo do email
        $mail->Body = $htmlBody;
        
        // Versão texto simples para compatibilidade
        $mail->AltBody = "Prezado(a) " . $dados['nome'] . ",\n\n" .
                        "Confirmamos o recebimento do seu currículo enviado para a SESAP-RN.\n" .
                        "Seus dados foram registrados em nosso sistema com sucesso.\n\n" .
                        "Protocolo: SESAP-" . date('Ymd') . "-" . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) . "\n\n" .
                        "Dados enviados:\n" .
                        "Nome: " . $dados['nome'] . "\n" .
                        "E-mail: " . $dados['email'] . "\n" .
                        "Telefone: " . $dados['telefone'] . "\n" .
                        "Cargo: " . $dados['cargo_desejado'] . "\n" .
                        "Escolaridade: " . $dados['escolaridade'] . "\n" .
                        "Data: " . date('d/m/Y') . " às " . date('H:i:s') . "\n\n" .
                        "Próximas etapas:\n" .
                        "- Análise inicial do currículo\n" .
                        "- Verificação de compatibilidade\n" .
                        "- Contato em caso de oportunidades\n" .
                        "- Retorno em até 20 dias úteis\n\n" .
                        "SESAP-RN - Secretaria de Estado da Saúde Pública";
        
        // Enviar email
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log do erro
        error_log('Erro no envio de email: ' . $e->getMessage());
        return false;
    }
}



// Função para enviar comprovante de recebimento para o candidato
function enviarComprovante($dados) {
    try {
        // Verificar se as configurações SMTP estão definidas
        if (SMTP_USERNAME === 'seu_email@gmail.com' || SMTP_PASSWORD === 'sua_senha_app') {
            error_log('ERRO: Configurações SMTP não foram definidas. Configure as credenciais no config.php');
            return false;
        }
        
        $mail = new PHPMailer(true);
        
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0; // Desabilitar debug em produção
        
        // Remetente e destinatário
        $mail->setFrom(EMAIL_FROM, 'SESAP - Sistema de Currículos');
        $mail->addAddress($dados['email'], $dados['nome']);
        
        // Configurar como HTML
        $mail->isHTML(false); // Usar texto simples para compatibilidade
        $mail->Subject = 'Confirmação de Recebimento - Currículo SESAP-RN';
        
        // Gerar protocolo único
        $protocolo = 'SESAP-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        // Corpo do email em texto simples
        $textBody = "🏛️ SESAP-RN - Confirmação de Recebimento de Currículo

Prezado(a) " . $dados['nome'] . ",

✅ CURRÍCULO RECEBIDO COM SUCESSO!

Confirmamos o recebimento do seu currículo enviado para a SESAP-RN.

📋 DADOS DO CANDIDATO:
• Nome: " . $dados['nome'] . "
• E-mail: " . $dados['email'] . "
• Telefone: " . $dados['telefone'] . "
• Cargo Desejado: " . $dados['cargo_desejado'] . "
• Escolaridade: " . $dados['escolaridade'] . "
• Data de Envio: " . date('d/m/Y') . " às " . date('H:i:s') . "

🔢 PROTOCOLO: #" . $protocolo . "
(Guarde este número para futuras consultas)

🔄 PRÓXIMAS ETAPAS:
• Análise inicial do currículo pela equipe de RH
• Verificação de compatibilidade com vagas disponíveis  
• Contato em caso de oportunidades compatíveis
• Retorno em até 20 dias úteis sobre o status

⚠️ INFORMAÇÕES IMPORTANTES:
• Não é necessário reenviar seu currículo
• Mantenha seus dados atualizados
• Acompanhe nosso site para novos concursos
• Guarde este e-mail como comprovante

🏥 SESAP-RN - Secretaria de Estado da Saúde Pública do Rio Grande do Norte
© " . date('Y') . " - Todos os direitos reservados.";

        
        // Enviar o email
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}
?>