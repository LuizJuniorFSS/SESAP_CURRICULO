<?php
session_start();
require_once '../../includes/config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']) {
    header('Location: user_login.php');
    exit;
}

// Buscar dados do usuário
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM curriculos WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$curriculo = $stmt->fetch();

if (!$curriculo) {
    session_destroy();
    header('Location: user_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Candidatura - SESAP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 1.8em;
        }
        
        .header .user-info {
            text-align: right;
        }
        
        .header .user-info p {
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .status-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            text-align: center;
        }
        
        .status-icon {
            font-size: 4em;
            margin-bottom: 15px;
        }
        
        .status-title {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        
        .status-description {
            color: #7f8c8d;
            font-size: 1.1em;
            line-height: 1.6;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .info-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #555;
            word-wrap: break-word;
        }
        
        .observacoes-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .observacoes-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .observacoes-content {
            color: #555;
            line-height: 1.6;
            white-space: pre-wrap;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        
        .arquivo-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .arquivo-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .download-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: transform 0.2s ease;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
        }
        
        .meta-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 12px;
            margin-top: 25px;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .meta-info h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.3em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .meta-item {
            background: white;
            padding: 18px;
            border-radius: 10px;
            font-size: 0.95em;
            color: #555;
            border-left: 4px solid #3498db;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .meta-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .meta-item strong {
            color: #2c3e50;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .meta-item .value {
            color: #34495e;
            font-size: 1.1em;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Minha Candidatura - SESAP</h1>
            <div class="user-info">
                <p><strong>Bem-vindo(a), <?= htmlspecialchars($curriculo['nome']) ?>!</strong></p>
                <p><?= htmlspecialchars($curriculo['email']) ?></p>
                <a href="user_logout.php" class="logout-btn" style="margin-top: 10px; display: inline-block;">🚪 Sair</a>
            </div>
        </div>
        
        <div class="status-card">
            <div class="status-icon">✅</div>
            <div class="status-title">Currículo Enviado com Sucesso!</div>
            <div class="status-description">
                Seu currículo foi recebido e está sendo analisado por nossa equipe de RH. 
                Você será contatado em caso de compatibilidade com nossas vagas disponíveis.
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>👤 Dados Pessoais</h3>
                <div class="info-item">
                    <div class="info-label">Nome Completo:</div>
                    <div class="info-value"><?= htmlspecialchars($curriculo['nome']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?= htmlspecialchars($curriculo['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telefone:</div>
                    <div class="info-value"><?= htmlspecialchars($curriculo['telefone']) ?></div>
                </div>
            </div>
            
        </div>
        
        <?php if (!empty($curriculo['observacoes'])): ?>
        <div class="observacoes-card">
            <h3>💼 Experiências académicas ou profissionais e Habilidades</h3>
            <div class="observacoes-content"><?= htmlspecialchars($curriculo['observacoes']) ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($curriculo['arquivo'])): ?>
        <div class="arquivo-card">
            <h3>📄 Currículo Anexado</h3>
            <p style="margin-bottom: 15px; color: #7f8c8d;">Clique no botão abaixo para baixar seu currículo:</p>
            <a href="../download.php?id=<?= $curriculo['id'] ?>" class="download-btn" target="_blank">
                📥 Baixar Currículo
            </a>
        </div>
        <?php endif; ?>
        
        <div class="meta-info">
            <h4>ℹ️ Informações do Sistema</h4>
            <div class="meta-grid">
                <div class="meta-item">
                    <strong>ID da Candidatura:</strong>
                    <span class="value">#<?= $curriculo['id'] ?></span>
                </div>
                <div class="meta-item">
                    <strong>Data de Envio:</strong>
                    <span class="value"><?= date('d/m/Y', strtotime($curriculo['data_envio'])) ?> às <?= date('H:i:s', strtotime($curriculo['data_envio'])) ?></span>
                </div>
                <div class="meta-item">
                    <strong>IP de Origem:</strong>
                    <span class="value"><?= htmlspecialchars($curriculo['ip_envio']) ?></span>
                </div>
                <div class="meta-item">
                    <strong>Última Atualização:</strong>
                    <span class="value"><?= date('d/m/Y', strtotime($curriculo['updated_at'])) ?> às <?= date('H:i:s', strtotime($curriculo['updated_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>