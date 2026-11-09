<?php

require_once __DIR__ . '/../conexao.php';
$sql = "SELECT id, nome FROM categoria WHERE status = 1 ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova ação</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="paineldecriaracoes-pagina">

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

    <main class="paineldecadastrarusuarios-conteudo">
        <div>
            <h1>TESTE</h1>
        </div>

    </main>
</body>
</html>