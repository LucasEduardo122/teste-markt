<?php
session_start();

if (isset($_SESSION['ID_USER']) && empty($_SESSION['ID_USER'])) {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Não autorizado'];
    return header('location: ../login.php');
}

try {
    include_once "../database/database.php";
    include_once "../components/acl.php";

    if (
        empty($_POST['nome']) && empty($_POST['email']) && empty($_POST['senha']) && empty($_POST['cpf'])
        && empty($_POST['permissao']) && empty($_POST['status']) && empty($_POST['id'] && empty($_POST['old_password']))
    ) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Informe todos os campos'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    $dados['nome'] = $_POST['nome'];
    $dados['email'] = $_POST['email'];
    $dados['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $dados['cpf'] = $_POST['cpf'];
    $dados['permissao'] = $_POST['permissao'];
    $dados['status'] = $_POST['status'];
    $dados['old_password'] = $_POST['old_password'];
    $dados['id'] = $_POST['id'];

    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Formato de e-mail incorreto'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    if (strlen($dados['nome']) < 3) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Nome com menos de 3 caracteres'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    if (strlen($dados['senha']) < 6) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Senha com menos de 6 digitos'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    if (strlen($dados['cpf']) < 11 || strlen($dados['cpf']) > 11) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Cpf com menos ou mais de 11 digitos'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    if (empty($dados['permissao'])) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Informe ao menos uma permissão'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    $id_user = $_SESSION['ID_USER'];


    if (empty($id_user)) {
        return header('location: ../login.php');
    }

    $acl = accessControlList($conn, $id_user, $type = 'editarusuario');

    if (!$acl) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Ação não permitida'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    $query_verify_user = $conn->prepare("SELECT * FROM usuario WHERE id = :id");
    $query_verify_user->bindParam(':id', $dados['id']);
    $query_verify_user->execute();
    $user_edit = $query_verify_user->fetch();

    if (empty($user_edit)) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Usuário não encontrado'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    $query_verify_user_retry = $conn->prepare("SELECT * FROM usuario WHERE (email = :email OR cpf = :cpf) AND id <> :id");
    $query_verify_user_retry->bindParam(':email', $dados['email']);
    $query_verify_user_retry->bindParam(':cpf', $dados['cpf']);
    $query_verify_user_retry->bindParam(':id', $dados['id']);
    $query_verify_user_retry->execute();
    $user_retry = $query_verify_user_retry->fetch();

    if (!empty($user_retry)) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Já existe um usuario cadastrado com esses dados'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    }

    $query_update = $conn->prepare("UPDATE usuario SET nome = :nome, email = :email, cpf = :cpf, senha = :senha, permissao = :permissao, status = :status, data_atualizacao = NOW() WHERE id = :id");
    $permissao = implode('_', $dados['permissao']);
    $status = $dados['status'] == 'ativo' ? 1 : 0;

    $permissions = $dados['permissao'];

    if (!empty($dados['senha'])) {
        if (!password_verify($dados['old_password'], $user_edit['senha'])) {
            $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Senha atual difere da antiga'];
            return header('location: ../form.php?type=editar&id=' . $_POST['id']);
        }
    }

    if ($dados['id'] == $id_user) {
        $query_update->bindParam(':nome', $dados['nome'], PDO::PARAM_STR);
        $query_update->bindParam(':id', $dados['id'], PDO::PARAM_INT);
        $query_update->bindParam(':cpf', $dados['cpf'], PDO::PARAM_INT);
        $query_update->bindParam(':email', $dados['email'], PDO::PARAM_STR);
        $query_update->bindParam(':senha', $dados['senha'], PDO::PARAM_STR);
        $query_update->bindParam(':permissao', $permissao, PDO::PARAM_STR);
        $query_update->bindParam(':status', $status, PDO::PARAM_STR);

        $_SESSION['NOME'] = $dados['nome'];
        $_SESSION['EMAIL'] = $dados['email'];
        $_SESSION['CPF'] = $dados['cpf'];
    } else {
        $query_update->bindParam(':nome', $dados['nome'], PDO::PARAM_STR);
        $query_update->bindParam(':id', $dados['id'], PDO::PARAM_INT);
        $query_update->bindParam(':cpf', $dados['cpf'], PDO::PARAM_INT);
        $query_update->bindParam(':email', $dados['email'], PDO::PARAM_STR);
        $query_update->bindParam(':senha', $dados['senha'], PDO::PARAM_STR);
        $query_update->bindParam(':permissao', $permissao, PDO::PARAM_STR);
        $query_update->bindParam(':status', $status, PDO::PARAM_STR);
    }

    if ($query_update->execute()) {
        $_SESSION['MESSAGE'] = ['type' => 'success', 'message' => 'Usuário atualizado'];
        return header('location: ../form.php?type=editar&id=' . $_POST['id']);
    } else {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Ocorreu um erro ao atualizar'];
        return header('location: ../index.php');
    }
} catch (\Throwable $th) {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Erro interno no servidor'];
    return header('location: ../index.php');
}
