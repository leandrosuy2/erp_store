<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificação de autenticação (exemplo)
if (!isset($_SESSION['user_id'])) {
    header('Location: login/login.php');
    exit();
}
?>
