<?php
$cadastroSucesso = ($_GET['cadastro'] ?? '') === 'sucesso';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar usuário</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="paineldecadastrarusuarios-pagina">

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">ADMINISTRADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">CADASTRAR USUÁRIO</h2>
    </section>

    <form class="cadastrodeusuario-formulario" method="POST" action="cadastrar.php">
        <input type="text" name="nome" placeholder="Nome do Usuário" maxlength="50" required>
        <input type="number" name="matricula" placeholder="Matrícula" min="1" required>
        <input type="email" name="email" placeholder="Email" maxlength="100" required>
        <input type="password" name="senha" placeholder="Senha (mínimo de 8 caracteres)" minlength="8" required>
        <div class="cadastrousuario-checkbox">
            <div id="cargos-disponiveis">Carregando cargos...</div>
            <p id="erro-cargos" role="alert" hidden></p>
        </div>
        <div class="cadastrodeusuario-buttons">
            <button type="reset" class="cadastrousuario-cancelbutton">Cancelar</button>
            <button type="submit" class="cadastrousuario-submitbutton" id="submitusuario">Cadastrar Usuário</button>
        </div>
    </form>

    <script>
        const formulario = document.querySelector('.cadastrodeusuario-formulario');
        const listaCargos = document.getElementById('cargos-disponiveis');
        const erroCargos = document.getElementById('erro-cargos');

        fetch('cadastrar.php')
            .then((resposta) => {
                if (!resposta.ok) {
                    throw new Error('Não foi possível carregar os cargos.');
                }
                return resposta.json();
            })
            .then((cargos) => {
                listaCargos.replaceChildren();
                cargos.forEach((cargo) => {
                    const opcao = document.createElement('label');
                    const checkbox = document.createElement('input');
                    const nome = document.createElement('span');

                    checkbox.type = 'checkbox';
                    checkbox.name = 'cargos[]';
                    checkbox.value = cargo.id;
                    nome.textContent = cargo.cargo;

                    opcao.append(checkbox, nome);
                    listaCargos.append(opcao);
                });

                if (cargos.length === 0) {
                    erroCargos.hidden = false;
                    erroCargos.textContent = 'Nenhum cargo foi encontrado.';
                }
            })
            .catch(() => {
                listaCargos.textContent = '';
                erroCargos.hidden = false;
                erroCargos.textContent = 'Não foi possível carregar os cargos. Recarregue a página.';
            });

        formulario.addEventListener('submit', (evento) => {
            if (!listaCargos.querySelector('input:checked')) {
                evento.preventDefault();
                erroCargos.hidden = false;
                erroCargos.textContent = 'Selecione pelo menos um cargo.';
            }
        });
    </script>

    <?php include __DIR__ . '/popup_usuario_sucesso.php'; ?>
</body>
</html>