<!-- Log de cadastrar usuarios -->

<?php
// session_start();
// $logs = [];
// $erroLog = null;

// if (!isset($_SESSION["matricula"])) {
//     http_response_code(401);
//     $erroLog = "É necessário estar autenticado para consultar os logs.";
//     return;}

// try {
//     require_once __DIR__ . "/../conexao.php";
//     $sqlLog = "SELECT id, operacao, matricula_usuario, tabela_afetada,DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log ORDER BY data_hora DESC, id DESC";
//     $consultaLog = $pdo->query($sqlLog);
//     $logs = $consultaLog->fetchAll(PDO::FETCH_ASSOC);

// } catch (PDOException $e) {
//     error_log("Erro ao consultar o log do Comitê: " . $e->getMessage());
//     http_response_code(500);
//     $erroLog = "Não foi possível carregar os registros do log.";
// }
?> 

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOG</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body class="paineldecadastrarusuarios-pagina">

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">COMITÊ</h1>
        <button type="button" class="paineldecriaracoes-btn-sair">Sair</button>
    </header>
    <main>
        <div class="painel-minhas-acoes-container-card">
            <div class="painel-minhas-acoes-card-pesquisa">
                <h3 class="card-titulo">Usuario Ativo</h3>
                <p class="card-categoria">Matricula</p>
                <p class="card-data">Data e hora</p>
                <p class="card-descricao">Operacao</p>
                <p class="card-status status-analise">Tabela Afetada</p>
            </div>
            
            <div class="painel-minhas-acoes-card-pesquisa">
                <h3 class="card-titulo">Usuario Ativo</h3>
                <p class="card-categoria">Matricula</p>
                <p class="card-data">Data e hora</p>
                <p class="card-descricao">Operacao</p>
                <p class="card-status status-analise">Tabela Afetada</p>
            </div>

        </div>
    </main>
</body>
</html>

