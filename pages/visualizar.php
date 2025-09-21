<?php
require_once '../includes/config.php';

// Verificar se a conexão com o banco existe
if (!isset($conn)) {
    die('Erro: Conexão com banco de dados não encontrada.');
}

$id = $_GET['id'] ?? '';

if (!$id || !is_numeric($id)) {
    header('Location: admin/admin.php');
    exit;
}

// Buscar currículo
$stmt = $conn->prepare("SELECT * FROM curriculos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if (!$curriculo = $resultado->fetch_assoc()) {
    header('Location: admin/admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Currículo - <?= htmlspecialchars($curriculo['nome']) ?></title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .curriculo-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .info-item label {
            font-weight: bold;
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-item .value {
            color: #555;
            font-size: 1.1em;
            word-wrap: break-word;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .observacoes {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
            margin-bottom: 20px;
        }
        
        .observacoes label {
            font-weight: bold;
            color: #2c3e50;
            display: block;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        
        .observacoes .value {
            color: #555;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        .arquivo-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #e74c3c;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .arquivo-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 5px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background: #229954;
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background: #e67e22;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
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
            .container {
                margin: 10px;
            }
            
            .content {
                padding: 15px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 Visualizar Currículo</h1>
            <p>Detalhes completos do candidato</p>
        </div>
        
        <div class="content">
            <div class="curriculo-card">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nome Completo</label>
                        <div class="value"><?= htmlspecialchars($curriculo['nome']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <label>Email</label>
                        <div class="value">
                            <a href="mailto:<?= htmlspecialchars($curriculo['email']) ?>" style="color: #3498db; text-decoration: none;">
                                <?= htmlspecialchars($curriculo['email']) ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <label>Telefone</label>
                        <div class="value">
                            <a href="tel:<?= htmlspecialchars($curriculo['telefone']) ?>" style="color: #3498db; text-decoration: none;">
                                <?= htmlspecialchars($curriculo['telefone']) ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <label>Escolaridade</label>
                        <div class="value"><?= htmlspecialchars($curriculo['escolaridade']) ?></div>
                    </div>
                    
                    <div class="info-item full-width">
                        <label>Cargo Desejado</label>
                        <div class="value"><?= htmlspecialchars($curriculo['cargo_desejado']) ?></div>
                    </div>
                </div>
                
                <?php if ($curriculo['observacoes']): ?>
                    <div class="observacoes">
                        <label>📝 Suas experiências academica ou profissionais e suas habilidades:</label>
                        <div class="value"><?= htmlspecialchars($curriculo['observacoes']) ?></div>
                    </div>
                <?php endif; ?>
                
                <div class="arquivo-section">
                    <h3>📎 Arquivo do Currículo</h3>
                    <p><strong>Nome do arquivo:</strong> <?= htmlspecialchars($curriculo['arquivo_nome']) ?></p>
                    
                    <?php if (file_exists($curriculo['arquivo_caminho'])): ?>
                        <div style="margin-top: 15px;">
                            <a href="download.php?id=<?= $curriculo['id'] ?>&action=download" 
                               class="btn btn-success">
                                📥 Baixar Currículo
                            </a>
                            
                            <?php 
                            $extensao = strtolower(pathinfo($curriculo['arquivo_nome'], PATHINFO_EXTENSION));
                            if (in_array($extensao, ['pdf', 'jpg', 'jpeg', 'png', 'gif'])): 
                            ?>
                                <a href="download.php?id=<?= $curriculo['id'] ?>&action=view" 
                                   class="btn btn-primary" target="_blank">
                                    👁️ Visualizar
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div style="color: #e74c3c; margin-top: 10px;">
                            ⚠️ Arquivo não encontrado no servidor
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="meta-info">
                    <h4>ℹ️ Informações do Sistema</h4>
                    <div class="meta-grid">
                        <div class="meta-item">
                            <strong>ID:</strong>
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
            
            <div class="actions">
                <a href="admin/admin.php" class="btn btn-secondary">
                    ← Voltar à Lista
                </a>
                <a href="admin/admin.php?acao=enviar_email&id=<?= $curriculo['id'] ?>" class="btn btn-primary"
                   onclick="return confirm('Deseja enviar email de confirmação para <?= htmlspecialchars($curriculo['email']) ?>?')">
                    📧 Enviar Email
                </a>
                <a href="admin/admin.php?acao=excluir&id=<?= $curriculo['id'] ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Tem certeza que deseja excluir este currículo? Esta ação não pode ser desfeita.')">
                    🗑️ Excluir
                </a>
            </div>
        </div>
    </div>
    
    <style>
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
    </style>
</body>
</html>