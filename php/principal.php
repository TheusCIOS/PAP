<?php
session_start();
require_once('DatabaseConfig.php');  // Conexão com o banco de dados

// Verifica se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"]) && isset($_POST["senha"])) {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $conn = DatabaseConfig::getConnection();

    // Consulta para verificar o usuário
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o usuário foi encontrado
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            // Verifica se a conta foi ativada
            if ($usuario['status'] == 1) {
                // Inicia a sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['tipo'] = $usuario['tipo'];

                // Redireciona para a própria página para atualizar o estado
                header("Location: /artmetal/php/principal.php");
                exit();
            } else {
                $erro = "Conta não ativada.";
            }
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <!-- Se não estiver logado, exibe o formulário de login -->
    <?php if (!isset($_SESSION['usuario_id'])): ?>
        <div class="login-form">
            <h2>Login</h2>
            <form method="POST" action="principal.php">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>
                
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>

                <button type="submit">Entrar</button>
            </form>
            <?php if (isset($erro)): ?>
                <p style="color: red;"><?php echo $erro; ?></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Conteúdo Principal (após o login) -->
        <header>
            <button onclick="showSection('agendar-reuniao')">Agendar Reunião</button>
            <button onclick="showSection('ver-projetos')">Ver Projetos</button>
            <button onclick="showSection('candidatar-soldador')">Candidatar-se para Soldador</button>

            <div class="user-actions">
                <img src="usuario.png" alt="Foto do Usuário" onclick="toggleAccountInfo()" style="width: 50px; height: 50px; border-radius: 50%; cursor: pointer;">
            </div>
        </header>

        <div id="accountInfo" style="display: none;">
            <h3>Informações da Conta</h3>
            <p><strong>Nome:</strong> <?php echo $_SESSION['nome']; ?></p>
            <p><strong>E-mail:</strong> <?php echo $_SESSION['email']; ?></p>
            <form method="POST" action="logout.php">
                <button type="submit">Desconectar</button>
            </form>
        </div>

        <main>
            <!-- Seções da página -->
            <section class="agendar-reuniao" style="display: none;">
                <h2>Agendar Reunião</h2>
                <form method="POST" action="agendar_reuniao.php">
                    <label for="data_reuniao">Data e Hora da Reunião:</label>
                    <input type="datetime-local" name="data_reuniao" id="data_reuniao" required>
                    <button type="submit">Agendar Reunião</button>
                </form>
            </section>

            <section class="ver-projetos" style="display: none;">
                <h2>Ver Projetos</h2>
                <div class="video-container">
                    <!-- Aqui você pode mostrar os vídeos ou projetos -->
                </div>
            </section>

            <section class="candidatar-soldador" style="display: none;">
                <h2>Candidatar-se para Soldador</h2>
                <form method="POST" action="candidatura_soldador.php" enctype="multipart/form-data">
                    <label for="nome_candidato">Nome Completo:</label>
                    <input type="text" id="nome_candidato" name="nome_candidato" required placeholder="Digite seu nome completo">

                    <label for="email_candidato">E-mail:</label>
                    <input type="email" id="email_candidato" name="email_candidato" required placeholder="Digite seu e-mail">

                    <label for="telefone_candidato">Telefone:</label>
                    <input type="tel" id="telefone_candidato" name="telefone_candidato" required placeholder="Digite seu telefone">

                    <label for="experiencia">Experiência em Soldagem:</label>
                    <textarea id="experiencia" name="experiencia" required placeholder="Descreva sua experiência em soldagem"></textarea>

                    <label for="formacao">Formação Acadêmica:</label>
                    <input type="text" id="formacao" name="formacao" required placeholder="Informe sua formação">

                    <label for="curriculo">Anexar Currículo (PDF):</label>
                    <input type="file" id="curriculo" name="curriculo" accept=".pdf" required>

                    <button type="submit">Enviar Candidatura</button>
                </form>
            </section>
        </main>
    <?php endif; ?>

</body>
</html>
