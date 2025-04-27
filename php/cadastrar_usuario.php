<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/wamp/www/artmetal/lib/vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nome'], $_POST['email'], $_POST['senha'])) {

    $conn = new mysqli('localhost', 'root', '', 'artmetal');

    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Recebe os dados do formulário
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(16));

    // Verifica se o e-mail já está cadastrado
    $checkEmail = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        die("Erro: Este e-mail já está cadastrado.");
    }

    // Insere o novo usuário
    $sql = "INSERT INTO usuarios (nome, email, senha, token, status) VALUES (?, ?, ?, ?, 3)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $nome, $email, $senha, $token);

    if ($stmt->execute()) {
        // Enviar e-mail de ativação
        $link = "http://localhost/artmetal/php/confirmar_cadastro.php?token=$token";
        $subject = "Confirme seu cadastro";
        $htmlBody = "
            <html>
                <body>
                    <p>Olá $nome! Clique no link abaixo para ativar sua conta:</p>
                    <p><a href='$link'>Clique aqui para ativar sua conta</a></p>
                </body>
            </html>
        ";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'bdda325306ea33';
            $mail->Password = 'a55b703444dcf8';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;
            $mail->setFrom('matheusproplayer3@gmail.com', 'Matheus');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;

            $mail->send();
            echo "E-mail enviado com sucesso!";
        } catch (Exception $e) {
            echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
        }
    } else {
        echo "Erro ao cadastrar usuário.";
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Acesso inválido.";
    exit;
}
?>
