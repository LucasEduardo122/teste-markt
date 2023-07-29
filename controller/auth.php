<?php
session_start();

try {
    include_once "../database/database.php";
    include_once "../components/acl.php";

    $cpf = $_POST['login'];
    $password = $_POST['senha'];

    $query_auth = $conn->prepare("SELECT * FROM usuario WHERE cpf = :cpf");
    $query_auth->bindParam(':cpf', $cpf);
    $query_auth->execute();
    $user = $query_auth->fetch();


    if ($user && password_verify($password, $user['senha'])) {

        if ($user['status'] == 0) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Conta inativa'];
            return header('location: ../login.php');
        }

        $permissaoLogin = accessControlList($conn, $user['id'], 'loginusuario');

        if(!$permissaoLogin) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Login desativado'];
            return header('location: ../login.php');
        }

        $_SESSION['NOME'] = $user['nome'];
        $_SESSION['EMAIL'] = $user['email'];
        $_SESSION['UUID'] = $user['uuid'];
        $_SESSION['CPF'] = $user['CPF'];
        $_SESSION['ID_USER'] = $user['id'];
        header('location: ../index.php');
        return;
    } else {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Usuário ou senha incorretos'];
        return header('location: ../login.php');
    }
} catch (\Throwable $th) {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Erro interno no servidor'];
    return header('location: ../login.php');
}
