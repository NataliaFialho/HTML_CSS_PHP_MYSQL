<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

exigirCargo($pdo, 1);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador - Embrapa</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
<header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">ADMINISTRADOR</h1>
        <a href="../tela_dois_cargos.php" class="cadastrocategoria-btn-sair">Sair</a>
    </header>

    <main class="conteudo-principal">

    <section class="card-painel">
        <h2 class="card-titulo">USUÁRIOS</h2>
        <div class="card-botoes">

            <a href="tela_cadastrar_usuario.php" class="botao">ADICIONAR</a>
            <a href="tela_lista_usuarios.php" class="botao">LISTAR</a>
        </div>
    </section>

    <section class="card-painel">
        <h2 class="card-titulo">AÇÕES</h2>
        <div class="card-botoes">
            <a href="cadastrar_categoria.php" class="botao">ADICIONAR</a>
            <a href="gerenciar_categorias.php" class="botao">LISTAR</a>

        </div>
    </section>
</main>
</body>
</html>