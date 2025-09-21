<?php
// Configurar fuso horário brasileiro
date_default_timezone_set('America/Sao_Paulo');

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sesap_curriculo');
define('DB_USER', 'root'); // Usuário padrão do XAMPP
define('DB_PASS', ''); // Senha vazia padrão do XAMPP
define('DB_CHARSET', 'utf8mb4');

// Configurações de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'seu_email@gmail.com'); // Altere para seu email
define('SMTP_PASSWORD', 'sua_senha_app'); // Altere para sua senha de app
define('EMAIL_FROM', 'seu_email@gmail.com'); // Email remetente
define('EMAIL_TO', 'rh@sesap.rn.gov.br'); // Email destinatário

// Configurações de upload
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 1048576); // 1MB em bytes
define('ALLOWED_EXTENSIONS', ['doc', 'docx', 'pdf']);

// Função para conectar ao banco de dados (PDO)
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }
}

// Conexão MySQLi para compatibilidade com páginas administrativas
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset(DB_CHARSET);
    
    if ($conn->connect_error) {
        die('Erro na conexão MySQLi: ' . $conn->connect_error);
    }
} catch (Exception $e) {
    die('Erro ao conectar com MySQLi: ' . $e->getMessage());
}

// Função para obter IP do usuário
function getUserIP() {
    // Para desenvolvimento local, obter o gateway padrão dinamicamente
    if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
        return getDefaultGateway();
    }
    
    // Para produção, usar a lógica original
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Função para obter o gateway padrão da máquina
function getDefaultGateway() {
    $gateway = '';
    
    // Para Windows
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $output = shell_exec('ipconfig | findstr /i "Gateway"');
        if ($output) {
            // Extrair o IP do gateway da saída do comando
            preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $output, $matches);
            if (!empty($matches[1])) {
                $gateway = $matches[1];
            }
        }
    } 
    // Para Linux/Unix/Mac
    else {
        $output = shell_exec('ip route | grep default');
        if ($output) {
            preg_match('/default via (\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $output, $matches);
            if (!empty($matches[1])) {
                $gateway = $matches[1];
            }
        }
        
        // Fallback para sistemas Unix/Mac
        if (empty($gateway)) {
            $output = shell_exec('route -n get default | grep gateway');
            if ($output) {
                preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $output, $matches);
                if (!empty($matches[1])) {
                    $gateway = $matches[1];
                }
            }
        }
    }
    
    // Se não conseguir obter o gateway, retornar um IP padrão
    return !empty($gateway) ? $gateway : '192.168.1.1';
}

// Função para validar extensão de arquivo
function isValidFileExtension($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_EXTENSIONS);
}

// Função para validar tamanho do arquivo
function isValidFileSize($filesize) {
    return $filesize <= MAX_FILE_SIZE;
}

// Função para gerar nome único para arquivo
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

// Função para sanitizar entrada
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para validar email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Função para validar CPF
function isValidCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Calcula os dígitos verificadores
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    
    return true;
}
?>