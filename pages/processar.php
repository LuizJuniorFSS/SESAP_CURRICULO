<?php
require_once '../includes/config.php';
require_once '../includes/email.php';

// Iniciar sessão para mensagens
session_start();

// Verificar se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/index.html');
    exit;
}

// Função sanitizeInput já está definida em config.php

// Função para validar telefone brasileiro
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^\d{10,11}$/', $phone);
}

// Array para armazenar erros
$errors = [];
$data = [];

// Validar campos obrigatórios
$requiredFields = ['nome', 'email', 'telefone', 'cargo_desejado', 'escolaridade'];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "O campo {$field} é obrigatório.";
    } else {
        $data[$field] = sanitizeInput($_POST[$field]);
    }
}

// Validações específicas
if (!empty($data['nome']) && strlen($data['nome']) < 2) {
    $errors[] = "Nome deve ter pelo menos 2 caracteres.";
}

if (!empty($data['email']) && !isValidEmail($data['email'])) {
    $errors[] = "E-mail inválido.";
}

if (!empty($data['telefone']) && !validatePhone($data['telefone'])) {
    $errors[] = "Telefone inválido.";
}

if (!empty($data['cargo_desejado']) && strlen($data['cargo_desejado']) < 2) {
    $errors[] = "Cargo desejado deve ter pelo menos 2 caracteres.";
}

// Validar escolaridade
$escolaridadeValida = [
    'Ensino Fundamental Incompleto',
    'Ensino Fundamental Completo',
    'Ensino Médio Incompleto',
    'Ensino Médio Completo',
    'Ensino Superior Incompleto',
    'Ensino Superior Completo',
    'Pós-graduação',
    'Mestrado',
    'Doutorado'
];

if (!empty($data['escolaridade']) && !in_array($data['escolaridade'], $escolaridadeValida)) {
    $errors[] = "Escolaridade inválida.";
}

// Campo observações (opcional)
$data['observacoes'] = isset($_POST['observacoes']) ? sanitizeInput($_POST['observacoes']) : '';

// Validar arquivo
if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = "Erro no upload do arquivo.";
} else {
    $arquivo = $_FILES['arquivo'];
    
    // Verificar tamanho do arquivo (1MB máximo)
    if ($arquivo['size'] > MAX_FILE_SIZE) {
        $errors[] = "Arquivo muito grande. Tamanho máximo: 1MB.";
    }
    
    // Verificar extensão do arquivo
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, ALLOWED_EXTENSIONS)) {
        $errors[] = "Formato de arquivo não permitido. Use .doc, .docx ou .pdf.";
    }
    
    // Verificar tipo MIME
    $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $arquivo['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimes)) {
        $errors[] = "Tipo de arquivo não permitido.";
    }
}

// Se houver erros, redirecionar de volta com mensagens
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: ../public/index.html?error=1');
    exit;
}

// Processar upload do arquivo - Armazenar no banco como BLOB
$uploadDir = UPLOAD_DIR;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Função para limpar nome do arquivo
function limparNomeArquivo($nome) {
    // Remover acentos e caracteres especiais
    $nome = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
    // Remover caracteres não alfanuméricos exceto espaços
    $nome = preg_replace('/[^a-zA-Z0-9\s]/', '', $nome);
    // Substituir espaços por underscores
    $nome = str_replace(' ', '_', $nome);
    // Remover múltiplos underscores consecutivos
    $nome = preg_replace('/_+/', '_', $nome);
    // Remover underscores do início e fim
    $nome = trim($nome, '_');
    return $nome;
}

// Gerar nome do arquivo com o nome da pessoa
$nomeCompleto = limparNomeArquivo($data['nome']);
$nomeArquivo = 'Curriculo_' . $nomeCompleto . '.' . $extensao;

// Ler o conteúdo do arquivo para armazenar como BLOB
$arquivoConteudo = file_get_contents($arquivo['tmp_name']);
if ($arquivoConteudo === false) {
    $_SESSION['errors'] = ['Erro ao ler o arquivo.'];
    header('Location: ../public/index.html?error=1');
    exit;
}

// Obter informações do arquivo
$arquivoTamanho = $arquivo['size'];
$arquivoMimeType = $mimeType;

// Criar backup físico opcional (para compatibilidade)
$caminhoArquivo = $uploadDir . $nomeArquivo;
$contador = 1;
while (file_exists($caminhoArquivo)) {
    $nomeArquivo = 'Curriculo_' . $nomeCompleto . '_' . $contador . '.' . $extensao;
    $caminhoArquivo = $uploadDir . $nomeArquivo;
    $contador++;
}

// Salvar cópia física (opcional - pode ser removido se quiser apenas BLOB)
if (!move_uploaded_file($arquivo['tmp_name'], $caminhoArquivo)) {
    // Não é erro crítico, pois o arquivo será salvo no banco
    $caminhoArquivo = 'BLOB_STORAGE'; // Indicador de que está no banco
}

// Salvar no banco de dados com BLOB
try {
    $pdo = getConnection();
    
    $sql = "INSERT INTO curriculos (nome, email, telefone, cargo_desejado, escolaridade, observacoes, arquivo_nome, arquivo_caminho, arquivo_conteudo, arquivo_tamanho, arquivo_mime_type, ip_envio) 
            VALUES (:nome, :email, :telefone, :cargo_desejado, :escolaridade, :observacoes, :arquivo_nome, :arquivo_caminho, :arquivo_conteudo, :arquivo_tamanho, :arquivo_mime_type, :ip_envio)";
    
    $stmt = $pdo->prepare($sql);
    
    $params = [
        ':nome' => $data['nome'],
        ':email' => $data['email'],
        ':telefone' => $data['telefone'],
        ':cargo_desejado' => $data['cargo_desejado'],
        ':escolaridade' => $data['escolaridade'],
        ':observacoes' => $data['observacoes'],
        ':arquivo_nome' => $arquivo['name'],
        ':arquivo_caminho' => $caminhoArquivo,
        ':arquivo_conteudo' => $arquivoConteudo,
        ':arquivo_tamanho' => $arquivoTamanho,
        ':arquivo_mime_type' => $arquivoMimeType,
        ':ip_envio' => getUserIP()
    ];
    
    $stmt->execute($params);
    $curriculoId = $pdo->lastInsertId();
    
    // Para envio de email, usar arquivo temporário se necessário
    $arquivoParaEmail = $caminhoArquivo;
    if ($caminhoArquivo === 'BLOB_STORAGE') {
        // Criar arquivo temporário para envio de email
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'temp_' . $nomeArquivo;
        file_put_contents($tempFile, $arquivoConteudo);
        $arquivoParaEmail = $tempFile;
    }
    
    // Enviar email para a empresa
    $emailEnviado = enviarEmail($data, $arquivoParaEmail, $arquivo['name']);
    
    // Limpar arquivo temporário se foi criado
    if ($caminhoArquivo === 'BLOB_STORAGE' && file_exists($tempFile)) {
        unlink($tempFile);
    }
    
    // Preparar dados para a página de sucesso
    $_SESSION['curriculo_id'] = $curriculoId;
    $_SESSION['success'] = 'Currículo cadastrado com sucesso!';
    $_SESSION['dados_candidato'] = $data;
    $_SESSION['user_email_temp'] = $data['email']; // Email para acesso posterior
    
    // Salvar IP na sessão para exibir na página de sucesso
    $_SESSION['ip_envio'] = getUserIP();
    
    // Redirecionar para página de sucesso
    header('Location: sucesso.php');
    exit;
    
} catch (PDOException $e) {
    // Log do erro (em produção, usar um sistema de log apropriado)
    error_log('Erro no banco de dados: ' . $e->getMessage());
    
    // Remover arquivo se houve erro no banco
    if (file_exists($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }
    
    $_SESSION['errors'] = ['Erro interno do servidor. Tente novamente mais tarde.'];
    header('Location: ../public/index.html?error=1');
    exit;
}
?>