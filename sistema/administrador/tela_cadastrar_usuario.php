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

    <div>
        <div class="cadastrodeusuario-formulario">
            <input type="text" placeholder="Nome do Usuário">
            <input type="text" placeholder="Matricula"> 
            <input type="text" placeholder="Email"> 
            <input type="password" placeholder="Senha">
            <div class="cadastrousuario-checkbox">
                <span>Administrador</span>
                <input type="checkbox" id="administrador">
                <span>Validador</span>
                <input type="checkbox" id="validador">
                <span>Comitê</span>
                <input type="checkbox" id="comite">
                <span>Pesquisador</span>
                <input type="checkbox" id="pesquisador">
            </div>
            <div class="cadastrodeusuario-buttons">
                <button class="cadastrousuario-cancelbutton">Cancelar</button>
                <button class="cadastrousuario-submitbutton" id="submitusuario">Cadastrar Usuário</button>
            </div>
        </div>
    </div>
</body>
</html>