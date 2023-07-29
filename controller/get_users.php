<?php

session_start();

if (isset($_SESSION['ID_USER']) && empty($_SESSION['ID_USER'])) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
}


try {
    include_once "../database/database.php";

    $query_usuarios = "SELECT * FROM usuario ORDER BY id DESC";
    $result_usuarios = $conn->prepare($query_usuarios);
    $result_usuarios->execute();

    $users_data = $result_usuarios->fetchAll(PDO::FETCH_ASSOC);

    if (!$users_data) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);

        echo json_encode(['users' => NULL]);
        return;
    }

    header('Content-Type: application/json; charset=utf-8');
    http_response_code(200);

    $users = array('users' => array());

    foreach ($users_data as $usuario) {
        // Adicionar os dados do usuário ao array $users
        $users['users'][] = $usuario;
    }
    echo json_encode($users);
} catch (\Throwable $th) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno no servidor']);
}
