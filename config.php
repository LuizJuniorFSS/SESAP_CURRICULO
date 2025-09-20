<?php
// Configurar fuso horário brasileiro
date_default_timezone_set('America/Sao_Paulo');

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sesap_curriculo');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'seu_email@gmail.com'); // Altere para seu email
define('SMTP_PASSWORD', 'sua_senha_app'); // Altere para sua senha de app
define('EMAIL_FROM', 'seu_email@gmail.com'); // Email remetente
define('EMAIL_TO', 'rh@empresa.com'); // Email destinatário

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
    // Tentar obter o Gateway Padrão
    $gateway = getDefaultGateway();
    if ($gateway) {
        return $gateway;
    }
    
    // Fallback para IP do cliente se não conseguir obter o gateway
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getDefaultGateway() {
    try {
        // Detectar sistema operacional
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - usar ipconfig
            $output = shell_exec('ipconfig | findstr "Gateway"');
            if ($output) {
                // Extrair IP do gateway da saída
                preg_match('/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/', $output, $matches);
                if (isset($matches[1]) && $matches[1] !== '0.0.0.0') {
                    return $matches[1];
                }
            }
        } else {
            // Linux/Unix - usar route
            $output = shell_exec('route -n | grep "^0.0.0.0" | awk "{print \$2}"');
            if ($output) {
                $gateway = trim($output);
                if (filter_var($gateway, FILTER_VALIDATE_IP)) {
                    return $gateway;
                }
            }
        }
    } catch (Exception $e) {
        // Em caso de erro, retornar null para usar fallback
        error_log('Erro ao obter gateway: ' . $e->getMessage());
    }
    
    return null;
}
?>

