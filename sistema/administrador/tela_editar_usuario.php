<?php
$mostrarPopupUsuarioEditado = isset($_GET['sucesso']) && $_GET['sucesso'] === '1';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuário</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="paineldecadastrarusuarios-pagina">
    <?php require_once 'popup_usuario_editado.php'; ?>

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">ADMINISTRADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair" onclick="window.location.href='../tela_dois_cargos.php'">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">EDITAR USUÁRIO</h2>
    </section>

    <p id="erro-edicao" class="editarusuario-mensagem editarusuario-erro" role="alert" hidden></p>

    <p style="display: flex" id="aviso-lista">tem que escolher o usuario la na tela de lista de usuarios meu nobre</p>
    <form class="cadastrodeusuario-formulario" method="POST" action="editar.php" style="display: none;">
        <input type="hidden" name="matricula">
        <input type="text" name="nome" placeholder="Nome do Usuário" maxlength="50" required>
        <input type="number" name="matricula_exibicao" placeholder="Matrícula" aria-label="Matrícula" readonly>
        <input type="email" name="email" placeholder="Email" maxlength="100" required>
        <input type="password" name="senha" placeholder="Nova senha (opcional, mínimo de 8 caracteres)" minlength="8" autocomplete="new-password">
        <div class="cadastrousuario-checkbox">
            <div id="cargos-disponiveis">Carregando dados do usuário...</div>
            <p id="erro-cargos" class="editarusuario-mensagem editarusuario-erro" role="alert" hidden></p>
        </div>
        <div class="cadastrodeusuario-buttons">
            <button type="button" class="cadastrousuario-cancelbutton" id="cancelar-edicao">Cancelar</button>
            <button type="submit" class="cadastrousuario-submitbutton">Salvar alterações</button>
        </div>
    </form>

    <script>
        const aviso = document.getElementById('aviso-lista');
        const formulario = document.querySelector('.cadastrodeusuario-formulario');
        const listaCargos = document.getElementById('cargos-disponiveis');
        const erroCargos = document.getElementById('erro-cargos');
        const erroEdicao = document.getElementById('erro-edicao');
        const parametros = new URLSearchParams(window.location.search);
        const matricula = parametros.get('matricula') || parametros.get('id');

        document.getElementById('cancelar-edicao').addEventListener('click', () => {
            window.location.href = 'tela_lista_usuarios.php';
        });

        if (!matricula) {
            listaCargos.textContent = '';
            erroEdicao.hidden = false;
        } else {
            fetch(`editar.php?matricula=${encodeURIComponent(matricula)}`)
                .then(async (resposta) => {
                    const dados = await resposta.json();
                    if (!resposta.ok) {
                        throw new Error(dados.erro || 'Não foi possível carregar o usuário.');
                    }
                    return dados;
                })
                .then(({ usuario, cargos, cargosSelecionados }) => {
                    formulario.elements.matricula.value = usuario.matricula;
                    formulario.elements.matricula_exibicao.value = usuario.matricula;
                    formulario.elements.nome.value = usuario.nome;
                    formulario.elements.email.value = usuario.email;
                    listaCargos.replaceChildren();

                    cargos.forEach((cargo) => {
                        const opcao = document.createElement('label');
                        const checkbox = document.createElement('input');
                        const nome = document.createElement('span');

                        checkbox.type = 'checkbox';
                        checkbox.name = 'cargos[]';
                        checkbox.value = cargo.id;
                        checkbox.checked = cargosSelecionados.some(
                            (idCargo) => String(idCargo) === String(cargo.id)
                        );
                        nome.textContent = cargo.cargo;

                        opcao.append(checkbox, nome);
                        listaCargos.append(opcao);
                    });

                    formulario.style.display = 'flex';
                    aviso.style.display = 'none';
                    if (cargos.length === 0) {
                        erroCargos.hidden = false;
                        erroCargos.textContent = 'Nenhum cargo foi encontrado.';
                    }
                })
                .catch((erro) => {
                    listaCargos.textContent = '';
                    erroEdicao.hidden = false;
                    erroEdicao.textContent = erro.message;
                });
        }

        formulario.addEventListener('submit', (evento) => {
            if (!listaCargos.querySelector('input:checked')) {
                evento.preventDefault();
                erroCargos.hidden = false;
                erroCargos.textContent = 'Selecione pelo menos um cargo.';
            }
        });
    </script>
</body>
</html>