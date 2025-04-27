<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$nome_banco = "artmetal";

// Conectar ao banco
$conexao = new mysqli($servidor, $usuario, $senha, $nome_banco);

// Verificar conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

$mensagem = "";
$sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"] ?? '';
    $descricao = $_POST["descricao"] ?? '';

    if (empty($nome) || empty($descricao)) {
        $mensagem = "Por favor, preencha todos os campos.";
    } else {
        $stmt = $conexao->prepare("INSERT INTO problemas_reportados (nome, descricao) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $descricao);

        if ($stmt->execute()) {
            $sucesso = true;
            $mensagem = "Problema reportado com sucesso!";
        } else {
           
        }                

        $stmt->close();
    }
}
$conexao->close();
?>