<?php
date_default_timezone_set('America/Sao_Paulo');

define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'sesap_curriculo');
define('DB_USER', getenv('DB_USER') ?: 'sesap');
define('DB_PASS', getenv('DB_PASS') ?: 'sesap123');
define('DB_CHARSET', 'utf8mb4');

define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'seu_email@gmail.com');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'sua_senha_app');
define('EMAIL_FROM', getenv('EMAIL_FROM') ?: 'seu_email@gmail.com');
define('EMAIL_TO', getenv('EMAIL_TO') ?: 'rh@sesap.rn.gov.br');

define('UPLOAD_DIR', getenv('UPLOAD_DIR') ?: 'uploads/');
define('MAX_FILE_SIZE', getenv('MAX_FILE_SIZE') ? (int)getenv('MAX_FILE_SIZE') : 1048576);
define('ALLOWED_EXTENSIONS', ['doc', 'docx', 'pdf']);

function getConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }
}

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset(DB_CHARSET);
    if ($conn->connect_error) {
        die('Erro na conexão MySQLi: ' . $conn->connect_error);
    }
} catch (Exception $e) {
    die('Erro ao conectar com MySQLi: ' . $e->getMessage());
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>