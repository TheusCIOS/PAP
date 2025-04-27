<?php
session_start();
require_once('DatabaseConfig.php');  // Conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $conn = DatabaseConfig::getConnection();

    // Consulta para verificar o usuário
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Depuração: verificar se o usuário foi encontrado
    if ($result->num_rows > 0) {
        echo "Usuário encontrado!<br>"; // Depuração
        $usuario = $result->fetch_assoc();

        // Depuração: verificar a senha
        if (password_verify($senha, $usuario['senha'])) {
            echo "Senha correta!<br>"; // Depuração

            // Verifica se a conta foi ativada
            if ($usuario['status'] == 1) {
                echo "Conta ativada!<br>"; // Depuração
                // Conta ativada, inicia sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['tipo'] = $usuario['tipo']; // Salva o tipo de usuário na sessão

                // Depuração: verificar tipo de usuário
                echo "Tipo de usuário: " . $usuario['tipo'] . "<br>"; // Depuração

                // Verifica o tipo de usuário
                if ($usuario['tipo'] == 'admin') {
                    // Se for administrador, redireciona para a página de admin
                    header("Location: /artmetal/php/adm.php");
                } else {
                    // Se for um usuário comum, redireciona para a página principal
                    header("Location: /artmetal/php/principal.html");
                }
                exit; // Garante que o código não continue executando após o redirecionamento
            } else {
                echo "Conta não ativada.<br>"; // Depuração
            }
        } else {
            echo "Senha incorreta.<br>"; // Depuração
        }
    } else {
        echo "Usuário não encontrado.<br>"; // Depuração
    }

    $stmt->close();
    $conn->close();
}
?>
