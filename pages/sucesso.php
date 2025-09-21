<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    // Se não estiver autenticado, redirecionar para login
    header('Location: user/user_login.php');
    exit;
}

// Verificar se há email do usuário na sessão
if (!isset($_SESSION['user_email'])) {
    header('Location: user/user_login.php');
    exit;
}

$user_email = $_SESSION['user_email'];
$mensagem = "Currículo cadastrado com sucesso!";
$ip = isset($_SESSION['ip_envio']) ? $_SESSION['ip_envio'] : 'Não disponível';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currículo Enviado - SESAP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        .success-icon {
            font-size: 80px;
            color: #10b981;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        h1 {
            color: #2c5aa0;
            margin-bottom: 20px;
            font-size: 28px;
        }
        
        .message {
            background: #f0f9ff;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: left;
        }
        
        .info-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .info-title {
            color: #2c5aa0;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .info-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        
        .info-value {
            color: #6b7280;
            margin-top: 5px;
        }
        
        .actions {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #2c5aa0;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1e3a8a;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }
        
        .footer-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        
        .email-status {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: left;
        }
        
        .email-status-title {
            color: #92400e;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .email-status-text {
            color: #78350f;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1>Currículo Enviado com Sucesso!</h1>
        
        <div class="message">
            <p>Bem-vindo(a) à sua área pessoal, <strong><?php echo htmlspecialchars($user_email); ?></strong>!</p>
            <p><?php echo htmlspecialchars($mensagem); ?></p>
        </div>

        <div class="email-status">
            <div class="email-status-title">👤 Área do Candidato</div>
            <div class="email-status-text">
                Você está logado como: <strong><?php echo htmlspecialchars($user_email); ?></strong><br>
                Esta é sua área pessoal onde pode acompanhar o status da sua candidatura.
            </div>
        </div>
        

        
        <div class="info-box">
            <div class="info-title">📋 Próximos Passos</div>
            
            <div class="info-item">
                <div class="info-label">1. Confirmação por Email</div>
                <div class="info-value">
                    Você receberá um email de confirmação com todos os detalhes do seu envio
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">2. Análise do Currículo</div>
                <div class="info-value">
                    Nossa equipe de RH analisará seu currículo em até 20 dias úteis
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">3. Retorno</div>
                <div class="info-value">
                    Entraremos em contato caso seu perfil seja compatível com nossas vagas
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">4. Acompanhamento</div>
                <div class="info-value">
                    Acompanhe nosso site para novas oportunidades e processos seletivos
                </div>
            </div>
        </div>
        
        <div class="info-box">
            <div class="info-title">ℹ️ Informações Técnicas</div>
            
            <div class="info-item">
                <div class="info-label">Data e Hora do Envio:</div>
                <div class="info-value"><?php echo date('d/m/Y') . ' às ' . date('H:i:s'); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">IP de Origem:</div>
                <div class="info-value"><?php echo htmlspecialchars($ip); ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Status:</div>
                <div class="info-value">✅ Processado com sucesso</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="user/user_dashboard.php" class="btn btn-primary" style="margin-bottom: 15px;">
                📊 Meu Dashboard
            </a>
            <a href="user/user_logout.php" class="btn btn-secondary">
                🚪 Sair
            </a>
            <a href="/sesap_curriculo/public/index.html" class="btn btn-secondary">
                🏠 Voltar ao Início
            </a>
        </div>
        
        <div class="footer-info">
            <p><strong>SESAP-RN</strong> - Secretaria de Estado da Saúde Pública do Rio Grande do Norte</p>
            <p>Obrigado por seu interesse em fazer parte da nossa equipe!</p>
        </div>
    </div>
</body>
</html>