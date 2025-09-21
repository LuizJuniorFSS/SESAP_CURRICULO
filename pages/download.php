<?php
require_once '../includes/config.php';

// Verificar se foi fornecido um ID de currículo
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    die('Arquivo não encontrado.');
}

$curriculo_id = (int)$_GET['id'];

try {
    // Buscar informações do currículo no banco
    $stmt = $conn->prepare("SELECT arquivo_nome, arquivo_caminho FROM curriculos WHERE id = ?");
    $stmt->bind_param("i", $curriculo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        die('Currículo não encontrado.');
    }
    
    $curriculo = $result->fetch_assoc();
    $arquivo_caminho = $curriculo['arquivo_caminho'];
    $arquivo_nome = $curriculo['arquivo_nome'];
    
    // Verificar se o arquivo existe fisicamente
    if (!file_exists($arquivo_caminho)) {
        http_response_code(404);
        die('Arquivo não encontrado no servidor.');
    }
    
    // Determinar o tipo MIME
    $extensao = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION));
    $mime_types = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    $content_type = isset($mime_types[$extensao]) ? $mime_types[$extensao] : 'application/octet-stream';
    
    // Verificar se deve forçar download ou permitir visualização
    $action = isset($_GET['action']) ? $_GET['action'] : 'view';
    
    // Configurar headers
    header('Content-Type: ' . $content_type);
    header('Content-Length: ' . filesize($arquivo_caminho));
    
    if ($action === 'download' || $extensao !== 'pdf') {
        // Forçar download para arquivos DOC/DOCX ou quando solicitado
        header('Content-Disposition: attachment; filename="' . $arquivo_nome . '"');
    } else {
        // Permitir visualização inline para PDFs
        header('Content-Disposition: inline; filename="' . $arquivo_nome . '"');
    }
    
    // Adicionar headers de segurança
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    
    // Limpar buffer de saída e enviar arquivo
    ob_clean();
    flush();
    readfile($arquivo_caminho);
    exit;
    
} catch (Exception $e) {
    error_log('Erro ao servir arquivo: ' . $e->getMessage());
    http_response_code(500);
    die('Erro interno do servidor.');
}
?>