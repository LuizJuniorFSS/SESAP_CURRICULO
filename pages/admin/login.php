<?php
session_start();

// Se já estiver logado, redirecionar para admin
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header('Location: admin.php');
    exit;
}

$erro = '';
$mensagem = '';

// Verificar se há mensagem de logout
if (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    // Credenciais padrão
    if ($usuario === 'SESAP' && $senha === 'admin123') {
        $_SESSION['admin_logado'] = true;
        $_SESSION['admin_usuario'] = $usuario;
        $_SESSION['admin_login_time'] = time();
        
        header('Location: admin.php');
        exit;
    } else {
        $erro = 'Usuário ou senha incorretos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo SESAP</title>
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
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .logo p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .erro {
            background: #fee;
            color: #c33;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
            border: 1px solid #fcc;
        }
        
        .sucesso {
            background: #efe;
            color: #3c3;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
            border: 1px solid #cfc;
        }
        
        .credenciais {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #666;
        }
        
        .credenciais strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🏛️ SESAP-RN</h1>
            <p>Painel Administrativo</p>
        </div>
        
        <?php if ($mensagem): ?>
            <div class="sucesso">
                ✅ <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="erro">
                ⚠️ <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="usuario">👤 Usuário:</label>
                <input type="text" id="usuario" name="usuario" required 
                       value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" 
                       placeholder="Digite seu usuário">
            </div>
            
            <div class="form-group">
                <label for="senha">🔒 Senha:</label>
                <input type="password" id="senha" name="senha" required 
                       placeholder="Digite sua senha">
            </div>
            
            <button type="submit" class="btn-login">
                🚀 Entrar no Painel
            </button>
        </form>
        
        
    </div>
</body>
</html>