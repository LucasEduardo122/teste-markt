<?php
session_start();

if (!empty($_SESSION['ID_USER'])) {
    return header('location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div id="site">
        <figure>
            <img src="images/logo.png" alt="Logo Markt Club">
        </figure>

        <form action="./controller/auth.php" method="post">
            <legend>FAÇA SEU LOGIN</legend>
            <p>Digite seu CPF no campo abaixo e clique em logar para fazer seu login.</p>

            <div class="input">
                <input type="text" id="input_login" placeholder="CPF" inputmode="numeric" name="login">
                <label for="input_login">CPF</label>
            </div>
            <div class="input">
                <input type="password" id="input_senha" placeholder="Senha" inputmode="numeric" name="senha">
                <label for="input_senha">Senha</label>
            </div>

            <button type="submit">LOGAR</button>
            <a style="color:red; margin-top: 15px; font-size: 15px" href="./controller/create_user_auth.php">criar user</a>
            <?php
            include_once "./components/view/messages.php"
            ?>
        </form>
    </div>
</body>

</html>