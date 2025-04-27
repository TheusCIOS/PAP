<?php
session_start();
require_once('DatabaseConfig.php');  // Conexão com o banco de dados

// Verifica se o token foi passado via GET
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Conectar ao banco de dados
    $conn = DatabaseConfig::getConnection();

    // Verifica se o token existe na tabela de usuários
    $query = "SELECT * FROM usuarios WHERE token = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql = "UPDATE usuarios SET status = 1 WHERE token = ?";
        $stmt_update = $conn->prepare($sql);
        if (!$stmt_update) {
            die("Erro na preparação da consulta: " . $conn->error);
        }
        
        
        // Verifica se a preparação da consulta foi bem-sucedida
        if (!$stmt_update) {
            die("Erro na preparação da consulta de atualização: " . $conn->error);
        }

        $stmt_update->bind_param("s", $token);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            // Conta ativada com sucesso
            $_SESSION['mensagem'] = "Sua conta foi ativada com sucesso!";
            header("Location: /artmetal/php/principal.html"); // Redireciona para a página de login
            exit;
        } else {
            echo "A conta já está ativada ou o token é inválido.";
        }
        
        // Fechar a consulta de atualização
        $stmt_update->close();
    } else {
        echo "Token inválido. Por favor, verifique o link de ativação.";
    }

    // Fechar a consulta inicial e a conexão
    $stmt->close();
    $conn->close();
} else {
    echo "Token não fornecido. Por favor, use o link correto para ativação.";
}
?>