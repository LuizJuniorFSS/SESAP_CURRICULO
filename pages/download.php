<?php
require_once '../includes/config.php';

// Verificar se foi fornecido um ID de currículo
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    die('Arquivo não encontrado.');
}

$curriculo_id = (int)$_GET['id'];

try {
    // Buscar informações do currículo no banco (incluindo BLOB)
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT arquivo_nome, arquivo_caminho, arquivo_conteudo, arquivo_tamanho, arquivo_mime_type FROM curriculos WHERE id = ?");
    $stmt->execute([$curriculo_id]);
    $curriculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$curriculo) {
        http_response_code(404);
        die('Currículo não encontrado.');
    }
    
    $arquivo_nome = $curriculo['arquivo_nome'];
    $arquivo_caminho = $curriculo['arquivo_caminho'];
    $arquivo_conteudo = $curriculo['arquivo_conteudo'];
    $arquivo_tamanho = $curriculo['arquivo_tamanho'];
    $arquivo_mime_type = $curriculo['arquivo_mime_type'];
    
    // Priorizar BLOB se disponível, senão usar arquivo físico
    $usar_blob = !empty($arquivo_conteudo);
    
    if (!$usar_blob) {
        // Fallback para arquivo físico (compatibilidade com registros antigos)
        if (!file_exists($arquivo_caminho)) {
            http_response_code(404);
            die('Arquivo não encontrado no servidor.');
        }
        $arquivo_tamanho = filesize($arquivo_caminho);
        
        // Determinar tipo MIME se não estiver no banco
        if (empty($arquivo_mime_type)) {
            $extensao = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION));
            $mime_types = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $arquivo_mime_type = isset($mime_types[$extensao]) ? $mime_types[$extensao] : 'application/octet-stream';
        }
    }
    
    // Verificar se deve forçar download ou permitir visualização
    $action = isset($_GET['action']) ? $_GET['action'] : 'view';
    $extensao = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION));
    
    // Configurar headers
    header('Content-Type: ' . $arquivo_mime_type);
    header('Content-Length: ' . $arquivo_tamanho);
    
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
    
    if ($usar_blob) {
        // Servir arquivo do BLOB
        echo $arquivo_conteudo;
    } else {
        // Servir arquivo físico (fallback)
        readfile($arquivo_caminho);
    }
    
    exit;
    
} catch (Exception $e) {
    error_log('Erro ao servir arquivo: ' . $e->getMessage());
    http_response_code(500);
    die('Erro interno do servidor.');
}
?>