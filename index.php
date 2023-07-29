<?php
session_start();

if (empty($_SESSION['ID_USER'])) {
    return header('location: login.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div id="site">
        <header>
            <h1>USUÁRIOS</h1>
            <form class="busca" action="">
                <i><img src="images/lupa.svg"></i>
                <input type="text" name="pesquisa" placeholder="Pesquisar...">
            </form>
            <figure></figure>
            <a class="sair" href="./controller/logout.php">sair</a>
        </header>

        <?php
        include_once "./components/view/messages.php"
        ?>

        <ul id="users">
            <li class="titulo">
                <div class="texto nome">Nome</div>
                <div class="texto cpf">CPF</div>
                <div class="texto email">E-MAIL</div>
                <div class="texto data">DATA</div>
                <div class="texto status">STATUS</div>
                <div class="editar"></div>
                <div class="deletar"></div>
            </li>
            <!-- <li class="dado">
                <div class="texto nome">Nome do usuário</div>
                <div class="texto cpf">000.000.000-00</div>
                <div class="texto email">email@dominio.com.br</div>
                <div class="texto data">10/10/2021</div>
                <div class="texto status">Ativo</div>
                <div class="editar"><a href="form.php"><img src="images/editar.svg"></a></div>
                <div class="deletar"><img src="images/deletar.svg"></div>
            </li> -->
        </ul>
        <div class="pagina">
            <p class="resultado"></p>
            <p class="resultado" id="paginas"></p>
            <a href="#" id="anterior">Anterior</a>
            <a href="#" id="proximo">Próxima</a>
        </div>
        <a href="form.php?type=cadastro" class="botao_add">Adicionar novo</a>
    </div>

    <script>
        let indiceAtual = 0;
        let json = "";
        let resultado = document.querySelector('p.resultado');

        async function exibirUsuarios() {

            let response = await fetch('http://localhost/controller/get_users.php');

            if (!response.ok) {
                alert("HTTP-Error: " + response.status);
            }

            json = await response.json();

            let ul = document.querySelector('ul#users');
            ul.innerHTML = '';

            ul.innerHTML += `<li class="titulo">
                <div class="texto nome">Nome</div>
                <div class="texto cpf">CPF</div>
                <div class="texto email">E-MAIL</div>
                <div class="texto data">DATA</div>
                <div class="texto status">STATUS</div>
                <div class="editar"></div>
                <div class="deletar"></div>
            </li>`

            let totalPaginas = Math.ceil(json.users.length / 5);

            if (json.users == null) {
                ul.innerHTML += `<li class="dado" style="text-align: center"><div class="texto nome">Nenhum usuário encontrado</div></li>`
                resultado.innerHTML = '0 Resultados';
            } else {
                let usuarios = json.users.slice(indiceAtual, indiceAtual + 5)

                resultado.innerHTML = json.users.length + ' Resultados';

                let paginasDiv = document.getElementById('paginas');
                paginasDiv.innerHTML = `Página ${Math.floor(indiceAtual / 5) + 1} de ${totalPaginas}`;

                usuarios.map(user => {
                    ul.innerHTML += `<li class="dado">
          <div class="texto nome">${user.nome}</div>
          <div class="texto cpf">${user.cpf}</div>
          <div class="texto email">${user.email}</div>
          <div class="texto data">${user.data_criacao}</div>
          <div class="texto status">${user.status == 1 ? 'Ativo' : 'Inativo'}</div>
          <div class="editar"><a href="form.php?type=editar&id=${user.id}"><img src="images/editar.svg"></a></div>
          <div class="deletar"><a href="./controller/delete_user.php?id=${user.id}"><img src="images/deletar.svg"></a></div>
          </li>`;
                });
            }
        }

        function usuarioAnterior() {
            if (indiceAtual > 0) {
                indiceAtual -= 5;
                exibirUsuarios();
            }
        }

        function proximoUsuario() {
            if (json.users != null) {
                if (indiceAtual + 5 < json.users.length) {
                    indiceAtual += 5;
                    exibirUsuarios();
                }
            }
        }

        document.getElementById("anterior").addEventListener("click", usuarioAnterior);

        document.getElementById("proximo").addEventListener("click", proximoUsuario);

        exibirUsuarios();
    </script>
</body>

</html>