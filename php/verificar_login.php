<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    echo 'true';  // Retorna 'true' se o usuário estiver logado
} else {
    echo 'false';  // Retorna 'false' se o usuário não estiver logado
}
?>
