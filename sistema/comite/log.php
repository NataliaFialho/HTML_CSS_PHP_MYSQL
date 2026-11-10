<!-- Log de cadastrar usuarios -->

<?php
session_start();
$logs = [];
$erroLog = null;

if (!isset($_SESSION["matricula"])) {
    http_response_code(401);
    $erroLog = "É necessário estar autenticado para consultar os logs.";
    return;}

try {
    require_once __DIR__ . "/../conexao.php";
    $sqlLog = "SELECT id, operacao, matricula_usuario, tabela_afetada,DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log ORDER BY data_hora DESC, id DESC";
    $consultaLog = $pdo->query($sqlLog);
    $logs = $consultaLog->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log do Comitê: " . $e->getMessage());
    http_response_code(500);
    $erroLog = "Não foi possível carregar os registros do log.";
}

// Log de cadastrar usuarios
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
            <!-- Exemplo de Card 1 (Status: Em Análise) -->
            <div class="painel-minhas-acoes-card-pesquisa">
                <h3 class="card-titulo">Ação de Reflorestamento Urbano</h3>
                <p class="card-categoria">Meio Ambiente</p>
                <p class="card-data">15/10/2026</p>
                <p class="card-descricao">Plantio de mudas nativas na praça central do bairro para aumento da área verde urbana.</p>
                <p class="card-status status-analise">Em Análise</p>
            </div>

            <!-- Exemplo de Card 2 (Status: Aprovado) -->
            <div class="painel-minhas-acoes-card-pesquisa">
                <h3 class="card-titulo">Arrecadação de Alimentos</h3>
                <p class="card-categoria">Social</p>
                <p class="card-data">22/10/2026</p>
                <p class="card-descricao">Campanha de natal para arrecadar alimentos não perecíveis para comunidades vulneráveis.</p>
                <p class="card-status status-aprovado">Aprovado</p>
            </div>
        </div>
    </main>
</body>
</html>

