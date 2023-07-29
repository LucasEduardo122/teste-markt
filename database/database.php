<?php
// $host = "localhost";
// $user = "root";
// $pass = "";
// $db_name = "teste-backend";
// $port = 3306;

$host = "mariadb";
$user = "root";
$pass = "123456";
$db_name = "teste";
$port = 3306;
try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=" . $db_name, $user, $pass);
} catch (PDOException $th) {
    echo "Erro: Conexão com o banco de dados não realizada. Erro gerado " . $th->getMessage();
}

