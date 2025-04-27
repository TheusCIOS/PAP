<?php
// Arquivo de configuração da conexão com o banco de dados
require_once('../lib/vendor/autoload.php');
require_once('C:\wamp\www\artmetal\lib\vendor\phpmailer\phpmailer\src\PHPMailer.php');
// Verificar a conexão com o banco de dados
$conn = DatabaseConfig::getConnection();
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

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
        
        echo "Conexão bem-sucedida com o banco de dados!";  // Adicione este echo para verificar a conexão
        return $conn;
    }
}

?>

