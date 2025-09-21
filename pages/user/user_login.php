<?php
session_start();

// Se já estiver logado, redirecionar para o dashboard
if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] === true) {
    header('Location: user_dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

// Verificar se há mensagem de logout
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success_message = 'Logout realizado com sucesso!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error_message = 'Por favor, digite seu email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Por favor, digite um email válido.';
    } else {
        // Conectar ao banco de dados
        require_once '../../includes/config.php';
        
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("SELECT id, nome, email FROM curriculos WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Email encontrado, fazer login
                $_SESSION['user_authenticated'] = true;
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_id'] = $user['id'];
                
                header('Location: user_dashboard.php');
                exit;
            } else {
                $error_message = 'Email não encontrado. Certifique-se de que já enviou seu currículo.';
            }
        } catch (PDOException $e) {
            $error_message = 'Erro interno. Tente novamente mais tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Candidato - SESAP</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 450px;
            width: 100%;
        }
        
        .logo {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #2c5aa0;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
        }
        
        input[type="email"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="email"]:focus {
            outline: none;
            border-color: #2c5aa0;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: #2c5aa0;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #1e3a8a;
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }
        
        .info-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
            text-align: left;
        }
        
        .info-title {
            color: #2c5aa0;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .info-text {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link:hover {
            color: #2c5aa0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🏥</div>
        <h1>SESAP</h1>
        <p class="subtitle">Área do Candidato</p>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email usado no cadastro:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Digite seu email"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required
                >
            </div>
            
            <button type="submit" class="btn">
                🔐 Acessar Minha Área
            </button>
        </form>
        
        <div class="info-box">
            <div class="info-title">ℹ️ Como funciona?</div>
            <div class="info-text">
                Digite o mesmo email que você usou para enviar seu currículo. 
                Você terá acesso à sua área pessoal onde pode acompanhar o status 
                da sua candidatura e baixar uma cópia do seu currículo.
            </div>
        </div>
        
        <a href="../../public/index.html" class="back-link">
            ← Voltar ao início
        </a>
    </div>
</body>
</html>