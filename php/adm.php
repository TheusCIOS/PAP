<?php
session_start();
require_once 'DatabaseConfig.php'; // Inclui a configuração da conexão com o banco

// Obtém a conexão MySQLi
$conn = DatabaseConfig::getConnection();

// Função para excluir usuário
if (isset($_GET['delete_user_id'])) {
    $userId = $_GET['delete_user_id'];
    
    // Verifica se o usuário a ser excluído é o administrador (supondo que o admin tenha o tipo "admin")
    $checkAdminQuery = "SELECT tipo FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($checkAdminQuery);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Impede a exclusão do administrador
    if ($usuario['tipo'] == 'admin') {
        echo "<script>alert('Você não pode excluir o administrador!'); window.location.href='adm.php';</script>";
        exit;
    } else {
        // Se não for admin, pode excluir
        $deleteQuery = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='adm.php';</script>";
        exit;
    }
}

// Função para excluir reunião (agendamento)
if (isset($_GET['delete_meeting_id'])) {
    $meetingId = $_GET['delete_meeting_id'];

    // Prepara a consulta para excluir a reunião
    $deleteMeetingQuery = "DELETE FROM reunioes WHERE id = ?";
    $stmt = $conn->prepare($deleteMeetingQuery);
    $stmt->bind_param('i', $meetingId);
    if ($stmt->execute()) {
        echo "<script>alert('Reunião excluída com sucesso!'); window.location.href='adm.php';</script>";
        exit;
    } else {
        echo "<script>alert('Erro ao excluir a reunião!'); window.location.href='adm.php';</script>";
        exit;
    }
}

// Função para excluir candidatura
if (isset($_GET['delete_application_id'])) {
    $applicationId = $_GET['delete_application_id'];

    // Prepara a consulta para excluir a candidatura
    $deleteApplicationQuery = "DELETE FROM candidaturas WHERE id = ?";
    $stmt = $conn->prepare($deleteApplicationQuery);
    $stmt->bind_param('i', $applicationId);
    if ($stmt->execute()) {
        echo "<script>alert('Candidatura excluída com sucesso!'); window.location.href='adm.php';</script>";
        exit;
    } else {
        echo "<script>alert('Erro ao excluir a candidatura!'); window.location.href='adm.php';</script>";
        exit;
    }
}

// Consulta para pegar os usuários, excluindo os do tipo "admin"
$usuariosQuery = "SELECT * FROM usuarios WHERE tipo != 'admin'"; // Filtrando o tipo "admin"
$usuariosResult = $conn->query($usuariosQuery);

// Consulta para pegar as reuniões (tabela "reunioes")
$reunioesQuery = "SELECT * FROM reunioes";
$reunioesResult = $conn->query($reunioesQuery);

// Consulta para pegar as candidaturas
$candidaturasQuery = "SELECT * FROM candidaturas";
$candidaturasResult = $conn->query($candidaturasQuery);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
    <link rel="stylesheet" href="adm.css">
</head>
<body>

    <header>
        <h1>Painel de Administração</h1>
    </header>

    <nav>
        <a href="/artmetal/php/logout.php">Sair</a>
    </nav>

    <div class="container">
    <h2>Bem-vindo, <?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Usuário'; ?>!</h2>

        <p>Você está logado como administrador. Abaixo estão as opções para gerenciar os usuários, reuniões e candidaturas.</p>

        <!-- Lista de Usuários -->
        <div class="user-list">
            <h3>Gerenciar Usuários</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $usuariosResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nome']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td><?php echo $usuario['tipo']; ?></td>
                            <td>
                                <a href="?delete_user_id=<?php echo $usuario['id']; ?>" class="button" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Apagar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Tabela de Reuniões -->
        <div class="meeting-schedule">
            <h3>Reuniões Agendadas</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Cliente</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reuniao = $reunioesResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $reuniao['id']; ?></td>
                            <td><?php echo $reuniao['nome_cliente']; ?></td>
                            <td><?php echo $reuniao['data_reuniao']; ?></td>
                            <td>
                                <a href="?delete_meeting_id=<?php echo $reuniao['id']; ?>" class="button" onclick="return confirm('Tem certeza que deseja excluir esta reunião?')">Apagar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Tabela de Candidaturas -->
        <div class="job-applications">
            <h3>Candidaturas para Vagas</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Candidato</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($candidatura = $candidaturasResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $candidatura['id']; ?></td>
                            <td><?php echo $candidatura['nome_soldador']; ?></td>
                            <td><?php echo $candidatura['experiencia']; ?></td>
                            <td><?php echo $candidatura['disponibilidade_dia']; ?></td>
                            <td>
                                <?php if ($candidatura['curriculo']) { ?>
                                    <a href="uploads/<?php echo $candidatura['curriculo']; ?>" target="_blank">Ver Currículo</a>
                                <?php } else { ?>
                                    Não enviado
                                <?php } ?>
                            </td>
                            <td>
                                <a href="?delete_application_id=<?php echo $candidatura['id']; ?>" class="button" onclick="return confirm('Tem certeza que deseja excluir esta candidatura?')">Apagar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
      
</body>
</html>
