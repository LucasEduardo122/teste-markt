<?php
session_start();

include_once "../database/database.php";

function accessControlList($conn, $id_user, $type)
{
    try {


        $query_verify_user = $conn->prepare("SELECT * FROM usuario WHERE id = :id");
        $query_verify_user->bindParam(':id', $id_user);
        $query_verify_user->execute();
        $user_logged = $query_verify_user->fetch();

        $permissions = explode("_", $user_logged['permissao']);

        if (!in_array($type, $permissions)) {
            return false;
        }

        return true;
    } catch (\Throwable $th) {
        return false;
    }
}
