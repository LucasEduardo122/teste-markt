<?php
session_start();

if (isset($_SESSION['ID_USER']) && empty($_SESSION['ID_USER'])) {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Não autorizado'];
    return header('location: ../login.php');
}

try {
    include_once "../database/database.php";
    include_once "../components/acl.php";

    $id = $_GET['id'];

    if (empty($id)) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Dados faltando'];
        return header('location: ../index.php');
    }

    $id_user = $_SESSION['ID_USER'];


    if (empty($id_user)) {
        return header('location: ../login.php');
    }

    if($id == $id_user) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Você não pode se deletar'];
        return header('location: ../index.php');
    }

    $acl = accessControlList($conn, $id_user, $type = 'deletarusuario');

    if (!$acl) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Ação não permitida'];
        return header('location: ../index.php');
    }

    $query_delete = $conn->prepare("DELETE FROM usuario WHERE id = :id");
    $query_delete->bindParam(':id', $id);

    if ($query_delete->execute()) {
        $_SESSION['MESSAGE'] = ['type' => 'success', 'message' => 'Usuário deletado'];
        return header('location: ../index.php');
    } else {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Ocorreu um erro ao deletar'];
        return header('location: ../index.php');
    }
} catch (\Throwable $th) {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Erro interno no servidor'];
    return header('location: ../index.php');
}
