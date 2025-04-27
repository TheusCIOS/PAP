<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: http://localhost/artmetal/php/login.php');
    exit();
}

// Conexão com o banco de dados
class DatabaseConfig {
    public static function getConnection() {
        $url = "localhost";
        $user = "root";
        $password = "";
        $database = "artmetal";

        $conn = new mysqli($url, $user, $password, $database);

        if ($conn->connect_error) {
            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
        }

        return $conn;
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recebe os dados do formulário
    $nome_soldador = isset($_POST['nome_soldador']) ? htmlspecialchars($_POST['nome_soldador'], ENT_QUOTES, 'UTF-8') : '';
    $experiencia_soldador = isset($_POST['experiencia_soldador']) ? htmlspecialchars($_POST['experiencia_soldador'], ENT_QUOTES, 'UTF-8') : '';
    $disponibilidade_dia = isset($_POST['disponibilidade_dia']) ? htmlspecialchars($_POST['disponibilidade_dia'], ENT_QUOTES, 'UTF-8') : '';

    // Verifica se os campos obrigatórios foram preenchidos
    if (empty($nome_soldador) || empty($experiencia_soldador) || empty($disponibilidade_dia)) {
        die("Erro: Todos os campos obrigatórios devem ser preenchidos.");
    }

    // Processa o arquivo de currículo se enviado
    $curriculo_nome = null;
    if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] === 0) {
        $file_tmp = $_FILES['curriculo']['tmp_name'];
        $file_name = $_FILES['curriculo']['name'];
        $file_size = $_FILES['curriculo']['size'];

        // Verifica a extensão e o tamanho do arquivo
        $allowed_ext = ['pdf', 'docx'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            die("Erro: O arquivo deve ser PDF ou DOCX.");
        }

        if ($file_size > 2 * 1024 * 1024) {
            die("Erro: O arquivo deve ter no máximo 2MB.");
        }

        $upload_dir = 'uploads/curriculos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $curriculo_nome = uniqid() . '.' . $file_ext;
        if (!move_uploaded_file($file_tmp, $upload_dir . $curriculo_nome)) {
            die("Erro ao fazer o upload do arquivo.");
        }
    }

    // Conecta ao banco de dados
    $conn = DatabaseConfig::getConnection();

    // Insere os dados da candidatura no banco de dados
    $query = "INSERT INTO candidaturas (usuario_id, nome_soldador, experiencia, curriculo, disponibilidade_dia) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Erro ao preparar a query: " . $conn->error);
    }

    $stmt->bind_param("issss", $_SESSION['usuario_id'], $nome_soldador, $experiencia_soldador, $curriculo_nome, $disponibilidade_dia);

    if ($stmt->execute()) {
        echo "Candidatura enviada com sucesso!";
        echo '<meta http-equiv="refresh" content="3;url=principal.html">'; // Redireciona após 3 segundos
    } else {
        die("Erro ao executar a query: " . $stmt->error);
    }

    // Fecha a conexão
    $stmt->close();
    $conn->close();
} else {
    die("Erro: Formulário não enviado.");
}
?>
