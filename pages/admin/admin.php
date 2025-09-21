<?php
session_start();

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../../includes/config.php';
require_once '../../includes/email.php';

// Verificar se a conexão com o banco existe
if (!isset($conn)) {
    die('Erro: Conexão com banco de dados não encontrada.');
}

// Processar ações CRUD
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? '';
$mensagem = '';
$tipo_mensagem = '';

// ENVIAR EMAIL DE CONFIRMAÇÃO
if ($acao === 'enviar_email' && $id) {
    try {
        // Buscar dados do candidato
        $stmt = $conn->prepare("SELECT * FROM curriculos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($curriculo = $resultado->fetch_assoc()) {
            // Gerar protocolo único
            $protocolo = 'SESAP-' . date('Y') . '-' . str_pad($curriculo['id'], 6, '0', STR_PAD_LEFT);
            
            // Criar um corpo de email simplificado para a URL do Gmail (evitar erro 400)
            $corpoEmailSimples = "🏛️ SESAP-RN - Confirmação de Recebimento de Currículo

Prezado(a) " . $curriculo['nome'] . ",

✅ CURRÍCULO RECEBIDO COM SUCESSO!

Confirmamos o recebimento do seu currículo enviado para a SESAP-RN.

📋 DADOS DO CANDIDATO:
• Nome: " . $curriculo['nome'] . "
• E-mail: " . $curriculo['email'] . "
• Telefone: " . $curriculo['telefone'] . "
• Cargo Desejado: " . $curriculo['cargo_desejado'] . "
• Escolaridade: " . $curriculo['escolaridade'] . "
• Data de Envio: " . date('d/m/Y', strtotime($curriculo['data_envio'])) . " às " . date('H:i:s', strtotime($curriculo['data_envio'])) . "

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

📧 CONTATO:
• Site: www.sesap.rn.gov.br
• E-mail: rh@sesap.rn.gov.br

🏥 SESAP-RN - Secretaria de Estado da Saúde Pública do Rio Grande do Norte
© " . date('Y') . " - Todos os direitos reservados.";

            // Codificar o corpo do email para URL
            $corpoEmailEncoded = urlencode($corpoEmailSimples);
            $assunto = urlencode('Confirmação de Recebimento - Currículo SESAP-RN');
            $destinatario = urlencode($curriculo['email']);
            
            // Criar URL do Gmail
            $gmailUrl = "https://mail.google.com/mail/?view=cm&fs=1&to={$destinatario}&su={$assunto}&body={$corpoEmailEncoded}";
            
            // Usar a função enviarComprovante como backup
            $resultado = enviarComprovante($curriculo);
            
            // Redirecionar para página de envio com Gmail
            echo "<script>
                // Abrir Gmail automaticamente
                window.open('" . $gmailUrl . "', '_blank');
                
                // Redirecionar automaticamente
                setTimeout(function() {
                    window.location.href = 'admin.php?mensagem=" . urlencode("Email de confirmação processado para " . $curriculo['email']) . "&tipo=success';
                }, 1000);
            </script>";
            exit;
        } else {
            $mensagem = "Currículo não encontrado.";
            $tipo_mensagem = "error";
        }
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
        $tipo_mensagem = "error";
    }
}

// DELETE - Excluir currículo
if ($acao === 'excluir' && $id) {
    try {
        // Buscar informações do arquivo antes de excluir
        $stmt = $conn->prepare("SELECT arquivo_caminho FROM curriculos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($curriculo = $resultado->fetch_assoc()) {
            // Excluir arquivo físico
            if (file_exists($curriculo['arquivo_caminho'])) {
                unlink($curriculo['arquivo_caminho']);
            }
            
            // Excluir registro do banco
            $stmt = $conn->prepare("DELETE FROM curriculos WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $mensagem = "Currículo excluído com sucesso!";
                $tipo_mensagem = "success";
            } else {
                $mensagem = "Erro ao excluir currículo.";
                $tipo_mensagem = "error";
            }
        } else {
            $mensagem = "Currículo não encontrado.";
            $tipo_mensagem = "error";
        }
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
        $tipo_mensagem = "error";
    }
}

// READ - Buscar currículos com filtros
$busca = $_GET['busca'] ?? '';
$cargo_filtro = $_GET['cargo'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Construir query com filtros
$where_conditions = [];
$params = [];
$types = '';

if ($busca) {
    $where_conditions[] = "(nome LIKE ? OR email LIKE ? OR telefone LIKE ?)";
    $busca_param = "%$busca%";
    $params[] = $busca_param;
    $params[] = $busca_param;
    $params[] = $busca_param;
    $types .= 'sss';
}

if ($cargo_filtro) {
    $where_conditions[] = "cargo_desejado LIKE ?";
    $params[] = "%$cargo_filtro%";
    $types .= 's';
}

if ($data_inicio) {
    $where_conditions[] = "DATE(data_envio) >= ?";
    $params[] = $data_inicio;
    $types .= 's';
}

if ($data_fim) {
    $where_conditions[] = "DATE(data_envio) <= ?";
    $params[] = $data_fim;
    $types .= 's';
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Contar total de registros
$count_sql = "SELECT COUNT(*) as total FROM curriculos $where_clause";
$count_stmt = $conn->prepare($count_sql);
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_registros = $count_stmt->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Buscar currículos
$sql = "SELECT * FROM curriculos $where_clause ORDER BY data_envio DESC LIMIT ? OFFSET ?";
$params[] = $por_pagina;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$curriculos = $stmt->get_result();

// Buscar cargos únicos para filtro
$cargos_stmt = $conn->query("SELECT DISTINCT cargo_desejado FROM curriculos ORDER BY cargo_desejado");
$cargos = $cargos_stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Currículos - CRUD</title>
    <script>
        // Timer de logout automático após 10 minutos de inatividade
        let logoutTimer;
        let warningTimer;
        const TIMEOUT_DURATION = 10 * 60 * 1000; // 10 minutos em milissegundos
        const WARNING_DURATION = 2 * 60 * 1000; // 2 minutos antes do logout
        
        function resetTimer() {
            // Limpar timers existentes
            clearTimeout(logoutTimer);
            clearTimeout(warningTimer);
            
            // Remover aviso se existir
            const warningDiv = document.getElementById('logout-warning');
            if (warningDiv) {
                warningDiv.remove();
            }
            
            // Configurar aviso 2 minutos antes do logout
            warningTimer = setTimeout(showWarning, TIMEOUT_DURATION - WARNING_DURATION);
            
            // Configurar logout automático
            logoutTimer = setTimeout(autoLogout, TIMEOUT_DURATION);
        }
        
        function showWarning() {
            const warningDiv = document.createElement('div');
            warningDiv.id = 'logout-warning';
            warningDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #ff6b6b;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 10000;
                font-weight: bold;
                max-width: 300px;
            `;
            warningDiv.innerHTML = `
                ⚠️ <strong>Aviso de Logout</strong><br>
                Você será deslogado em 2 minutos por inatividade.<br>
                <button onclick="resetTimer()" style="margin-top: 10px; padding: 5px 10px; background: white; color: #ff6b6b; border: none; border-radius: 4px; cursor: pointer;">
                    Continuar Sessão
                </button>
            `;
            document.body.appendChild(warningDiv);
        }
        
        function autoLogout() {
            alert('Sua sessão expirou por inatividade. Você será redirecionado para a página de login.');
            window.location.href = 'logout.php';
        }
        
        // Eventos que resetam o timer
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar listeners para todos os eventos de atividade
            events.forEach(event => {
                document.addEventListener(event, resetTimer, true);
            });
            
            // Iniciar o timer
            resetTimer();
        });
    </script>
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
            max-width: 1200px;
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
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: bold;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .filters h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        
        .form-group input, .form-group select {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background: #e67e22;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 2em;
            margin-bottom: 5px;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #34495e;
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
            gap: 10px;
        }
        
        .pagination a, .pagination span {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #3498db;
            color: white;
        }
        
        .pagination .current {
            background: #3498db;
            color: white;
        }
        
        .no-data {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 1.2em;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            
            .content {
                padding: 15px;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>🗂️ Sistema CRUD de Currículos</h1>
                    <p>Gerencie todos os currículos enviados - Create, Read, Update, Delete</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin-bottom: 10px; opacity: 0.8;">
                        👤 Usuário: <strong><?= htmlspecialchars($_SESSION['admin_usuario']) ?></strong>
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="logout.php" 
                           style="background: rgba(255,255,255,0.2); color: white; padding: 8px 16px; 
                                  border-radius: 5px; text-decoration: none; font-size: 0.9em;
                                  transition: background 0.3s;"
                           onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                           onmouseout="this.style.background='rgba(255,255,255,0.2)'"
                           onclick="return confirm('Deseja realmente sair do painel administrativo?')">
                            🚪 Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content">
            <?php 
            // Verificar mensagem via GET (para redirecionamentos)
            if (isset($_GET['mensagem'])) {
                $mensagem = $_GET['mensagem'];
                $tipo_mensagem = $_GET['tipo'] ?? 'success';
            }
            
            if ($mensagem): ?>
                <div class="alert <?= $tipo_mensagem ?>">
                    <?= htmlspecialchars($mensagem) ?>
                </div>
            <?php endif; ?>
            
            <!-- Estatísticas -->
            <div class="stats">
                <div class="stat-card">
                    <h3><?= $total_registros ?></h3>
                    <p>Total de Currículos</p>
                </div>
                <div class="stat-card">
                    <h3><?= $total_paginas ?></h3>
                    <p>Páginas</p>
                </div>
                <div class="stat-card">
                    <h3><?= count($cargos) ?></h3>
                    <p>Cargos Diferentes</p>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="filters">
                <h3>🔍 Filtros de Busca</h3>
                <form method="GET">
                    <div class="filter-row">
                        <div class="form-group">
                            <label>Buscar por Nome/Email/Telefone:</label>
                            <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Digite para buscar...">
                        </div>
                        
                        <div class="form-group">
                            <label>Cargo:</label>
                            <select name="cargo">
                                <option value="">Todos os cargos</option>
                                <?php foreach ($cargos as $cargo): ?>
                                    <option value="<?= htmlspecialchars($cargo['cargo_desejado']) ?>" 
                                            <?= $cargo_filtro === $cargo['cargo_desejado'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cargo['cargo_desejado']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Data Início:</label>
                            <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Data Fim:</label>
                            <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                        <a href="admin.php" class="btn btn-warning">🔄 Limpar Filtros</a>
                    </div>
                </form>
            </div>
            
            <!-- Tabela de Currículos -->
            <div class="table-container">
                <?php if ($curriculos->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Cargo Desejado</th>
                                <th>Data de Envio</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($curriculo = $curriculos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $curriculo['id'] ?></td>
                                    <td><?= htmlspecialchars($curriculo['nome']) ?></td>
                                    <td><?= htmlspecialchars($curriculo['email']) ?></td>
                                    <td><?= htmlspecialchars($curriculo['telefone']) ?></td>
                                    <td><?= htmlspecialchars($curriculo['cargo_desejado']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($curriculo['data_envio'])) ?> às <?= date('H:i', strtotime($curriculo['data_envio'])) ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="../visualizar.php?id=<?= $curriculo['id'] ?>" class="btn btn-primary" title="Visualizar">
                                                👁️ Ver
                                            </a>
                                            <a href="admin.php?acao=enviar_email&id=<?= $curriculo['id'] ?>" class="btn btn-success" title="Enviar Email de Confirmação"
                                               onclick="return confirm('Deseja enviar email de confirmação para <?= htmlspecialchars($curriculo['email']) ?>?')">
                                                📧 Email
                                            </a>
                                            <a href="admin.php?acao=excluir&id=<?= $curriculo['id'] ?>" 
                                               class="btn btn-danger" title="Excluir"
                                               onclick="return confirm('Tem certeza que deseja excluir este currículo?')">
                                                🗑️ Excluir
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <h3>📭 Nenhum currículo encontrado</h3>
                        <p>Não há currículos que correspondam aos filtros aplicados.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina - 1])) ?>">
                            ← Anterior
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++): ?>
                        <?php if ($i == $pagina): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina + 1])) ?>">
                            Próxima →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>