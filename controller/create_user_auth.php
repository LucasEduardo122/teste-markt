<?php
session_start();

try {
    include_once "../database/database.php";
    include_once "../components/createUUID.php";

    $dados['nome'] = 'teste';
    $dados['email'] = 'teste@teste.com';
    $dados['senha'] = password_hash('123456789', PASSWORD_DEFAULT);
    $dados['cpf'] = '12345678911';
    $dados['permissao'] =  ["loginusuario", "addusuario", "editarusuario","deletarusuario"];
    $dados['status'] = 1;

    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Formato de e-mail incorreto'];
        return header('location: ../login.php');
    }

    if (strlen($dados['senha']) < 6) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Senha com menos de 6 digitos'];
        return header('location: ../login.php');
    }

    if (strlen($dados['cpf']) < 11 || strlen($dados['cpf']) > 11) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Cpf com menos ou mais de 11 digitos'];
        return header('location: ../login.php');
    }

    $query_verify = $conn->prepare("SELECT * FROM usuario WHERE email = :email OR cpf = :cpf");
    $query_verify->bindParam(':email', $dados['email']);
    $query_verify->bindParam(':cpf', $dados['cpf']);
    $query_verify->execute();
    $user = $query_verify->fetch();

    if (!empty($user)) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Usuário já cadastrado'];
        return header('location: ../login.php');
    }

    $uuid = generateUUID();

    $query_insert = "INSERT INTO usuario (nome, uuid, cpf, email, senha, permissao, data_criacao, data_atualizacao, status) 
            VALUES (:nome, :uuid, :cpf, :email, :senha, :permissao, NOW(), NOW(), :status)";

    $cad_user = $conn->prepare($query_insert);
    $cad_user->bindParam(':nome', $dados['nome'], PDO::PARAM_STR);
    $cad_user->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $cad_user->bindParam(':cpf', $dados['cpf'], PDO::PARAM_INT);
    $cad_user->bindParam(':email', $dados['email'], PDO::PARAM_STR);
    $cad_user->bindParam(':senha', $dados['senha'], PDO::PARAM_STR);
    $cad_user->bindParam(':permissao', implode('_', $dados['permissao']), PDO::PARAM_STR);
    $cad_user->bindParam(':status', $dados['status'], PDO::PARAM_STR);

    $cad_user->execute();

    if ($cad_user->rowCount()) {
        $_SESSION['MESSAGE'] = ['type' => 'success', 'message' => 'Usuário cadastrado com sucesso'];
        return header('location: ../login.php');
    } else {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Falha ao cadastrar usuário'];
        return header('location: ../login.php');
    }
} catch (\Throwable $th) {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Erro interno no servidor'];
    return header('location: ../login.php');
}
