<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

exigirCargo($pdo, 4);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Pesquisador</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body class="body-painel-pesquisador">

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">PESQUISADOR</h1>
        <a href="../tela_dois_cargos.php" class="cadastrocategoria-btn-sair">Sair</a>
    </header>

    <main class="container-do-painel">

        <div class="painel-dos-botoes">

            <div class="btns-painel-pesquisador">

                <p class="titulo-acoes-pesquisador">
                    AÇÕES
                </p>

                <a
                    href="minhas_acoes.php"
                    class="btns-estilizacao-pesquisador"
                >
                    MINHAS AÇÕES
                </a>

                <a
                    href="painel_criar_acao.php"
                    class="btns-estilizacao-pesquisador"
                >
                    NOVA AÇÃO
                </a>

            </div>

        </div>

    </main>

</body>
</html>
