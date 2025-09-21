<?php
// Configurar fuso horário brasileiro
date_default_timezone_set('America/Sao_Paulo');

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sesap_curriculo');
define('DB_USER', 'root'); // Altere para seu usuário do banco
define('DB_PASS', ''); // Altere para sua senha do banco
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
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Função para validar email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Função para sanitizar dados
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Configurações de segurança
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Headers de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Altere para 1 em HTTPS

// Iniciar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>