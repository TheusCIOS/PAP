<?php
// Defina as variáveis para conexão com o banco de dados
$servername = "localhost";  // O servidor de banco de dados
$username = "root";         // O usuário do banco de dados
$password = "";             // A senha (se não tiver senha, deixe vazia)
$database = "artmetal";  // Nome do banco de dados

// Crie a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $database);

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Agora, se a conexão for bem-sucedida, podemos prosseguir com o restante do código

// Exemplo de como tratar o envio de dados do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data_reuniao = $_POST['data_reuniao'];
    $hora_reuniao = $_POST['hora_reuniao'];
    $motivo_reuniao = $_POST['motivo_reuniao'];
    $motivo_outro = isset($_POST['motivo_outro']) ? $_POST['motivo_outro'] : '';

    // Consulta SQL para inserir os dados na tabela 'reunioes'
    $query = "INSERT INTO reunioes (data_reuniao, hora_reuniao, motivo_reuniao, motivo_outro) 
              VALUES ('$data_reuniao', '$hora_reuniao', '$motivo_reuniao', '$motivo_outro')";

    // Executa a consulta
    if ($conn->query($query) === TRUE) {
        // Se a reunião foi agendada com sucesso, redireciona para a página principal após 5 segundos
        echo "<p>Receberas um email para confirmar a data,hora e local da reuniao </p>";
        echo "<p>Para a pagina principal em 5 segundos </p>";
        header("Refresh: 5; url=principal.html"); // Redireciona para a página principal após 5 segundos
        exit(); // Impede a execução do código a seguir após o redirecionamento
    } else {
        echo "Erro ao agendar reunião: " . $conn->error;
    }
}

// Feche a conexão ao final
$conn->close();
?>
