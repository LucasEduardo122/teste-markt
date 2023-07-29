<?php
session_start();

if (empty($_SESSION['ID_USER'])) {
    return header('location: ../login.php');
}

if (
    !empty($_POST['nome']) || !empty($_POST['email']) || !empty($_POST['senha']) || !empty($_POST['cpf'])
    || !empty($_POST['permissao']) || !empty($_POST['status'])
) {

    try {
        include_once "../database/database.php";
        include_once "../components/createUUID.php";
        include_once "../components/acl.php";

        $dados['nome'] = $_POST['nome'];
        $dados['email'] = $_POST['email'];
        $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $dados['cpf'] = $_POST['cpf'];
        $dados['permissao'] = $_POST['permissao'];
        $dados['status'] = $_POST['status'];

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Formato de e-mail incorreto'];
            return header('location: ../form.php?type=cadastro');
        }

        $id_user = $_SESSION['ID_USER'];


        if (empty($id_user)) {
            return header('location: ../login.php');
        }

        $acl = accessControlList($conn, $id_user, $type = 'addusuario');

        if (!$acl) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Ação não permitida'];
            return header('location: ../form.php?type=cadastro');
        }

        $query_verify = $conn->prepare("SELECT * FROM usuario WHERE email = :email OR cpf = :cpf");
        $query_verify->bindParam(':email', $dados['email']);
        $query_verify->bindParam(':cpf', $dados['cpf']);
        $query_verify->execute();
        $user = $query_verify->fetch();

        if (!empty($user)) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Usuário já cadastrado'];
            return header('location: ../form.php?type=cadastro');
        }

        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Formato de e-mail incorreto'];
            return header('location: ../form.php?type=cadastro');
        }

        if (strlen($dados['nome']) < 3) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Nome com menos de 3 caracteres'];
            return header('location: ../form.php?type=cadastro');
        }

        if (strlen($dados['senha']) < 6) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Senha com menos de 6 digitos'];
            return header('location: ../form.php?type=cadastro');
        }

        if (strlen($dados['cpf']) < 11 || strlen($dados['cpf']) > 11) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Cpf com menos ou mais de 11 digitos'];
            return header('location: ../form.php?type=cadastro');
        }

        if (empty($dados['permissao'])) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Informe ao menos uma permissão'];
            return header('location: ../form.php?type=cadastro');
        }

        $query_insert = "INSERT INTO usuario (nome, uuid, cpf, email, senha, permissao, data_criacao, data_atualizacao, status) 
                VALUES (:nome, :uuid, :cpf, :email, :senha, :permissao, NOW(), NOW(), :status)";

        $uuid = generateUUID();
        $permissao = implode('_', $dados['permissao']);
        $status = $dados['status'] == 'ativo' ? 1 : 0;

        $cad_user = $conn->prepare($query_insert);
        $cad_user->bindParam(':nome', $dados['nome'], PDO::PARAM_STR);
        $cad_user->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $cad_user->bindParam(':cpf', $dados['cpf'], PDO::PARAM_INT);
        $cad_user->bindParam(':email', $dados['email'], PDO::PARAM_STR);
        $cad_user->bindParam(':senha', $dados['senha'], PDO::PARAM_STR);
        $cad_user->bindParam(':permissao', $permissao, PDO::PARAM_STR);
        $cad_user->bindParam(':status', $status, PDO::PARAM_STR);

        $cad_user->execute();

        if ($cad_user->rowCount()) {
            $_SESSION['MESSAGE'] = ['type' => 'success', 'message' => 'Usuário cadastrado com sucesso'];
            return header('location: ../form.php?type=cadastro');
        } else {
            $_SESSION['MESSAGE'] = ['type' => 'success', 'message' => 'Falha ao cadastrar usuário'];
            return header('location: ../form.php?type=cadastro');
        }
    } catch (\Throwable $th) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Erro interno no servidor'];
        return header('location: ../form.php?type=cadastro');
    }
} else {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Dados faltando'];
    return header('location: ../form.php?type=cadastro');
}
