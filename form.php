<?php
session_start();

if (empty($_SESSION['ID_USER'])) {
    return header('location: login.php');
}

$type = $_GET['type'];

if ($type != 'cadastro' && $type != 'editar' || empty($type)) {
    $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Página não encontrada'];
    return header('location: index.php');
}

if ($type == 'editar') {
    include_once "./database/database.php";

    if (empty($_GET['id'])) {
        return header('location: index.php');
    }

    $id = $_GET['id'];

    $query_verify_user = $conn->prepare("SELECT * FROM usuario WHERE id = :id");
    $query_verify_user->bindParam(':id', $id);
    $query_verify_user->execute();
    $user = $query_verify_user->fetch();

    if (empty($user)) {
        $_SESSION['MESSAGE'] = ['type' => 'error', 'message' => 'Usuário não encontrado'];
        return header('location: index.php');
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php ($type == 'cadastro' ? 'Cadastrar Usuário' : 'Editar Usuário') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <div id="site">
        <header>
            <a class="voltar" href="index.php"><img src="images/voltar.svg"></a>
            <h1 class="total"><?php echo ($type == 'cadastro' ? 'Salvar novo usuário' : 'Editar usuário salvo') ?></h1>
            <figure></figure>
            <a class="sair" href="./controller/logout.php">sair</a>
        </header>
        <?php
           include_once "./components/view/messages.php"
        ?>
        <form action="<?php echo ($type == 'cadastro' ? './controller/insert_user.php' : './controller/update_user.php') ?>" method="POST" class="cadastro">
            <div class="input">
                <label for="input_nome">Nome:</label>
                <input type="text" id="input_nome" name="nome" value="<?php echo ($type == "editar" ? $user['nome'] : '') ?>" placeholder="Digite um nome">
            </div>
            <div class="input">
                <label for="input_cpf">CPF:</label>
                <input type="text" id="input_cpf" name="cpf" value="<?php echo ($type == "editar" ? $user['cpf'] : '') ?>" placeholder="Digite um CPF">
            </div>
            <div class="input">
                <label for="input_email">E-mail:</label>
                <input type="text" id="input_email" name="email" value="<?php echo ($type == "editar" ? $user['email'] : '') ?>" placeholder="Digite um e-mail">
            </div>
            <div class="input">
                <label for="input_senha">Senha:</label>
                <input type="password" id="input_senha" name="senha" placeholder="Digite a nova senha">
            </div>

            <?php
            if ($type == 'editar') {

            ?>
                <div class="input">
                    <label for="input_senha_old">Senha antiga:</label>
                    <input type="password" id="input_senha_old" name="old_password" placeholder="Digite a senha antiga">
                </div>

                <input type="hidden" name="id" value="<?php echo $id ?>">
            <?php } ?>

            <div class="select">
                <label for="input_status">Status</label>
                <select name="status" id="input_status">

                    <option value="">Escolha uma opção</option>
                    <option value="ativo" <?php echo ($type == "editar" && $user['status'] == 1 ? 'selected' : '') ?>>Ativo</option>
                    <option value="inativo" <?php echo ($type == "editar" && $user['status'] == 0 ? 'selected' : '') ?>>Inativo</option>
                </select>
                <div class="seta"><img src="images/seta.svg" alt=""></div>
            </div>

            <h2>Permissão</h2>

            <?php
            if(isset($user)) {
                $permissions = explode("_", $user['permissao']);
            }
            ?>
            <div class="permissao">
                <div class="checkbox">
                    <input type="checkbox" id="input_permissao_login" <?php echo (isset($user) && $type == "editar" && in_array('loginusuario', $permissions) ? 'checked' : '') ?> name="permissao[]" value="loginusuario">
                    <div class="check"><img src="images/check.svg"></div>
                    <label for="input_permissao_login">Login</label>
                </div>
                <div class="checkbox">
                    <input type="checkbox" id="input_permissao_usuario_add" <?php echo (isset($user) && $type == "editar" && in_array('addusuario', $permissions) ? 'checked' : '') ?> name="permissao[]" value="addusuario">
                    <div class="check"><img src="images/check.svg"></div>
                    <label for="input_permissao_usuario_add">Add usuário</label>
                </div>
                <div class="checkbox">
                    <input type="checkbox" id="input_permissao_usuario_editar" <?php echo (isset($user) && $type == "editar" && in_array('editarusuario', $permissions) ? 'checked' : '') ?> name="permissao[]" value="editarusuario">
                    <div class="check"><img src="images/check.svg"></div>
                    <label for="input_permissao_usuario_editar">Editar usuário</label>
                </div>
                <div class="checkbox">
                    <input type="checkbox" id="input_permissao_usuario_deletar" <?php echo (isset($user) && $type == "editar" && in_array('deletarusuario', $permissions) ? 'checked' : '') ?> name="permissao[]" value="deletarusuario">
                    <div class="check"><img src="images/check.svg"></div>
                    <label for="input_permissao_usuario_deletar">Deletar usuário</label>
                </div>
            </div>

            <button type="submit">SALVAR</button>
        </form>
    </div>
</body>

</html>