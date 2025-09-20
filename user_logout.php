<?php
session_start();

// Limpar todas as variáveis de sessão do usuário
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['user_name']);
unset($_SESSION['user_authenticated']);

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header('Location: user_login.php');
exit;
?>