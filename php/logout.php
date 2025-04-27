<?php
session_start();

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    session_unset();       // Remove todas as variáveis de sessão
    session_destroy();     // Destroi a sessão
}

// Redireciona de qualquer forma para a página principal
header('Location: /artmetal/php/principal.html'); // Certifique-se que está indo pro .php, não .html
exit();

