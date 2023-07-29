<p align="center">
  <img src="https://markt.club/images/logo_marktclub.png" width="200" alt="Logo Markt Club">
</p>

## Como Usar

**Docker:** O projeto foi configurado para funcionar no Docker.Não será necessário exportar o arquivo tabela.sql. No arquivo docker-compose já existe um comando volume fazendo a exportação automaticamente, só irá levar um tempinho.

**Criar usuário:** Na página de login existe um âncora com o nome "criar user" que irá redirecionar para uma página php onde será criado os dados de usuário automaticamente.

**Dados da Conta criada:** O CPF da conta criada é 12345678911 e a senha é 123456789. Essa conta está ativa e possui todas as permissões.

**Funções:** O sistema conta com sistema de login, atualização de dados de um usuário, validação de dados, remoção de um usuário do sistema, listagem de usuários e uma lista de paginação.

**Requisitos:** Para atualizar um usuário, é necessário preencher todos os dados. Como é algo para segurança, foi adicionado um campo de senha antiga. Sendo assim, informe uma nova senha (pode ser a senha antiga) e a senha antiga para serem comparadas. Caso a senha antiga seja diferente da atual (a que está cadastrada no sistema), os dados não serão atualizados. O sistema também valida se o email ou o CPF já é de outro usuário cadastrado.

**Stacks:** Foram utilizadas as seguintes stacks: PHP, MYSQL e JAVASCRIPT.
